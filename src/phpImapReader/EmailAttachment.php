<?php

namespace benhall14\phpImapReader;

/**
 * IMAP Email Attachment Class.
 *
 * @category  Protocols
 * @package   Protocols
 * @author    Benjamin Hall <ben@conobe.co.uk>
 * @copyright 2022 Copyright (c) Benjamin Hall
 * @license   MIT https://github.com/benhall14/php-imap-reader
 * @link      https://conobe.co.uk/projects/php-imap-reader/
 */
class EmailAttachment
{
    /**
     * The attachment id
     * 
     * @var int
     */
    public $id;

    /**
     * The attachment name.
     * 
     * @var string
     */
    public $name;

    /**
     * The attachment file path.
     * 
     * @var string
     */
    public $file_path;

    /**
     * The attachment type.
     * 
     * @var string
     */
    public $type;

    /**
     * The attachment mime type.
     * 
     * @var string
     */
    public $mime;

    /**
     * Sets the attachments id.
     * 
     * @param int $id The attachment id.
     * 
     * @return EmailAttachment
     */
    public function setID($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the attachments name.
     * 
     * @param string $name The attachment name.
     * 
     * @return EmailAttachment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the attachment type.
     * 
     * @param string $type The attachment type.
     * 
     * @return EmailAttachment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Sets the attachment mime type.
     * 
     * @param string $mime_type The attachment mime type.
     * 
     * @return EmailAttachment
     */
    public function setMime($mime_type)
    {
        $this->mime = $mime_type;

        return $this;
    }

    /**
     * Sets the attachments file path.
     * 
     * @param string $file_path The attachment file path.
     * 
     * @return EmailAttachment
     */
    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;

        return $this;
    }

    /**
     * Sets the attachments data
     * 
     * @param string $data The attachment data.
     * 
     * @return EmailAttachment
     */
    public function setAttachmentData($data)
    {
        $this->attachment_data = $data;

        return $this;
    }

    /**
     * Get the attachments id.
     * 
     * @return integer
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the attachments name.
     * 
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the attachments file path.
     * 
     * @return string
     */
    public function filePath()
    {
        return $this->file_path;
    }

    /**
     * Get the attachments content.
     * 
     * @return string
     */
    public function content()
    {
        return $this->attachment_data;
    }

    /**
     * Get the attachments type.
     * 
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Gets the inline status of the attachment.
     * 
     * @return boolean
     */
    public function isInline()
    {
        return $this->type() == 'inline' ? true : false;
    }
}
