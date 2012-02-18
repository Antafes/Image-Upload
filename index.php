<?php
session_start();

require_once('lib/mysql.php');
require_once('lib/functions.php');

if ($_GET['image'])
	echo checkImage(substr($_GET['image'], 0, -10), DateTime::createFromFormat('Y-m-d', substr($_GET['image'], -10)), true, ($_GET['showThumb'] ? true : false));
else
	header('Location: login.php');