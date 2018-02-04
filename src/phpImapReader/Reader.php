<?php

namespace benhall14\phpImapReader;

use Exception;

/**
 * IMAP Reader Class.
 *
 * This class is used to fetch emails based on the parameter options set. You can set useful filtering flags such as ALL, ANSWERED, DELETED, FLAGGED, NEW, OLD, RECENT, SEEN, UNANSWERED, UNDELETED, UNGLAGGED, UNKEYWORD, UNSEEN. You can match emails by BCC, BEFORE DATE, CC, FROM, KEYWORD, ON DATE, SINCE DATE, TO. You can also search for text strings in email TEXT, SUBJECT and BODY.
 *
 * @copyright  Copyright (c) Benjamin Hall
 * @license https://github.com/benhall14/php-imap-reader
 * @package protocols
 * @author Benjamin Hall <https://linkedin.com/in/benhall14>
*/
class Reader
{
    /**
     * The IMAP host name.
     * @var string
     */
    private $hostname;

    /**
     * The IMAP user name.
     * @var string
     */
    private $user_name;

    /**
     * The IMAP password.
     * @var string
     */
    private $password;

    /**
     * Save attachment status - should we save attachments.
     * @var boolean
     */
    private $save_attachments;

    /**
     * Retry status
     * @var boolean
     */
    private $retry;

    /**
     * The IMAP handler.
     * @var resource
     */
    private $imap;

    /**
     * The index of email ids.
     * @var array
     */
    private $email_index;
    
    /**
     * The array of previously fetched emails
     * @var array
     */
    private $emails = array();
    
    /**
     * The number of emails found.
     * @var int
     */
    private $email_count;

    /**
     * Modes - such as NEW or UNSEEN.
     * @var string
     */
    private $modes = array();
    
    /**
     * The id of an specific id.
     * @var int
     */
    private $id = 0;
    
    /**
     * Limit the number of emails fetch. Can be used with page for pagination.
     * @var int
     */
    private $limit = 0;
    
    /**
     * Page number. Can be used with limit for pagination.
     * @var int
     */
    private $page = 0;
    
    /**
     * The email offset.
     * @var int
     */
    private $offset = 0;
    
    /**
     * The sorting order direction.
     * @var string
     */
    private $order = 'DESC';

    /**
     * The mailbox name.
     * @var string
     */
    private $mailbox = 'INBOX';

    /**
     * Sets the IMAP Reader
     * @param string  $hostname       The IMAP host name.
     * @param string  $user_name      The IMAP user name.
     * @param string  $password       The IMAP password.
     * @param string  $attachment_dir The directory path to store attachments or false to turn off saving attachments.
     * @param boolean $mark_as_read   Whether we should mark fetched emails as read.
     */
    public function __construct($hostname, $user_name, $password, $attachment_dir = false, $mark_as_read = true)
    {
        $this->hostname = $hostname;
        
        $this->user_name = $user_name;
        
        $this->password = $password;
        
        $this->encoding = 'UTF-8';

        $this->retry = 0;

        $this->mark_as_read = $mark_as_read;

        $this->save_attachments = false;

        if ($attachment_dir) {
            $attachment_dir = rtrim($attachment_dir, DIRECTORY_SEPARATOR);

            if (!is_dir($attachment_dir)) {
                throw new Exception('ERROR: Directory "' . $attachment_dir . '" could not be found.');
            }

            if (!is_writable($attachment_dir)) {
                throw new Exception('ERROR: Directory "' . $attachment_dir . '" is not writable.');
            }

            $this->save_attachments = true;
            $this->attachment_dir = $attachment_dir;
        }

        return true;
    }

    /**
     * Fetch the active IMAP stream. If no stream is active, try a connection.
     * @param  boolean $reconnect Whether to reconnect connection.
     * @return resource                  The IMAP stream.
     */
    private function stream($reconnect = true)
    {
        if ($this->imap && (!is_resource($this->imap) || !imap_ping($this->imap))) {
            $this->close();
                
            $this->imap = false;
        }

        if (!$this->imap && $reconnect) {
            $this->imap = $this->connect();
        }
            
        return $this->imap;
    }

    /**
     * Connect to an IMAP stream.
     * @return resource The opened IMAP stream.
     */
    private function connect()
    {
        $stream = imap_open($this->hostname . $this->mailbox, $this->user_name, $this->password, false, $this->retry);

        if (!$stream) {
            $last_error = imap_last_error();

            var_dump(imap_errors());
            imap_errors();

            throw new Exception('ERROR: Could Not Connect (' . (!empty($lastError) ? $lastError : 'unknown') . ')');
        }

        return $stream;
    }

    /**
     * Close the current IMAP stream.
     */
    private function close()
    {
        if ($this->stream(false) && is_resource($this->stream(false))) {
            imap_close($this->stream(false), CL_EXPUNGE);
        }
    }

    /**
     * Close connection on destruct.
     */
    public function __destruct()
    {
        return $this->close();
    }

    /**
     * Get the last error.
     * @return string The error message.
     */
    public function getError()
    {
        return imap_last_error();
    }

    /**
     * Delete an email by given email id.
     * @param  int $email_id The id of the email to delete.
     * @return boolean       The result of the action.
     */
    public function deleteEmail($email_id)
    {
        return imap_delete($this->stream(), $email_id, FT_UID);
    }

    /**
     * Mark an email as read by given email id.
     * @param  int $email_id The id of the email to mark as read.
     * @return boolean       The result of the action.
     */
    public function markAsRead($email_id)
    {
        return imap_setflag_full($this->stream(), $email_id, '\\Seen', ST_UID);
    }

    /**
     * Get the list of emails from the last get call.
     * @return array An array of returned emails.
     */
    public function emails()
    {
        return $this->emails;
    }

    /**
     * Get the first email from the list of returned emails. This is a shortcut for $this->emails[0];
     * @return Email The email.
     */
    public function email()
    {
        return $this->emails[0];
    }

    /**
     * Get the email based on the given id.
     * @param  int $id The Email Id.
     * @return object Return this instance for chain-ability.
     */
    public function id($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Add 'all' to the mode selection. This allows for matching 'all' emails.
     * @return object Return this instance for chain-ability.
     */
    public function all()
    {
        $this->modes[] = 'ALL';

        return $this;
    }

    /**
     * Add 'flagged' to the mode selection. This allows for matching 'flagged' emails.
     * @return object Return this instance for chain-ability.
     */
    public function flagged()
    {
        $this->modes[] = 'FLAGGED';

        return $this;
    }

    /**
     * Add 'unanswered' to the mode selection. This allows for matching 'unanswered' emails.
     * @return object Return this instance for chain-ability.
     */
    public function unanswered()
    {
        $this->modes[] = 'UNANSWERED';

        return $this;
    }

    /**
     * Add 'deleted' to the mode selection. This allows for matching 'deleted' emails.
     * @return object Return this instance for chain-ability.
     */
    public function deleted()
    {
        $this->modes[] = 'DELETED';

        return $this;
    }

    /**
     * Add 'unseen' to the mode selection. This allows for matching 'unseen' emails.
     * @return object Return this instance for chain-ability.
     */
    public function unseen()
    {
        $this->modes[] = 'UNSEEN';

        return $this;
    }

    /**
     * An alias of unseen. See unseen().
     *
     * @return void
     */
    public function unread()
    {
        return $this->unseen();
    }

    /**
     * Add 'from' to the mode selection. This allows for matching emails 'from' the given email address.
     * @param  string $from The sender email address.
     * @return object Return this instance for chain-ability.
     */
    public function from($from)
    {
        $this->modes[] = 'FROM "' . $from . '"';

        return $this;
    }

    /**
     * Add the body search to the mode selection. This allows for searching for a string within a body
     * @param  [type] $string [description]
     * @return object Return this instance for chain-ability.
     */
    public function searchBody($string)
    {
        if ($string) {
            $this->modes[] = 'BODY "' . $string . '"';
        }

        return $this;
    }

    /**
     * Add the subject search to the mode selection. This allows for searching for a string within a subject line.
     * @param  string $string The string to search for.
     * @return object Return this instance for chain-ability.
     */
    public function searchSubject($string)
    {
        if ($string) {
            $this->modes[] = 'SUBJECT "' . $string . '"';
        }

        return $this;
    }
    
    /**
     * Add the recent flag to the mode selection. This allows for matching recent emails.
     * @return object Return this instance for chain-ability.
     */
    public function recent()
    {
        $this->modes[] = 'RECENT';

        return $this;
    }

    /**
     * Add the unflagged flag to the mode selection. This allows for matching unflagged emails.
     * @return object Return this instance for chain-ability.
     */
    public function unflagged()
    {
        $this->modes[] = 'UNFLAGGED';

        return $this;
    }

    /**
     * Add the seen flag to the mode selection. This allows for matching seen emails.
     * @return object Return this instance for chain-ability.
     */
    public function seen()
    {
        $this->modes[] = 'SEEN';

        return $this;
    }

    /**
     * Alias of seen(). See Seen().
     *
     * @return void
     */
    public function read()
    {
        $this->seen();
    }

    /**
     * Add the new flag to the mode selection. This allows for matching new emails.
     * @return object Return this instance for chain-ability.
     */
    public function newMessages()
    {
        $this->modes[] = 'NEW';

        return $this;
    }

    /**
     * Add the old flag to the mode selection. This allows for matching old emails.
     * @return object Return this instance for chain-ability.
     */
    public function oldMessages()
    {
        $this->modes[] = 'OLD';

        return $this;
    }

    /**
     * Add the keyword flag to the mode selection. This allows for matching emails with the given keyword.
     * @param  string $keyword The keyword to search for.
     * @return object Return this instance for chain-ability.
     */
    public function keyword($keyword)
    {
        $this->modes[] = 'KEYWORD "' . $keyword . '"';

        return $this;
    }

    /**
     * Add the unkeyword flag to the mode selection. This allows for matching emails without the given keyword.
     * @param  string $keyword The keyword to avoid.
     * @return object Return this instance for chain-ability.
     */
    public function unkeyword($keyword)
    {
        $this->modes[] = 'UNKEYWORD "' . $keyword . '"';

        return $this;
    }

    /**
     * Add the before date flag to the mode selection. This allows for matching emails received before the given date.
     * @param  string $date The date to match.
     * @return object Return this instance for chain-ability.
     */
    public function beforeDate($date)
    {
        $date = date('d-M-Y', strtotime($date));
        
        $this->modes[] = 'BEFORE "' . $date . '"';

        return $this;
    }

    /**
     * Add the since date flag to the mode selection. This allows for matching emails received since the given date.
     * @param  string $date The date to match.
     * @return object Return this instance for chain-ability.
     */
    public function sinceDate($date)
    {
        $date = date('d-M-Y', strtotime($date));

        $this->modes[] = 'SINCE "' . $date . '"';

        return $this;
    }

    /**
     * Add the sent to flag to the mode selection. This allows for matching emails sent to the given email address string.
     * @param  string $to The email address to match.
     * @return object Return this instance for chain-ability.
     */
    public function sentTo($to)
    {
        $this->modes[] = 'TO "' . $to . '"';

        return $this;
    }

    /**
     * Add the BCC flag to the mode selection. This allows for matching emails with the string present in the BCC field.
     * @param  string $to The email address to match.
     * @return object Return this instance for chain-ability.
     */
    public function searchBCC($string)
    {
        $this->modes[] = 'BCC "' . $string . '"';

        return $this;
    }

    /**
     * Add the CC flag to the mode selection. This allows for matching emails with the string present in the CC field.
     * @param  string $to The email address to match.
     * @return object Return this instance for chain-ability.
     */
    public function searchCC($string)
    {
        $this->modes[] = 'CC "' . $string . '"';

        return $this;
    }

    /**
     * Add the on date flag to the mode selection. This allows for matching emails received on the given date.
     * @param  string $date The date to match.
     * @return object Return this instance for chain-ability.
     */
    public function onDate($date)
    {
        $date = date('d-M-Y', strtotime($date));

        $this->modes[] = 'ON "' . $date . '"';

        return $this;
    }

    /**
     * Add the text flag to the mode selection. This allows for matching emails with the given text string.
     * @param  string $string The string to search for.
     * @return object Return this instance for chain-ability.
     */
    public function searchText($string)
    {
        $this->modes[] = 'TEXT "' . $string . '"';

        return $this;
    }

    /**
     * Set the limit. This can be used with page() for pagination of emails.
     * @param  int $limit The total number of emails to return.
     * @return object Return this instance for chain-ability.
     */
    public function limit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * Set the page number. This is used with limit() for pagination of emails.
     * @param  int $page The page number.
     * @return object Return this instance for chain-ability.
     */
    public function page($page)
    {
        $this->page = $page;

        $this->offset = ($page - 1) * $this->limit;

        return $this;
    }

    /**
     * Set the email fetching order to ASCending.
     * @return object Return this instance for chain-ability.
     */
    public function orderASC()
    {
        $this->order = 'ASC';

        return $this;
    }

    /**
     * Set the email fetching order to DESCending.
     * @return object Return this instance for chain-ability.
     */
    public function orderDESC()
    {
        $this->order = 'DESC';

        return $this;
    }
    
    /**
     * Sets the folder to retrieve emails from. Alias for mailbox().
     * @param  string $folder The name of the folder. IE. INBOX.
     * @return object Return this instance for chain-ability.
     */
    public function folder($folder)
    {
        return $this->mailbox($folder);
    }

    /**
     * Sets the mailbox to retrieve emails from.
     * @param  string $mailbox The name of the mailbox, IE. INBOX.
     * @return object Return this instance for chain-ability.
     */
    public function mailbox($mailbox)
    {
        $this->mailbox = $mailbox;

        return $this;
    }

    /**
     * Get a formatted list of selected modes for imap_search.
     * @return string The mode list formatted into a string.
     */
    private function modes()
    {
        if (!$this->modes) {
            $this->modes[] = 'ALL';
        }

        return implode(' ', $this->modes);
    }

    /**
     * Fetch emails based on the previously set parameters.
     * @return array An array of emails.
     */
    public function get()
    {
        if (!$this->connect()) {
            throw new Exception('ERROR: Could not connect.');
        }

        if ($this->id) {
            $this->emails = array();

            $this->emails[] = $this->getEmail($this->id);

            return $this->emails;
        }

        $this->emails = array();

        $this->email_index = imap_search($this->stream(), $this->modes(), false, $this->encoding);

        $this->email_count = imap_num_msg($this->stream());

        if (!$this->limit) {
            $this->limit = count($this->email_index);
        }

        if ($this->email_index) {
            if ($this->order == 'DESC') {
                rsort($this->email_index);
            } else {
                sort($this->email_index);
            }

            if ($this->limit || ($this->limit && $this->offset)) {
                $this->email_index = array_slice($this->email_index, $this->offset, $this->limit);
            }

            $this->emails = array();

            foreach ($this->email_index as $id) {
                $this->emails[] = $this->getEmail($id);
                
                if ($this->mark_as_read) {
                    $this->markAsRead($id);
                }
            }
        }

        return $this->emails;
    }

    /**
     * Fetch an email by id.
     * @param  int $id The id of the email to fetch.
     * @return Email     The email object for given Id.
     */
    private function getEmail($id)
    {
        $email = new Email();
        
        $header = imap_headerinfo($this->stream(), $id);

        $email->setid(imap_uid($this->stream(), $id));
        $email->setSize($header->Size);
        $header->subject = isset($header->subject) ? $this->decodeMimeHeader($header->subject) : false;
        $email->setSubject($header->subject);
        $email->setDate($header->date);
        $email->setUdate($header->udate);

        if (isset($header->to)) {
            foreach ($header->to as $to) {
                $to_name = isset($to->personal) ? $this->decodeMimeHeader($to->personal) : false;
                $email->addTo($to->mailbox, $to->host, $to_name);
            }
        }

        if (isset($header->from)) {
            $from_name = isset($header->from[0]->personal) ? $this->decodeMimeHeader($header->from[0]->personal) : false;
            $email->setFrom($header->from[0]->mailbox, $header->from[0]->host, $from_name);
        }

        if (isset($header->reply_to)) {
            foreach ($header->reply_to as $reply_to) {
                $reply_to_name = isset($reply_to->personal) ? $this->decodeMimeHeader($reply_to->personal) : false;
                $email->addReplyTo($reply_to->mailbox, $reply_to->host, $reply_to_name);
            }
        }

        if (isset($header->cc)) {
            foreach ($header->cc as $cc) {
                $cc_name = isset($cc->personal) ? $this->decodeMimeHeader($cc->personal) : false;
                $email->addCC($cc->mailbox, $cc->host, $cc_name);
            }
        }

        $recent = ($header->Recent == 'R' || $header->Recent == 'N') ? true : false;
        $email->setRecent($recent);

        $unseen = ($header->Unseen == 'U') ? true : false;
        $email->setUnseen($unseen);
           
        $flagged = ($header->Flagged == 'F') ? true : false;
        $email->setFlagged($flagged);
           
        $answered = ($header->Answered == 'A') ? true : false;
        $email->setAnswered($answered);
           
        $deleted = ($header->Deleted == 'D') ? true : false;
        $email->setDeleted($deleted);
           
        $draft = ($header->Draft == 'X') ? true : false;
        $email->setDraft($draft);

        $body = imap_fetchstructure($this->stream(), $id);
     
        if (isset($body->parts) && count($body->parts)) {
            foreach ($body->parts as $part_number => $part) {
                $this->decodePart($email, $part, $part_number + 1);
            }
        } else {
            $this->decodePart($email, $body);
        }

        return $email;
    }

    /**
     * Decode an email part.
     * @param  Email   $email       The email object to update.
     * @param  string  $part        The part data to decode.
     * @param  boolean $part_number The part number.
     * @return string               The decoded part data.
     */
    private function decodePart(Email $email, $part, $part_number = false)
    {
        $options = ($this->mark_as_read) ? FT_UID : FT_UID | FT_PEEK;

        if ($part_number) {
            $data = imap_fetchbody($this->stream(), $email->id(), $part_number, $options);
        } else {
            $data = imap_body($this->stream(), $email->id(), $options);
        }

        switch ($part->encoding) {
            case 1:
                $data = imap_utf8($data);
                break;

            case 2:
                $data = imap_binary($data);
                break;

            case 3:
                $data = imap_base64($data);
                break;

            case 4:
                $data = quoted_printable_decode($data);
                break;
        }

        $params = array();
        if (isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                $params[ strtolower($param->attribute) ] = $param->value;
            }
        }

        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                $params[ strtolower($param->attribute) ] = $param->value;
            }
        }

        # is this part an attachment
        $attachment_id = false;
        $is_attachment = false;

        if (isset($part->disposition) && in_array(strtolower($part->disposition), array('attachment', 'inline' )) && $part->subtype != 'PLAIN') {
            $is_attachment = true;
            $attachment_type = strtolower($part->disposition);

            if ($attachment_type == 'inline') {
                $is_inline_attachment = true;
                $attachment_id = isset($part->id) ? trim($part->id, " <>") : false;
            } else {
                $is_inline_attachment = false;
                $attachment_id = $email->id();
            }
        }

        # if there is an attachment
        if ($is_attachment) {
            $file_name = false;
            
            if (isset($params['filename'])) {
                $file_name = $params['filename'];
            } elseif (isset($params['name'])) {
                $file_name = $params['name'];
            }

            if ($file_name) {
                $file_name = $attachment_id . '-' . $file_name;

                $attachment = new EmailAttachment();
                $attachment->setId($attachment_id);
                $attachment->setName($file_name);

                if ($is_inline_attachment) {
                    $attachment->setType('inline');
                } else {
                    $attachment->setType('attachment');
                }

                $attachment->setAttachmentData($data);

                if ($this->save_attachments) {
                    $attachment->setFilePath($this->attachment_dir . DIRECTORY_SEPARATOR . $attachment->name());

                    if ($this->attachment_dir && $attachment->filePath()) {
                        if (!file_exists($attachment->filePath())) {
                            file_put_contents($attachment->filePath(), $data);
                        }
                    }
                }

                $email->addAttachment($attachment);
            }
        } else {
            # if the charset is set, convert to our encoding UTF-8
            if (!empty($params['charset'])) {
                $data = $this->convertEncoding($data, $params['charset']);
            }

            # part->type = 0 is TEXT or TYPETEXT
            if ($part->type == 0) {
                # subpart is either plain text or html version
                if (strtoupper($part->subtype) == 'PLAIN') {
                    $email->setPlain($data);
                } else {
                    $email->setHTML($data);
                }
            
                # part->type = 2 is MESSAGE
            } elseif ($part->type == 2) {
                $email->setPlain($data);
            }
        }

        # rerun for additional parts
        if (!empty($part->parts)) {
            foreach ($part->parts as $subpart_number => $subpart) {
                if ($part->type == 2 && $part->subtype == 'RFC822') {
                    $this->decodePart($email, $subpart, $part_number);
                } else {
                    $this->decodePart($email, $subpart, $part_number . '.' . ($subpart_number + 1));
                }
            }
        }

        return trim($data);
    }

    /**
     * Decode Mime Header.
     * @param  string $string  The encoded header string.
     * @return string          The decoded header string.
     */
    private function decodeMimeHeader($encoded_header)
    {
        $decoded_header = '';
        
        $elements = imap_mime_header_decode($encoded_header);
        
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i]->charset == 'default') {
                $elements[$i]->charset = 'iso-8859-1';
            }
        
            $decoded_header .= $this->convertEncoding($elements[$i]->text, $elements[$i]->charset);
        }
        
        return $decoded_header;
    }

    /**
     * Convert a string encoding to the encoding set.
     * @param  string $string       The string to re-encode.
     * @param  string $fromEncoding The encoding type of the original string.
     * @return string               The re-encoded string.
     */
    private function convertEncoding($string, $current_encoding_type)
    {
        $converted_string = false;

        if (!$string) {
            return $string;
        }

        if ($current_encoding_type == $this->encoding) {
            return $string;
        }
        
        if (extension_loaded('mbstring')) {
            $converted_string = @mb_convert_encoding($string, $this->encoding, $current_encoding_type);
        } else {
            $converted_string = @iconv($current_encoding_type, $this->encoding . '//IGNORE', $string);
        }
        
        return $converted_string ?: $string;
    }
}
