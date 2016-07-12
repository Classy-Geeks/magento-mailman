<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */


// Start setup
$oInstaller = $this;
$oInstaller->startSetup();

// Messages
$oInstaller->run("

CREATE TABLE `{$this->getTable('mailman/messages')}` (
	`message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status_id` tinyint(3) unsigned NOT NULL,
	`hash` VARCHAR(64) NOT NULL,
	`email_reply` text NOT NULL,
	`email_return_path` text NOT NULL,
	`email_from` text NOT NULL,
	`email_to` text NOT NULL,
	`email_cc` text,
	`email_bcc` text,
	`subject` text NOT NULL,
	`body_type` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`message_id`),
	KEY `status_id` (`status_id`),
	KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('mailman/attachments')}` (
	`attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`message_id` int(10) unsigned NOT NULL,
	`hash` VARCHAR(64) NOT NULL,
	`file_name` tinytext NOT NULL,
	`file_type` tinytext NOT NULL,
	`file_encoding` tinytext NOT NULL,
	`file_disposition` tinytext NOT NULL,
	PRIMARY KEY (`attachment_id`),
	KEY `message_id` (`message_id`),
	KEY `hash` (`hash`),
	CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `{$this->getTable('mailman/messages')}` (`message_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('mailman/events')}` (
	`event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`message_id` int(10) unsigned NOT NULL,
	`type_id` tinyint(3) unsigned NOT NULL,
	`date_created` datetime NOT NULL,
	`param` tinytext,
	PRIMARY KEY (`event_id`),
	KEY `message_id` (`message_id`),
	KEY `type_id` (`type_id`),
	KEY `date_created` (`date_created`),
	CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `{$this->getTable('mailman/messages')}` (`message_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

