<?php
session_start();

if (!$_SESSION['user'])
{
	header('Location: login.php');
	die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Marian Pollzien" />
<meta http-equiv="cache-control" content="no-cache" />
<meta name="copyright" content="&copy; 2005 - 2010 by Neithan" />
<title>Image Upload</title>
</head>
<style type="text/css">
img {
	border: 0 none;
	width: 100px;
}

a {
	text-decoration: none;
}
</style>
<body>
	<a href="imagelist.php">Zur Bilderliste</a>
	<a href="logout.php">Logout</a><br />
	<br />
	<form enctype="multipart/form-data" action="upload.php" method="post">
		Bild: <input type="file" name="fileupload[]" /><br />
		<input type="Submit" value="Submit" />
		<input type="hidden" name="submitted" value="true" />
	</form>
<?php
include('lib/config.php');
include('lib/mysql.php');
$con = mysql_connect($host, $user, $password);
mysql_select_db($db);
$filedir = dirname(__FILE__).'/images/';
$maxfile = 3145728;
if ($_POST['submitted'] == 'true')
{
	$userfile_name = $_FILES['fileupload']['name'][0];
	$userfile_tmp = $_FILES['fileupload']['tmp_name'][0];
	$userfile_size = $_FILES['fileupload']['size'][0];
	$userfile_type = $_FILES['fileupload']['type'][0];
	if (!$userfile_name)
	{
?>
	Keine Datei ausge&auml;hlt.<br/>
<?php
	}
	else
	{
		if ($userfile_size > $maxfile)
		{
?>
	Die Datei ist zu gro&szlig;.<br/>
	Die Dateigr&ouml;&szlig;e betr&auml;gt: <?php echo number_format($userfile_size, 0, ',', '.') ?> Byte.<br/>
	Erlaubt sind <?php echo number_format(round($maxfile / 1024, 0), 0, ',', '.') ?> KB.
<?php
			exit;
		}

		if ($userfile_type == 'image/gif' or $userfile_type == 'image/jpeg' or $userfile_type == 'image/png')
		{
			$userfile_size=round($userfile_size/1024);
			$sql = '
				INSERT INTO imagelist (
					imagename,
					mimetype,
					add_datetime,
					userID
				) VALUES (
					"'.mysql_real_escape_string($userfile_name).'",
					"'.mysql_real_escape_string($userfile_type).'",
					NOW(),
					'.mysql_real_escape_string($_SESSION['user']).'
				)
			';
			$id = mysqlQuery($sql);

			if ($filedir)
				$location = $filedir.$id;
			else
				$location = $id;

			move_uploaded_file($userfile_tmp, $location);
			chmod($location, 0644);

			if ($userfile_type == 'image/jpeg')
				$image = imagecreatefromjpeg($location);
			elseif ($userfile_type == 'image/png')
				$image = imagecreatefrompng($location);
			elseif ($userfile_type == 'image/gif')
				$image = imagecreatefromgif($location);

			$image_width = imagesx($image);
			$image_height = imagesy($image);
			if ($image_width > 1024)
			{
				$new_image_width = 1024;
				$new_image_height = $image_height / ($image_width / 1024);
			}
			elseif ($image_height > 768)
			{
				$new_image_width = $image_width / ($image_height / 768);
				$new_image_height = 768;
			}
			else
			{
				$new_image_width = $image_width;
				$new_image_height = $image_height;
			}
			$new_image = imagecreatetruecolor($new_image_width, $new_image_height);
			imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width, $new_image_height, $image_width, $image_height);

			if ($userfile_type == 'image/jpeg')
				imagejpeg($new_image, $location);
			elseif ($userfile_type == 'image/png')
				imagpng($new_image, $location);
			elseif ($userfile_type == 'image/gif')
				imagegif($new_image, $location);

			imagedestroy($image);
			imagedestroy($new_image);

			$sql = '
				UPDATE imagelist
				SET hash = "'.md5_file($location).'"
				WHERE imagelist_id = '.mysql_real_escape_string($id).'
			';
			mysqlQuery($sql);

			$sql = '
				SELECT
					hash,
					DATE(add_datetime) date
				FROM imagelist
				WHERE imagelist_id = '.  mysql_real_escape_string($id).'
			';
			$image = mysqlQuery($sql);
?>
	Dateiname: <a href="index.php?image=<?php echo $image['hash'].$image['date']; ?>"><?php echo $userfile_name ?></a><br />
	Gr&ouml;&szlig;e: <?php echo number_format($userfile_size, 0, ',', '.') ?> KB<br />
	Link: http://images.wafriv.de/index.php?image=<?php echo $image['hash'].$image['date']; ?><br />
<?php
		}
	}
}
?>
</body>
</html>