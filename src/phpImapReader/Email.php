<?php

namespace benhall14\phpImapReader;

use DateTime;
use stdClass;

/**
 * IMAP Email Object Class.
 *
 * @copyright  Copyright (c) Benjamin Hall
 * @license https://github.com/benhall14/php-imap-reader
 * @package protocols
 * @author Benjamin Hall <https://linkedin.com/in/benhall14>
*/
class Email
{
    /**
     * The ID of this email.
     * @var int
     */
    private $id;
    
    /**
     * The date this email was received.
     * @var DateTime
     */
    private $date;
    
    /**
     * The UNIX time stamp when this email was received.
     * @var int
     */
    private $udate;
    
    /**
     * The subject line of this email.
     * @var string
     */
    private $subject;
    
    /**
     * The recipient of this email.
     * @var array
     */
    private $to = array();
    
    /**
     * The sender of this email.
     * @var string
     */
    private $from;
    
    /**
     * An array of Reply To email addresses.
     * @var array
     */
    private $reply_to = array();
    
    /**
     * An array of Carbon Copy recipients.
     * @var array
     */
    private $cc = array();

    /**
     * The 'recent' flag.
     * @var boolean
     */
    private $recent;
    
    /**
     * The 'unseen' flag.
     * @var boolean
     */
    private $unseen;
    
    /**
     * The 'flagged' flag.
     * @var boolean
     */
    private $flagged;
    
    /**
     * The 'answered' flag.
     * @var boolean
     */
    private $answered;
    
    /**
     * The 'deleted' flag.
     * @var boolean
     */
    private $deleted;
    
    /**
     * The 'draft' flag.
     * @var boolean
     */
    private $draft;
    
    /**
     * The integer size of this email.
     * @var int
     */
    private $size;

    /**
     * The plain text body of this email.
     * @var string
     */
    private $text_plain;
    
    /**
     * The HTML body of this email.
     * @var string
     */
    private $text_html;

    /**
     * An array of attachments, including inline.
     * @var array
     */
    private $attachments = array();

    /**
     * Checks if the email recipient matches the given email address.
     * @param  string  $email The email address to match against the recipient.
     * @return boolean        Returns true if the email address matches, else false.
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
     * @return array An array of email addresses in the 'Reply To' field.
     */
    public function replyTo()
    {
        return $this->reply_to;
    }

    /**
     * Get the 'Carbon Copied' email addresses for this email.
     * @return array An array of email addresses in the cc field.
     */
    public function cc()
    {
        return $this->cc;
    }

    /**
     * Get the recipient of this email.
     * @return string The recipient information.
     */
    public function to()
    {
        return $this->to;
    }
    
    /**
     * Get the ID of this email.
     * @return int The ID of the email.
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the size in bytes of this email.
     * @return int The size.
     */
    public function size()
    {
        return (int) $this->size;
    }

    /**
     * Get the date that this email was received.
     * @param  string $format The format in which to return the date.
     * @return string         The formatted date string.
     */
    public function date($format = 'Y-m-d H:i:s')
    {
        return $this->date->format($format);
    }

    /**
     * Get the subject line of this email.
     * @return string The subject.
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * Get the sender name of this email.
     * @return string The 'From' Name.
     */
    public function fromName()
    {
        return $this->from->name;
    }

    /**
     * Get the sender email address of this email.
     * @return string The 'From' email.
     */
    public function fromEmail()
    {
        return $this->from->email;
    }

    /**
     * Get the plain text body of this email.
     * @return string The plain text body.
     */
    public function plain()
    {
        return $this->text_plain;
    }

    /**
     * Get the HTML body of this email.
     * @return string The HTML body.
     */
    public function html()
    {
        return $this->text_html ? $this->injectInline($this->text_html) : false;
    }

    /**
     * Return a boolean based on whether this email has attachments.
     * @return boolean True if the email has attachments, else false.
     */
    public function hasAttachments()
    {
        return (count($this->attachments)) ? true : false;
    }

    /**
     * Return an array of the attachments for this email.
     * @return array The attachment array.
     */
    public function attachments()
    {
        return $this->attachments;
    }

    /**
     * Return a specific attachment based on the attachment id.
     * @param  int $attachment_id The attachment to return.
     * @return EmailAttachment  The attachment object
     */
    public function attachment($attachment_id)
    {
        return isset($this->attachments[$attachment_id]) ? $this->attachments[$attachment_id] : false;
    }

    /**
     * Return the status of the recent flag.
     * @return boolean The recent flag status.
     */
    public function isRecent()
    {
        return $this->recent;
    }

    /**
     * Return the status of the unseen flag.
     * @return boolean The unseen flag status.
     */
    public function isUnseen()
    {
        return $this->unseen;
    }

    /**
     * Return the status of the flagged flag.
     * @return boolean The flagged flag status.
     */
    public function isFlagged()
    {
        return $this->flagged;
    }

    /**
     * Return the status of the answered flag.
     * @return boolean The answered flag status.
     */
    public function isAnswered()
    {
        return $this->answered;
    }

    /**
     * Return the status of the deleted flag.
     * @return boolean The deleted flag status.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Return the status of the draft flag.
     * @return boolean The draft flag status.
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * Set the subject line for this email.
     * @param string $subject The subject line string.
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        
        return $this;
    }

    /**
     * Set the unique id for this email.
     * @param int $id The id.
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the date for this email.
     * @param string $date The Date string.
     */
    public function setDate($date)
    {
        $this->date = new DateTime($date);

        return $this;
    }

    /**
     * Set the UNIX time stamp for this email.
     * @param int $date A UNIX time stamp.
     */
    public function setUdate($date)
    {
        $this->udate = $date;

        return $this;
    }

    /**
     * Set the size of this email.
     * @param int $size Size in bytes.
     */
    public function setSize($size)
    {
        $this->size = (int) $size;

        return $this;
    }

    /**
     * Sets the unseen flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setUnseen($boolean)
    {
        $this->unseen = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the answered flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setAnswered($boolean)
    {
        $this->answered = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the draft flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setDraft($boolean)
    {
        $this->draft = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the recent flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setRecent($boolean)
    {
        $this->recent = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the flagged flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setFlagged($boolean)
    {
        $this->flagged = (bool) $boolean;

        return $this;
    }

    /**
     * Sets the deleted flag based on the given boolean state.
     * @param boolean $boolean The flag status.
     */
    public function setDeleted($boolean)
    {
        $this->deleted = (bool) $boolean;

        return $this;
    }

    /**
     * Adds a recipient to the 'To' array.
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The recipient name.
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
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The reply to name.
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
     * Adds a carbon copy entry to this email.
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The name of the CC.
     */
    public function addCC($mailbox, $host, $name = false)
    {
        if (!$mailbox || !$host) {
            return false;
        }

        $cc = new stdClass();

        $cc->name = $name ? : false;

        $cc->mailbox = $mailbox;

        $cc->host = $host;

        $cc->email = $cc->mailbox . '@' . $cc->host;

        $this->cc[] = $cc;

        return $this;
    }

    /**
     * Set the 'from' email address for this email.
     * @param string $mailbox The mailbox.
     * @param string $host    The host name.
     * @param string $name    (optional) The senders name.
     */
    public function setFrom($mailbox, $host, $name = false)
    {
        $this->from = new stdClass();

        $this->from->name = $name ? : false;

        $this->from->mailbox = $mailbox;

        $this->from->host = $host;

        $this->from->email = $this->from->mailbox . '@' . $this->from->host;

        return $this;
    }

    /**
     * Updates the HTML text body by concatenating the given string to the current HTML body.
     * @param string $html The HTML string to be added to the HTML text body.
     */
    public function setHTML($html)
    {
        $this->text_html .= trim($html);
    
        return $this;
    }

    /**
     * Updates the plain text body by concatenating the given string to the current plain text body.
     * @param string $plain The text string to be added to the plain text body.
     */
    public function setPlain($plain)
    {
        $this->text_plain .= trim($plain);
 
        return $this;
    }

    /**
     * Adds an attachment to this email.
     * @param EmailAttachment $attachment An attachment object.
     */
    public function addAttachment(EmailAttachment $attachment)
    {
        $this->attachments[ $attachment->id() ] = $attachment;
        
        return $this;
    }

    /**
     * Inject in-line attachments by replacing the attachment ids with the attachment file path.
     * @param  string $body The email body to have attachments injected.
     * @return string $body The email body with the attachments injected.
     */
    private function injectInline($body)
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
