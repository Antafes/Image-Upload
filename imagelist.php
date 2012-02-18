<?php
session_start();

if (!$_SESSION['user'])
{
	header('Location: login.php');
	die();
}
?>
<html>
<head>
<title>Image List</title>
</head>
<style type="text/css">
img {
	border: 0 none;
	max-width: 100px;
}

a {
	text-decoration: none;
}

.left {
	float: left;
	margin-right: 10px;
	text-align: center;
}

.row
{
	margin-top: 10px;
}
</style>
<body>
	<a href="upload.php">Zum Upload</a>
	<a href="logout.php">Logout</a>
<?php
require_once('lib/config.php');
require_once('lib/mysql.php');
require_once('lib/functions.php');
$con = mysql_connect($host, $user, $password);
mysql_select_db($db);

if ($_GET['userID'] || $_SESSION['user'])
{
	$sql = '
		SELECT * FROM imagelist
		WHERE userID = '.mysql_real_escape_string($_SESSION['user'] ? $_SESSION['user'] : $_GET['userID']).'
	';
	$data = mysqlQuery($sql, true);

	$row = 0;
	foreach ($data as $image)
	{
		$image_date = date_create_from_format('Y-m-d H:i:s', $image['add_datetime']);
		if (checkImage($image['hash'], $image_date->format('Y-m-d')) === 'Kein Bild gefunden')
			continue;

		if ($row === 0)
			echo '<div class="row">'."\n";

		echo '<div class="left">'."\n";
		echo '<a href="index.php?image='.$image['hash'].substr($image['add_datetime'], 0, 10).'"><img src="index.php?image='.$image['hash'].substr($image['add_datetime'], 0, 10).'" /><br />'."\n";
		echo $dir.'</a>'."\n";
		echo '</div>'."\n";
		$row++;

		if ($row > 4)
		{
			$row = 0;
			echo '<br style="clear: both;" />'."\n";
			echo '</div>'."\n";
		}
	}
}
?>
</body>
</html>