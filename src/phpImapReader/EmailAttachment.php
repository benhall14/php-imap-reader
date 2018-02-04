<?php

namespace benhall14\phpImapReader;

/**
 * IMAP Email Attachment Class.
 *
 * @copyright  Copyright (c) Benjamin Hall
 * @license https://github.com/benhall14/php-imap-reader
 * @package protocols
 * @author Benjamin Hall <https://linkedin.com/in/benhall14>
*/
class EmailAttachment
{
    /**
     * The attachment id
     * @var int
     */
    private $id;

    /**
     * The attachment name.
     * @var string
     */
    private $name;

    /**
     * The attachment file path.
     * @var string
     */
    private $file_path;

    /**
     * The attachment type.
     * @var string
     */
    private $type;

    /**
     * The attachment mime type.
     * @var string
     */
    private $mime;

    /**
     * The attachment data contents.
     *
     * @var string
     */
    private $attachment_data;

    /**
     * Sets the attachments id.
     * @param int $id The attachment id.
     */
    public function setID($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the attachments name.
     * @param string $name The attachment name.
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the attachment type.
     * @param string $type The attachment type.
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Sets the attachment mime type.
     * @param string $mime_type The attachment mime type.
     */
    public function setMime($mime_type)
    {
        $this->mime = $mime_type;

        return $this;
    }

    /**
     * Sets the attachments file path.
     * @param string $file_path The attachment file path.
     */
    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;

        return $this;
    }

    /**
     * Sets the attachments data
     * @param string $data The attachment data.
     */
    public function setAttachmentData($data)
    {
        $this->attachment_data = $data;

        return $this;
    }

    /**
     * Get the attachments id.
     * @return int The attachment id.
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the attachments name.
     * @return string The attachment name.
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the attachments file path.
     * @return string The attachment file path.
     */
    public function filePath()
    {
        return $this->file_path;
    }

    /**
     * Get the attachments content.
     * @return string The attachment content data.
     */
    public function content()
    {
        return $this->attachment_data;
    }
    
    /**
     * Get the attachments type.
     * @return string The attachment type.
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Gets the inline status of the attachment.
     * @return boolean Returns true if the attachment is an inline type.
     */
    public function isInline()
    {
        return $this->type == 'inline' ? true : false;
    }
}
