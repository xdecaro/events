CREATE TABLE IF NOT EXISTS `#__decaroevents_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `event_type` varchar(100) NOT NULL DEFAULT '',
  `description` mediumtext NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  `start_at` datetime NOT NULL,
  `end_at` datetime NULL,
  `capacity` int unsigned NULL,
  `registration_open` tinyint(1) NOT NULL DEFAULT 0,
  `access` int unsigned NOT NULL DEFAULT 1,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published_start` (`published`,`start_at`),
  KEY `idx_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__decaroevents_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  `start_at` datetime NOT NULL,
  `end_at` datetime NULL,
  `capacity` int unsigned NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `modified` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_event_start` (`event_id`,`start_at`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__decaroevents_registrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned NOT NULL,
  `session_id` int unsigned NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(320) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `checked_in_at` datetime NULL,
  `notes` text NULL,
  `created` datetime NOT NULL,
  `modified` datetime NULL,
  PRIMARY KEY (`id`),
  KEY `idx_event_status` (`event_id`,`status`),
  KEY `idx_session_status` (`session_id`,`status`),
  UNIQUE KEY `uniq_event_email` (`event_id`,`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
