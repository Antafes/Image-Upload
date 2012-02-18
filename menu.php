<?php
$sql = '
	SELECT admin
	FROM users
	WHERE userID = '.sqlval($_SESSION['user']).'
';
$admin = mysqlQuery($sql);
?>
<a href="imagelist.php">Zur Bilderliste</a>
<a href="upload.php">Zum Upload</a>
<?php
if ($admin)
{
?>
<a href="admin.php">Admin</a>
<?php
}
?>
<a href="logout.php">Logout</a>