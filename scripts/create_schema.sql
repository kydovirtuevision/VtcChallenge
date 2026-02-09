-- SQL schema for VtcChallenge (MySQL-compatible)

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `roles` JSON NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `confirmation_token` VARCHAR(255) DEFAULT NULL,
  `api_token` VARCHAR(64) DEFAULT NULL,
  PRIMARY KEY(`id`),
  UNIQUE KEY `UNIQ_USER_EMAIL` (`email`),
  UNIQUE KEY `UNIQ_USER_APITOKEN` (`api_token`)
);

CREATE TABLE IF NOT EXISTS `note` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'new',
  `owner_id` INT NOT NULL,
  PRIMARY KEY(`id`),
  KEY `IDX_NOTE_OWNER` (`owner_id`),
  CONSTRAINT `FK_NOTE_OWNER` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
);
