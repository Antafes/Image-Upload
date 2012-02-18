<?php
session_start();

if (!$_SESSION['user'])
{
	header('Location: login.php');
	die();
}

require_once(dirname(__FILE__).'/lib/mysql.php');
require_once(dirname(__FILE__).'/lib/functions.php');
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
<body id="upload">
<?php
require_once('menu.php');
?>
	<br />
	<form enctype="multipart/form-data" action="upload.php" method="post">
		Bild: <input type="file" name="fileupload[]" /><br />
		<input type="Submit" value="Submit" />
		<input type="hidden" name="submitted" value="true" />
	</form>
<?php
if ($_POST['submitted'] == 'true')
{
	$result = processImage($_FILES['fileupload']);
	$userfile_name = $_FILES['fileupload']['name'][0];
	$userfile_size = $_FILES['fileupload']['size'][0];

	switch ($result) {
		case 'no_file':
			$text = 'Keine Datei ausgewählt.<br />';
			break;
		case 'too_big':
			$text = 'Die Datei ist zu groß.<br />';
			$text .= 'Die Dateigröße beträgt: '.number_format($userfile_size, 0, ',', '.').' Byte.<br />';
			$text .= 'Erlaubt sind '.number_format(round($GLOBALS['config']['maxImageFileSize'] / 1024, 0), 0, ',', '.').' KB.';
			break;
		case 'file_not_allowed':
			$text = 'Dieser Dateityp ist nicht erlaubt.<br />';
			$text .= 'Folgende Dateitypen können hochgeladen werden:<br />';
			$text .= 'GIF, JPEG, PNG';
			break;
		default:
			$text = 'Dateiname: <a href="index.php?image='.$result['hash'].$result['date'].'">'.$userfile_name.'</a><br />';
			$text .= 'Größe: '.number_format($userfile_size, 0, ',', '.').' KB<br />';
			$text .= 'Link: http://images.wafriv.de/index.php?image='.$result['hash'].$result['date'].'<br />';
			break;
	}

	echo utf8_encode($text);
}
?>
</body>
</html>