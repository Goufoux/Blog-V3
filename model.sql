CREATE DATABASE `blog` /*!40100 DEFAULT CHARACTER SET utf8 */;

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_created_at` datetime DEFAULT NULL,
  `comment_user` int(11) DEFAULT NULL,
  `comment_content` text,
  `comment_state` int(11) DEFAULT '0',
  `comment_post` int(11) DEFAULT NULL,
  `comment_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `logs` (
  `logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `logs_created_at` datetime DEFAULT NULL,
  `logs_code` int(11) DEFAULT NULL,
  `logs_message` text,
  `logs_ligne` varchar(150) DEFAULT NULL,
  `logs_fichier` varchar(150) DEFAULT NULL,
  `logs_titre` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`logs_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `post_user` int(11) NOT NULL,
  `post_title` varchar(100) NOT NULL,
  `post_seo_title` varchar(100) DEFAULT NULL,
  `post_chapo` text,
  `post_seo_description` varchar(250) DEFAULT NULL,
  `post_content` text NOT NULL,
  `post_image` varchar(150) DEFAULT NULL,
  `post_image_alt` varchar(100) DEFAULT NULL,
  `post_format` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `role_name` varchar(45) DEFAULT NULL,
  `role_description` text,
  `role_create` tinyint(4) NOT NULL DEFAULT '0',
  `role_update` tinyint(4) NOT NULL DEFAULT '0',
  `role_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_created_at` datetime DEFAULT NULL,
  `user_updated_at` datetime DEFAULT NULL,
  `user_name` varchar(35) DEFAULT NULL,
  `user_first_name` varchar(35) DEFAULT NULL,
  `user_email` varchar(150) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_token` varchar(255) DEFAULT NULL,
  `user_token_renewal` datetime DEFAULT NULL,
  `user_newsletter` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `userrole` (
  `userRole_id` int(11) NOT NULL AUTO_INCREMENT,
  `userRole_user` int(11) DEFAULT NULL,
  `userRole_role` int(11) DEFAULT NULL,
  `userRole_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userRole_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
