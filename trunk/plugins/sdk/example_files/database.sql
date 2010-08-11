CREATE TABLE `guestbook` (
  `guestbook_id` int(5) NOT NULL auto_increment,
  `profile_id` varchar(128) NOT NULL default 'default',
  `guestbook_ip` varchar(15) NOT NULL default '',
  `guestbook_name` varchar(128) NOT NULL default '',
  `guestbook_message` text NOT NULL,
  `guestbook_mail` varchar(255) default NULL,
  `guestbook_homepage` text,
  `guestbook_hometown` varchar(100) default NULL,
  `guestbook_messenger` varchar(255) default NULL,
  `guestbook_msgtyp` varchar(5) default NULL,
  `guestbook_opinion` int(1) default NULL,
  `guestbook_date` int(11) NOT NULL default '0',
  `guestbook_comment` text,
  `guestbook_is_registered` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`guestbook_id`),
  FULLTEXT KEY `guestbook_message` (`guestbook_message`),
  FULLTEXT KEY `guestbook_comment` (`guestbook_comment`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;