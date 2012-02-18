<?php
session_start();

require_once(dirname(__FILE__).'/lib/mysql.php');
require_once(dirname(__FILE__).'/lib/functions.php');

if ($_GET['do'])
{
	switch ($_GET['do']) {
		case 'recreateThumbs':
			$dir = opendir(dirname(__FILE__).'/'.$GLOBALS['config']['thumbsDir']);

			while ($element = readdir($dir))
				if ($element != '.' && $element != '..' && $element != '.htaccess')
					unlink(dirname(__FILE__).'/'.$GLOBALS['config']['thumbsDir'].$element);

			closedir($dir);

			$sql = '
				SELECT imagelist_id
				FROM imagelist
				WHERE !deleted
			';
			$imagelist = mysqlQuery($sql, true);

			foreach ($imagelist as $image)
				createThumbnail($image['imagelist_id']);

			header('Location: admin.php?recreate=done');
			break;
		default:
			break;
	}
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
<body id="admin">
<?php
require_once(dirname(__FILE__).'/menu.php');
?><br />
	<a href="admin.php?do=recreateThumbs">Thumbnails neu erstellen</a>
<?php
if ($_GET['recreate'] == 'done')
	echo '<br />Thumbnails neu erstellt.';
?>
</body>
</html>