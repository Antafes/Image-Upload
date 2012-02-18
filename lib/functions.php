<?php
function checkImage($hash, $date)
{
	$sql = '
		SELECT
			imagelist_id,
			mimetype,
			DATE(add_datetime) add_date
		FROM imagelist
		WHERE hash = "'.mysql_real_escape_string($hash).'"
	';
	$imageArray = mysqlQuery($sql);
	if ($imageArray['add_date'] == $date)
		return file_get_contents('images/'.$imageArray['imagelist_id']);
	else
		return 'Kein Bild gefunden';
}