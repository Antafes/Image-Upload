<?php
session_start();

if (!$_SESSION['user'])
{
	header('Location: login.php');
	die();
}

require_once('lib/mysql.php');
require_once('lib/functions.php');

if ($_GET['delete'])
{
	$sql = '
		UPDATE imagelist
		SET deleted = 1
		WHERE imagelist_id = '.sqlval($_GET['delete']).'
	';
	mysqlQuery($sql);

	header('Location: imagelist.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<title>Image Upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="author" content="Marian Pollzien" />
<meta http-equiv="cache-control" content="no-cache" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body id="imagelist">
<?php
require_once('menu.php');

if ($_GET['userID'] || $_SESSION['user'])
{
	$sql = '
		SELECT
			imagelist_id,
			hash,
			DATE(add_datetime) AS add_date
		FROM imagelist
		WHERE userID = '.sqlval($_SESSION['user'] ? $_SESSION['user'] : $_GET['userID']).'
			AND !deleted
	';
	$data = mysqlQuery($sql, true);

	$row = 0;
	foreach ($data as $image)
	{
		if (checkImage($image['hash'], DateTime::createFromFormat('Y-m-d', $image['add_date']), false) === 'Kein Bild gefunden')
			continue;

		if ($row === 0)
			echo '<div class="row">'."\n";

		echo '<div class="left">'."\n";
		echo '<a href="index.php?image='.$image['hash'].$image['add_date'].'"><img src="index.php?image='.$image['hash'].$image['add_date'].'&amp;showThumb=1" /><br />'."\n";
		echo $dir.'</a><br />'."\n";
		echo '<a href="imagelist.php?delete='.$image['imagelist_id'].'">löschen</a>';
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