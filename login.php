<?php
session_start();

if ($_SESSION['user'])
{
	header('Location: imagelist.php');
	die();
}

require_once('lib/mysql.php');

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
<body id="login">
<?php
if (!$_GET['mode'])
{
	if ($_POST['nick'] && $_POST['password'])
	{
		$sql = '
			SELECT
				userID,
				nick,
				password
			FROM users
			WHERE nick = '.sqlval($_POST['nick']).'
		';
		$user = mysqlQuery($sql);

		if (md5($_POST['password']) === $user['password'])
		{
			$_SESSION['user'] = $user['userID'];
			header('Location: imagelist.php');
			die();
		}
	}
?>
<form method="post" action="login.php">
	<table>
		<tr>
			<td>Benutzer:</td>
			<td>
				<input type="text" name="nick" />
			</td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td>
				<input type="password" name="password" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="Login" />
			</td>
		</tr>
	</table>
	<br />
	<a href="login.php?mode=registration">Registrieren</a>
</form>
<?php
}
else
{
	if ($_POST['nick'] && $_POST['password'] && $_POST['repeat_password'] && $_POST['email'])
	{
		if ($_POST['password'] === $_POST['repeat_password'])
		{
			$sql = '
				INSERT INTO users (
					nick,
					password,
					email,
					createdDatetime
				) VALUES (
					'.sqlval($_POST['nick']).',
					'.sqlval(md5($_POST['password'])).',
					'.sqlval($_POST['email']).',
					NOW()
				)
			';
			$userID = mysqlQuery($sql);

			$_SESSION['user'] = $userID;
			header('Location: imagelist.php');
			die();
		}
	}
?>
<form method="post" action="login.php?mode=registration">
	<table>
		<tr>
			<td>Benutzer:</td>
			<td>
				<input type="text" name="nick" />
			</td>
		</tr>
		<tr>
			<td>E-Mail:</td>
			<td>
				<input type="text" name="email" />
			</td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td>
				<input type="password" name="password" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="password" name="repeat_password" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="Registrieren" />
			</td>
		</tr>
	</table>
</form>
<?php
}
?>
</body>
</html>