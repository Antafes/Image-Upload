<?php

$DB_MIGRATION = array(

	'description' => function () {
		return 'adjustments to SmartWork';
	},

	'up' => function ($migration_metadata) {

		$results = array();

		$results[] = query_raw('
			ALTER TABLE `users`
				ALTER `nick` DROP DEFAULT,
				ALTER `password` DROP DEFAULT,
				ALTER `email` DROP DEFAULT
		');

		$results[] = query_raw('
			ALTER TABLE `users`
				COLLATE="utf8_bin",
				ENGINE=InnoDB,
				CHANGE COLUMN `nick` `name` VARCHAR(255) NOT NULL COLLATE "utf8_general_ci" AFTER `userId`,
				CHANGE COLUMN `password` `password` VARCHAR(255) NOT NULL COLLATE "utf8_bin" AFTER `name`,
				CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL COLLATE "utf8_general_ci" AFTER `password`,
				ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT "0" AFTER `createdDatetime`
		');

		$results[] = query_raw('
			ALTER TABLE `users`
				ADD COLUMN `active` TINYINT(1) NOT NULL AFTER `admin`
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				ALTER `imagename` DROP DEFAULT,
				ALTER `mimetype` DROP DEFAULT,
				ALTER `hash` DROP DEFAULT
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				COLLATE="utf8_bin",
				ENGINE=InnoDB,
				CHANGE COLUMN `imagename` `imagename` VARCHAR(255) NOT NULL COLLATE "utf8_general_ci" AFTER `userId`,
				CHANGE COLUMN `mimetype` `mimetype` VARCHAR(255) NOT NULL COLLATE "utf8_general_ci" AFTER `imagename`,
				CHANGE COLUMN `hash` `hash` VARCHAR(255) NOT NULL COLLATE "utf8_bin" AFTER `mimetype`
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				CHANGE COLUMN `imagelist_id` `imageId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST
		');

		$results[] = query_raw('
			RENAME TABLE `imagelist` TO `images`
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				ADD CONSTRAINT `imagelist_userId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE ON DELETE CASCADE
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "date", "Y-m-d", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "delete", "Löschen", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "imageIsDeleted", "Das Bild wurde gelöscht!", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "noImageFound", "Es wurde kein Bild gefunden.", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "upload", "Hochladen", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "image", "Bild", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "noFileSelected", "Keine Datei ausgewählt.<br />", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "fileTooBig", "Die Datei ist zu groß.<br />\r\nDie Dateigröße beträgt: ##FILESIZE## Byte.<br />\r\nErlaubt sind ##MAXFILESIZE## KB.", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "fileTypeNotAllowed", "Dieser Dateityp ist nicht erlaubt.<br />\r\nFolgende Dateitypen können hochgeladen werden:<br />\r\nGIF, JPEG, PNG", 0)
		');

		$results[] = query_raw('
			INSERT INTO `translations` (`languageId`, `key`, `value`, `deleted`) VALUES (1, "fileUploaded", "Dateiname: <a href="index.php?page=Image&amp;image=##IMAGEKEY##">##FILENAME##</a><br />\r\nGröße: ##FILESIZE## KB<br />\r\nLink: ##FILELINK##<br />", 0)
		');

		return !in_array(false, $results);

	},

	'down' => function ($migration_metadata) {

		$results = array();

		$results[] = query_raw('
			RENAME TABLE `images` TO `imagelist`
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				CHANGE COLUMN `imageId` `imagelist_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				DROP FOREIGN KEY `imagelist_userId`
		');

		$results[] = query_raw('
			ALTER TABLE `users`
				ALTER `name` DROP DEFAULT,
				ALTER `password` DROP DEFAULT,
				ALTER `email` DROP DEFAULT
		');

		$results[] = query_raw('
			ALTER TABLE `users`
				DROP COLUMN `active`
		');

		$results[] = query_raw('
			ALTER TABLE `users`
				COLLATE="latin1",
				ENGINE=MyISAM,
				CHANGE COLUMN `name` `nick` VARCHAR(255) NOT NULL AFTER `userId`,
				CHANGE COLUMN `password` `password` VARCHAR(255) NOT NULL AFTER `nick`,
				CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL AFTER `password`,
				DROP COLUMN `deleted`
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				ALTER `imagename` DROP DEFAULT,
				ALTER `mimetype` DROP DEFAULT,
				ALTER `hash` DROP DEFAULT
		');

		$results[] = query_raw('
			ALTER TABLE `imagelist`
				COLLATE="latin1",
				ENGINE=MyISAM,
				CHANGE COLUMN `imagename` `imagename` VARCHAR(255) NOT NULL AFTER `userId`,
				CHANGE COLUMN `mimetype` `mimetype` VARCHAR(255) NOT NULL AFTER `imagename`,
				CHANGE COLUMN `hash` `hash` VARCHAR(255) NOT NULL AFTER `mimetype`
		');

		$results[] = query_raw('
			DELETE FROM `translations` `key` IN ("date", "delete", "imageIsDeleted", "noImageFound", "upload", "image", "noFileSelected", "fileTooBig", "fileTypeNotAllowed", "fileUploaded")
		');

		return !in_array(false, $results);

	}

);