# PHP IMAP Reader
A PHP class that makes working with IMAP as easy as possible. 

This class is written to be chain-able so to create a logically fluent and easily readable way to access an IMAP mailbox. 

It simplifies the PHP IMAP_* library into a set of easy to read methods that do the heavy lifting for you.

It has been fully tested to work with PHP 5.3+, including **PHP 8.1.**

# Installation via Composer
You can now install this class via composer.

	$ composer require benhall14/php-imap-reader
	
**Remember** to add the composer autoloader before using the class and use the correct namespace.

	require 'vendor/autoload.php';

	use benhall14\phpImapReader\Email as Email;
	use benhall14\phpImapReader\EmailAttachment as EmailAttachment;
	use benhall14\phpImapReader\Reader as Reader;

# Usage
Please make sure you have added the required classes.

In its simplest form, use the following to connect:

```php
define('IMAP_USERNAME', ''); 				# your imap user name
define('IMAP_PASSWORD', ''); 				# your imap password
define('IMAP_MAILBOX', ''); 				# your imap address EG. {mail.example.com:993/novalidate-cert/ssl}
define('ATTACHMENT_PATH', __DIR__ . '/attachments'); 	# the path to save attachments to or false to skip attachments

try{
    
    # set the mark as read flag (true by default). If you don't want emails to be marked as read/seen, set this to false.
    $mark_as_read = true;

    # You can ommit this to use UTF-8 by default.
    $encoding = 'UTF-8'

    # create a new Reader object
    $imap = new Reader(IMAP_MAILBOX, IMAP_USERNAME, IMAP_PASSWORD, ATTACHMENT_PATH, $mark_as_read, $encoding);

    # use one or more of the following chain-able methods to filter your email selection
    $imap
        ->folder($folder)           # alias for mailbox($mailbox)
        ->mailbox($mailbox)         # sets the mailbox to return emails from. Default = INBOX
        ->id($id)                   # retrieve a specific email by id
        ->recent()                  # get all RECENT emails
        ->flagged()                 # get all FLAGGED emails
        ->unflagged()               # get all UNFLAGGED emails
        ->unanswered()              # get all UNANSWERED emails
        ->deleted()                 # get all DELETED emails
        ->unread() 		    # alias for UNSEEN()
        ->unseen()                  # get all UNSEEN emails
        ->from($email)              # get all emails from $email
        ->searchSubject($string)    # get all emails with $string in the subject line
        ->searchBody($string)       # get all emails with $string in the body
        ->searchText($string)       # get all emails with $string TEXT
        ->seen()                    # get all SEEN emails
        ->read() 		    # alias for SEEN()
        ->newMessages()             # get all NEW emails
        ->oldMessages()             # get all OLD emails
        ->keyword($keyword)         # get all emails with $keyword KEYWORD
        ->unkeyword($keyword)       # get all emails without $keyword KEYWORD
        ->beforeDate($date)         # get all emails received before $date. *Date should be in a format that can be parsed by strtotime.*
        ->sinceDate($date)          # get all emails received since $date. *Date should be in a format that can be parsed by strtotime.*
        ->sentTo($to)               # get all emails sent to $to
        ->searchBCC($string)        # get all emails with $string in the BCC field
        ->searchCC($string)         # get all emails with $string in the CC field
        ->onDate($date)             # get all emails received on $date. *Date should be in a format that can be parsed by strtotime.*
        ->limit($limit)             # limit the number of emails returned to $limit for pagination
        ->page($page)               # used with limit to create pagination
        ->orderASC()                # order the emails returned in ASCending order
        ->orderDESC()               # order the emails returned in DESCendeing order
        ->reset()                   # resets the current reader to be able to reconnect to another folder/mailbox.
        ->all()                     # get all emails (default)
        ->get();                    # finally make the connection and retrieve the emails.
    
    # You can then loop through $imap->emails() for each email.
    foreach($imap->emails() as $email){

        # The email has been clean and formated.
        # see below.

    }

    # Reset the reader and connect to another folder.
    $imap->reset()->folder('Sent')->get();
    
    # ... your code here ...

} catch (Exception $e){

    echo $e->getMessage();

}
```

While looping through the returned emails, each email object can be used as below:
```php

    $email->isTo('mail@example.com');   # Return true if the email is to $email, else returns false

    $email->replyTo();              	# Returns an array of Reply To email addresses (and names)

    $email->cc();                 	# Returns an array of CC email addresses (and names)

    $email->to();                       # Returns the recipient email address

    $email->id();                       # Returns the id of the email

    $email->size();                     # Returns the size of the email

    $email->date($format);        	# Returns the date in the $format specified. Default Y-m-d H:i:s

    $email->subject();          	# Returns the email subject

    $email->fromName();     		# Returns the sender's name, if set.

    $email->fromEmail();     		# Returns the sender's email address

    $email->plain();            	# Returns the plain text body of the email, if present

    $email->html();            		# Returns the html body of the email, if present

    $email->hasAttachments();       	# Returns true/false based on if the email has attachments

    $email->attachments();      	# Returns an array of EmailAttachment objects

    $email->attachment($id);    	# Returns an attachment based on the given attachment $id

    $email->isRecent();   		# Returns true/false based on the recent flag

    $email->isUnseen();       		# Returns true/false based on the unseen flag

    $email->isFlagged();  		# Returns true/false based on the flagged flag

    $email->isAnswered(); 		# Returns true/false based on the answered flag

    $email->isDeleted();      		# Returns true/false based on the deleted flag

    $email->isDraft();          	# Returns true/false based on the draft flag

    $email->eml();                      # Returns the email in .eml format

    $email->saveEml($filename);         # Saves the email in .eml format
    
    $email->count();         # Returns number of emails in folder

```

The **$email->attachments();** method returns an array of attachments belonging to the email in a **benhall14\phpImapReader\EmailAttachment** object. The following methods are available for each attachment.

```php

	# check if the current $email has any attachments.
	if($email->hasAttachments()){
	
		# get the attachments for the current $email in the	loop.
		$attachments = $email->attachments();
	
		# loop through the found attachments.
		foreach($attachments as $attachment){

			$attachment->id(); 			# Returns the attachments ID.

			$attachment->name(); 		    	# Returns the attachments name.

			$attachment->filePath(); 		# Returns the local file path for the attachment. This is based on the ATTACHMENT_PATH constant set in the imap config.

			$attachment->content();			# Returns the attachments content data.

			$attachment->type(); 			# Returns either 'attachment' or 'inline'.

		}

}	
```

# Requirements

**Works with PHP 5.3+ (including PHP 8.1)**

**PHP IMAP Extension**

# License
Copyright (c) 2016-2021 Benjamin Hall, ben@conobe.co.uk
https://conobe.co.uk

Licensed under the MIT license

# Donate?

If you find this project helpful or useful in anyway, please consider getting me a cup of coffee - It's really appreciated :)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://paypal.me/benhall14)
