<?php
/**
 * check the given hash and date against the values in the database
 * @param String $hash
 * @param DateTime $date
 * @param boolean $showImage
 * @param boolean $showThumb (default: false)
 * @return String
 */
function checkImage($hash, DateTime $date, $showImage, $showThumb = false)
{
	$sql = '
		SELECT
			imagelist_id,
			mimetype,
			DATE(add_datetime) add_date
		FROM imagelist
		WHERE hash = '.sqlval($hash).'
	';
	$imageArray = mysqlQuery($sql);

	if (DateTime::createFromFormat('Y-m-d', $imageArray['add_date']) == $date)
	{
		if ($showThumb)
		{
			if (file_exists(dirname(__FILE__).'/../thumbs/'.$imageArray['imagelist_id']))
			{
				if ($showImage)
					header('Content-type: '.$imageArray['mimetype']);

				return file_get_contents(dirname(__FILE__).'/../thumbs/'.$imageArray['imagelist_id']);
			}
			else
			{
				if ($showImage)
					header('Content-type: '.$imageArray['mimetype']);

				return file_get_contents(dirname(__FILE__).'/../images/'.$imageArray['imagelist_id']);
			}
		}
		else
		{
			if ($showImage)
				header('Content-type: '.$imageArray['mimetype']);

			return file_get_contents(dirname(__FILE__).'/../images/'.$imageArray['imagelist_id']);
		}
	}
	else
		return 'Kein Bild gefunden';
}

/**
 * processes the uploaded image and could return the following:
 * no_file: there was no file uploaded
 * too_big: the file is bigger then the allowed file size
 * file_not_allowed: the mime type of the uploaded image is not supported
 * an array: image was successfully uploaded and processed
 * @param array $file
 * @return string/array
 */
function processImage($file)
{
	$userfile_name = $file['name'][0];
	$userfile_tmp = $file['tmp_name'][0];
	$userfile_size = $file['size'][0];
	$userfile_type = $file['type'][0];

	if (!$userfile_name)
		return 'no_file';
	else
	{
		if ($userfile_size > $GLOBALS['config']['maxImageFileSize'])
			return 'too_big';

		if ($userfile_type == 'image/gif' || $userfile_type == 'image/jpeg' || $userfile_type == 'image/png')
		{
			$userfile_size=round($userfile_size/1024);
			$sql = '
				INSERT INTO imagelist (
					imagename,
					mimetype,
					add_datetime,
					userID
				) VALUES (
					'.sqlval($userfile_name).',
					'.sqlval($userfile_type).',
					NOW(),
					'.sqlval($_SESSION['user']).'
				)
			';
			$id = mysqlQuery($sql);

			$location = $GLOBALS['config']['imageDir'].$id;

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
				SET hash = '.sqlval(md5_file($location)).'
				WHERE imagelist_id = '.sqlval($id).'
			';
			mysqlQuery($sql);

			createThumbnail($id);

			$sql = '
				SELECT
					hash,
					DATE(add_datetime) date
				FROM imagelist
				WHERE imagelist_id = '.sqlval($id).'
			';
			return mysqlQuery($sql);
		}
		else
			return 'file_not_allowed';
	}
}

/**
 * create a thumbnail to the given imagelist_id
 * @param int $imagelist_id
 */
function createThumbnail($imagelist_id)
{
	$sql = '
		SELECT mimetype
		FROM imagelist
		WHERE imagelist_id = '.sqlval($imagelist_id).'
	';
	$mimetype = mysqlQuery($sql);

	$location = dirname(__FILE__).'/../'.$GLOBALS['config']['imageDir'].$imagelist_id;
	$thumbLocation = dirname(__FILE__).'/../'.$GLOBALS['config']['thumbsDir'].$imagelist_id;

	if ($mimetype == 'image/jpeg')
		$image = imagecreatefromjpeg($location);
	elseif ($mimetype == 'image/png')
		$image = imagecreatefrompng($location);
	elseif ($mimetype == 'image/gif')
		$image = imagecreatefromgif($location);

	$image_width = imagesx($image);
	$image_height = imagesy($image);

	if ($image_width > 100)
	{
		$new_image_width = 100;
		$new_image_height = $image_height / ($image_width / 100);
	}

	$new_image = imagecreatetruecolor($new_image_width, $new_image_height);
	imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_image_width, $new_image_height, $image_width, $image_height);

	if ($mimetype == 'image/jpeg')
		imagejpeg($new_image, $thumbLocation);
	elseif ($mimetype == 'image/png')
		imagpng($new_image, $thumbLocation);
	elseif ($mimetype == 'image/gif')
		imagegif($new_image, $thumbLocation);

	imagedestroy($image);
	imagedestroy($new_image);
}