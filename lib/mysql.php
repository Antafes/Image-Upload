<?php
require_once(dirname(__FILE__).'/config.php');

/**
 * handles the mysql_query
 * if the query is a select, it returns an array if there is only one value, otherwise it returns the value
 * if the query is an update, replace or delete from, it returns the number of affected rows
 * if the query is an insert, it returns the last insert id
 * @author Neithan
 * @param string $sql
 * @param bool $noTransform (default = false) if set to "true" the query function always returns a multidimension array
 * @return array|string|int|float
 */
function mysqlQuery($sql, $noTransform = false)
{
	global $debug;
	static $con;

	if (!$con)
	{
		$con = mysql_connect($GLOBALS['db']['host'], $GLOBALS['db']['user'], $GLOBALS['db']['password']);
		mysql_select_db($GLOBALS['db']['db'], $con);
	}

	$sql = ltrim($sql);
	if ($debug == true)
		$res = mysql_query($sql, $con);
	else
		$res = @mysql_query($sql, $con);
	if (!$res)
	{
		$backtrace = debug_backtrace();
		$html = '<br />Datenbank Fehler '.mysql_error().'<br /><br />';
		$html .= $sql.'<br />';
		$html .= '<table>';
		foreach ($backtrace as $part)
		{
			$html .= '<tr><td width="100">';
			$html .= 'File: </td><td>'.$part['file'];
			$html .= ' in line '.$part['line'];
			$html .= '</td></tr><tr><td>';
			$html .= 'Function: </td><td>'.$part['function'];
			$html .= '</td></tr><tr><td>';
			$html .= 'Arguments: </td><td>';
			foreach ($part['args'] as $args)
				$html .= $args.', ';
			$html = substr($html, 0, -2);
			$html .= '</td></tr>';
		}
		$html .= '</table>';
		die($html);
	}

	if ($res)
	{

		if (substr($sql,0,6) == "SELECT")
		{
			$out = array();
			if (mysql_num_rows($res) > 1 or $noTransform)
			{
				while($line = mysql_fetch_array($res,MYSQL_ASSOC))
					$out[] = $line;
			}
			elseif (mysql_num_rows($res) == 1 and !$noTransform)
			{
				$out = mysql_fetch_array($res,MYSQL_ASSOC);
				if (count($out) == 1)
					$out = current($out);
			}
			else
				$out = false;
			return $out;
		}

		if (substr($sql,0,6) == "INSERT" and $noTransform == false)
		    return mysql_insert_id($con);
		elseif (substr($sql,0,6) == "INSERT" and $noTransform == true)
			return mysql_affected_rows($con);

		if (substr($sql,0,6) == "UPDATE")
			return mysql_affected_rows($con);

		if (substr($sql,0,7) == "REPLACE")
			return mysql_affected_rows($con);

		if (substr($sql,0,11) == "DELETE FROM")
			return mysql_affected_rows($con);
	}
	else
		return false;
}

/**
 * escapes a value for sql queries
 * @param mixed $value
 * @return String
 */
function sqlval($value)
{
	return '"'.addslashes($value).'"';
}