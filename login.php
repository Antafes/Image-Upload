<?php
session_start();

if ($_SESSION['user'])
{
	header('Location: imagelist.php');
	die();
}

require_once('lib/config.php');
require_once('lib/mysql.php');

$con = mysql_connect($host, $user, $password);
mysql_select_db($db);

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
			WHERE nick = "'.mysql_real_escape_string($_POST['nick']).'"
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
					"'.mysql_real_escape_string($_POST['nick']).'",
					"'.mysql_real_escape_string(md5($_POST['password'])).'",
					"'.mysql_real_escape_string($_POST['email']).'",
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