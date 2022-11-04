<?php

require 'vendor/autoload.php';

use benhall14\phpImapReader\Email;
use benhall14\phpImapReader\EmailAttachment;
use benhall14\phpImapReader\Reader;

define('IMAP_USERNAME', '');
define('IMAP_PASSWORD', '');
define('IMAP_MAILBOX', ''); // For example: {outlook.office365.com:993/imap/ssl/novalidate-cert}
define('ATTACHMENT_PATH', __DIR__ . '/attachments/');

try {
    $imap = new Reader(IMAP_MAILBOX, IMAP_USERNAME, IMAP_PASSWORD, ATTACHMENT_PATH);
    
    $imap->limit(10)->get();

    foreach ($imap->emails() as $email) {
        echo '<div>';
            
        echo '<div>' . $email->fromEmail() . '</div>';
            
        echo '<div>' . $email->subject() . '</div>';
            
        echo '<div>' . $email->date('Y-m-d H:i:s') . '</div>';

        if ($email->hasAttachments()) {
            foreach ($email->attachments() as $attachment) {
                echo '<div>' . $attachment->filePath() . '</div>';
            }
        }
        
        #print_r($email->plain());
        #print_r($email->html());

        echo '</div><br/><br/><hr />';
    }
} catch (Exception $e) {
    die($e->getMessage());
}
