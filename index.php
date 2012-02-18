<?php
session_start();

require_once('lib/db.php');
require_once('lib/mysql.php');
require_once('lib/functions.php');
$con = mysql_connect($host, $user, $password);
mysql_select_db($db);
if ($_GET['image'])
{
	$hash = substr($_GET['image'], 0, -10);
	$date = substr($_GET['image'], -10);

	header('Content-type: '.$imageArray['mimetype']);
	echo checkImage($hash, $date);
}
else
	header('Location: login.php');