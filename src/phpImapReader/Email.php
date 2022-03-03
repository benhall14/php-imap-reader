<?php

namespace benhall14\phpImapReader;

use DateTime;
use stdClass;

/**
 * IMAP Email Object Class.
 *
 * @category  Protocols
 * @package   Protocols
 * @author    Benjamin Hall <ben@conobe.co.uk>
 * @copyright 2019 Copyright (c) Benjamin Hall
 * @license   MIT https://github.com/benhall14/php-imap-reader
 * @link      https://conobe.co.uk/projects/php-imap-reader/
 */
class Email
{
    /**
     * The ID of this email.
     * 
     * @var int
     */
    public $id;
    
    /**
     * The msgno of this email.
     * 
     * @var int
     */
    public $msgno;

    /**
     * The date this email was received.
     * 
     * @var DateTime
     */
    public $date;

    /**
     * The UNIX time stamp when this email was received.
     * 
     * @var int
     */
    public $udate;
    
    /**
     * An array containing the custom headers of the email.
     * 
     * @var array
     */
    public $custom_headers = array();

    /**
     * The subject line of this email.
     * 
     * @var string
     */
    public $subject;

    /**
     * The recipient of this email.
     * 
     * @var array
     */
    public $to = array();

    /**
     * The sender of this email.
     * 
     * @var string
     */
    public $from;

    /**
     * An array of Reply To email addresses.
     * 
     * @var array
     */
    public $reply_to = array();

    /**
     * An array of Carbon Copy recipients.
     * 
     * @var array
     */
    public $cc = array();

    /**
     * The 'recent' flag.
     * 
     * @var boolean
     */
    public $recent;

    /**
     * The 'unseen' flag.
     * 
     * @var boolean
     */
    public $unseen;

    /**
     * The 'flagged' flag.
     * 
     * @var boolean
     */
    public $flagged;

    /**
     * The 'answered' flag.
     * 
     * @var boolean
     */
    public $answered;

    /**
     * The 'deleted' flag.
     * 
     * @var boolean
     */
    public $deleted;

    /**
     * The 'draft' flag.
     * 
     * @var boolean
     */
    public $draft;

    /**
     * The integer size of this email.
     * 
     * @var int
     */
    public $size;

    /**
     * The plain text body of this email.
     * 
     * @var string
     */
    public $text_plain;

    /**
     * The HTML body of this email.
     * 
     * @var string
     */
    public $text_html;

    /**
     * An array of attachments, including inline.
     * 
     * @var array
     */
    public $attachments = array();

    /**
     * Raw Body
     *
     * @var string
     */
    public $raw_body;

    /**
     * Checks if the email recipient matches the given email address.
     * 
     * @param string $email The email address to match against the recipient.
     * 
     * @return boolean
     */
    public function isTo($email)
    {
        foreach ($this->to as $to) {
            if ($email == $to->email) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the 'Reply To' email addresses for this email.
     * 
     * @return array
     */
    public function replyTo()
    {
        return $this->reply_to;
    }

    /**
     * Get the 'Carbon Copied' email addresses for this email.
     * 
     * @return array An array of email addresses in the cc field.
     */
    public function cc()
    {
        return $this->cc;
    }

    /**
     * Get the recipient of this email.
     * 
     * @return string
     */
    public function to()
    {
        return $this->to;
    }

    /**
     * Get the ID of this email.
     * 
     * @return integer The ID of the email.
     */
    public function id()
    {
        return $this->id;
    }
    
    /**
     * Get the msgno of this email.
     * 
     * @return integer The msgno of the email.
     */
    public function msgno()
    {
        return $this->msgno;
    }
    
    /**
     * Get the custom headers of this email.
     * 
     * @return array The custom headers of the email.
     */
    public function customHeaders()
    {
        return $this->custom_headers;
    }
    
    /**
     * Get the size in bytes of this email.
     * 
     * @return integer
     */
    public function size()
    {
        return (int) $this->size;
    }

    /**
     * Get the date that this email was received.
     * 
     * @param string $format The format in which to return the date.
     * 
     * @return string
     */
    public function date($format = 'Y-m-d H:i:s')
    {
        return $this->date->format($format);
    }

    /**
     * Get the subject line of this email.
     * 
     * @return string
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * Get the sender name of this email.
     * 
     * @return string
     */
    public function fromName()
    {
        return $this->from && $this->from->name ? $this->from->name : null;
    }

    /**
     * Get the sender email address of this email.
     * 
     * @return string
     */
    public function fromEmail()
    {
        return $this->from && $this->from->email ? $this->from->email : null;
    }

    /**
     * Get the plain text body of this email.
     * 
     * @return string
     */
    public function plain()
    {
        return $this->text_plain;
    }

    /**
     * Get the HTML body of this email.
     * 
     * @return string
     */
    public function html()
    {
        return $this->text_html ? $this->injectInline($this->text_html) : false;
    }

    /**
     * Return a boolean based on whether this email has attachments.
     * 
     * @return boolean
     */
    public function hasAttachments()
    {
        return (count($this->attachments)) ? true : false;
    }

    /**
     * Return an array of the attachments for this email.
     * 
     * @return array
     */
    public function attachments()
    {
        return $this->attachments;
    }

    /**
     * Return a specific attachment based on the attachment id.
     * 
     * @param integer $attachment_id The attachment to return.
     * 
     * @return EmailAttachment
     */
    public function attachment($attachment_id)
    {
        return isset($this->attachments[$attachment_id]) ? $this->attachments[$attachment_id] : false;
    }

    /**
     * Return the status of the recent flag.
     * 
     * @return boolean
     */
    public function isRecent()
    {
        return $this->recent;
    }

    /**
     * Return the status of the unseen flag.
     * 
     * @return boolean
     */
    public function isUnseen()
    {
        return $this->unseen;
    }

    /**
     * Return the status of the flagged flag.
     * 
     * @return boolean
     */
    public function isFlagged()
    {
        return $this->flagged;
    }

    /**
     * Return the status of the answered flag.
     * 
     * @return boolean
     */
    public function isAnswered()
    {
        return $this->answered;
    }

    /**
     * Return the status of the deleted flag.
     * 
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Return the status of the draft flag.
     * 
     * @return boolean
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * Set the subject line for this email.
     * 
     * @param string $subject The subject line string.
     * 
     * @return Email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the unique id for this email.
     * 
     * @param integer $id The id.
     * 
     * @return Email
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    
    /**
     * Set the msgno for this email.
     * 
     * @param integer $msgno The msgno.
     * 
     * @return Email
     */
    public function setMsgno($msgno)
    {
        $this->msgno = $msgno;

        return $this;
    }

    /**
     * Sets the raw body.
     *
     * @param string $body
     *
     * @return Email
     */
    public function setRawBody($body)
    {
        $this->raw_body = $body;

        return $this;
    }

    /**
     * Set the date for this email.
     * 
     * @param string $date The Date string.
     * 
     * @return Email
     */
    public function setDate($date)
    {
        $this->date = new DateTime($date);

        return $this;
    }

    /**
     * Set the UNIX time stamp for this email.
     * 
     * @param integer $date A UNIX time stamp.
     * 
     * @return Email
     */
    public function setUdate($date)
    {
        $this->udate = $date;

        return $this;
    }

    /**
     * Set the size of this email.
     * 
     * @param integer $size Size in bytes.
     * 
     * @return Email
     */
    public function setSize($size)
    {
        $this->size = (int) $size;

        return $this;
    }

    /**
     * Sets the unseen flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setUnseen($boolean)
    {
        $this->unseen = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the answered flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setAnswered($boolean)
    {
        $this->answered = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the draft flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setDraft($boolean)
    {
        $this->draft = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the recent flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setRecent($boolean)
    {
        $this->recent = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the flagged flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setFlagged($boolean)
    {
        $this->flagged = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the deleted flag based on the given boolean state.
     * 
     * @param boolean $boolean The flag status.
     * 
     * @return Email
     */
    public function setDeleted($boolean)
    {
        $this->deleted = (bool) $boolean;

        return $this;
    }

    /**
     * Adds a recipient to the 'To' array.
     * 
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The recipient name.
     * 
     * @return Email
     */
    public function addTo($mailbox, $host, $name = false)
    {
        if (!$mailbox || !$host) {
            return false;
        }

        $to = new stdClass();

        $to->name = ($name) ? $name : false;

        $to->mailbox = $mailbox;

        $to->host = $host;

        $to->email = $to->mailbox . '@' . $to->host;

        $this->to[] = $to;

        return $this;
    }

    /**
     * Adds a 'Reply To' to 'Reply To' array.
     * 
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The reply to name.
     * 
     * @return Email
     */
    public function addReplyTo($mailbox, $host, $name = false)
    {
        if (!$mailbox || !$host) {
            return false;
        }

        $reply_to = new stdClass();

        $reply_to->name = ($name) ? $name : false;

        $reply_to->mailbox = $mailbox;

        $reply_to->host = $host;

        $reply_to->email = $reply_to->mailbox . '@' . $reply_to->host;

        $this->reply_to[] = $reply_to;

        return $this;
    }

    /**
     * Adds a custom header to this email.
     * 
     * @param string $custom_header The custom header to append to the array.
     * 
     * @return Email
     */
    public function addCustomHeader($custom_header)
    {
        if (!$custom_header) {
            return false;
        }

        $header_info = explode(":", $custom_header);
        
        if (is_array($header_info) && isset($header_info[0]) && isset($header_info[1])) {
            $this->custom_headers[$header_info[0]] = (string) trim($header_info[1]);
        }

        return $this;
    }

    /**
     * Returns a specific header (if it exists), or returns null.
     *
     * @param string $header_name
     *
     * @return string
     */
    public function getCustomHeader($header_name)
    {
        return isset($this->custom_headers) && isset($this->custom_headers[$header_name]) ? $this->custom_headers[$header_name] : null;
    }

    /**
     * An alias for getCustomHeader
     *
     * @param string $header_name
     *
     * @return string
     */
    public function getHeader($header_name)
    {
        return $this->getCustomHeader($header_name);
    }

    /**
     * Returns, or saves, the .eml version of the email.
     *
     * @param mixed $filename
     *
     * @return string
     */
    public function eml($filename = null)
    {
        if ($filename && !file_exists($filename)) {
            file_put_contents($filename, $this->raw_body);
        }

        return $this->raw_body;
    }

    /**
     * Save the Email as an .eml file.
     *
     * @param string $filename
     *
     * @return string
     */
    public function saveEml($filename)
    {
        return $this->eml($filename);
    }
    
    /**
     * Adds a carbon copy entry to this email.
     * 
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The name of the CC.
     * 
     * @return Email
     */
    public function addCC($mailbox, $host, $name = false)
    {
        if (!$mailbox || !$host) {
            return false;
        }

        $cc = new stdClass();

        $cc->name = $name ?: false;

        $cc->mailbox = $mailbox;

        $cc->host = $host;

        $cc->email = $cc->mailbox . '@' . $cc->host;

        $this->cc[] = $cc;

        return $this;
    }

    /**
     * Set the 'from' email address for this email.
     * 
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The senders name.
     * 
     * @return Email
     */
    public function setFrom($mailbox, $host, $name = false)
    {
        $this->from = new stdClass();

        $this->from->name = $name ?: false;

        $this->from->mailbox = $mailbox;

        $this->from->host = $host;

        $this->from->email = $this->from->mailbox . '@' . $this->from->host;

        return $this;
    }

    /**
     * Updates the HTML text body by concatenating the given 
     * string to the current HTML body.
     * 
     * @param string $html The HTML string to be added to the HTML text body.
     * 
     * @return Email
     */
    public function setHTML($html)
    {
        $this->text_html .= trim($html);

        return $this;
    }

    /**
     * Updates the plain text body by concatenating the given 
     * string to the current plain text body.
     * 
     * @param string $plain The text string to be added to the plain text body.
     * 
     * @return Email
     */
    public function setPlain($plain)
    {
        $this->text_plain .= trim($plain);

        return $this;
    }

    /**
     * Adds an attachment to this email.
     * 
     * @param EmailAttachment $attachment An attachment object.
     * 
     * @return Email
     */
    public function addAttachment(EmailAttachment $attachment)
    {
        $this->attachments[$attachment->id()] = $attachment;

        return $this;
    }

    /**
     * Inject in-line attachments by replacing the attachment ids
     * with the attachment file path.
     * 
     * @param  string $body The email body to have attachments injected.
     * 
     * @return string
     */
    public function injectInline($body)
    {
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                if ($attachment->isInline()) {
                    $body = str_replace('cid:' . $attachment->id(), $attachment->filePath(), $body);
                }
            }
        }

        return $body;
    }
}
