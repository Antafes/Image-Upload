<?php

$DB_MIGRATION = array(

	'description' => function () {
		return 'initial tables';
	},

	'up' => function ($migration_metadata) {

		$results = array();

		$results[] = query_raw('
			CREATE TABLE `imagelist` (
				`imagelist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`userId` int(10) unsigned NOT NULL,
				`imagename` varchar(255) NOT NULL,
				`mimetype` varchar(255) NOT NULL,
				`hash` varchar(255) NOT NULL,
				`add_datetime` datetime NOT NULL,
				`deleted` tinyint(1) NOT NULL,
				PRIMARY KEY (`imagelist_id`),
				KEY `userId` (`userId`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1
		');

		$results[] = query_raw('
			CREATE TABLE `users` (
				`userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`nick` varchar(255) NOT NULL,
				`password` varchar(255) NOT NULL,
				`email` varchar(255) NOT NULL,
				`admin` tinyint(1) NOT NULL,
				`createdDatetime` datetime NOT NULL,
				PRIMARY KEY (`userID`)
			) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1
		');

		return !in_array(false, $results);

	},

	'down' => function ($migration_metadata) {

		$results = array();

		$results[] = query_raw('
			DROP TABLE `imagelist`
		');

		$results[] = query_raw('
			DROP TABLE `users`
		');

		return !in_array(false, $results);

	}

);