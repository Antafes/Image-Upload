<?php
/**
 * This file is part of Image Upload.
 *
 * Image Upload is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Image Upload is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Image Upload.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   Image Upload
 * @author    Marian Pollzien <map@wafriv.de>
 * @copyright (c) 2015, $(copyrightUser}
 * @license   https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
namespace Listing;

/**
 * List class for images
 *
 * @package    Image Upload
 * @subpackage Listing
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Images extends \SmartWork\Listing
{
	/**
	 * Load all images.
	 *
	 * @param integer $userId
	 *
	 * @return \self
	 */
	public static function loadList()
	{
		$sql = '
			SELECT `imageId`
			FROM images
			WHERE !deleted
				AND `userId` = '.\sqlval($_SESSION['userId']).'
			ORDER BY imagename
		';
		$imageIds = query($sql, true);
		$obj = new self();

		if (empty($imageIds))
		{
			return $obj;
		}

		$list = array();
		foreach ($imageIds as $image)
		{
			$imageObject = \Model\Image::loadById($image['imageId']);

			$list[$imageObject->getImageId()] = $imageObject;
		}

		$obj->setList($list);

		return $obj;
	}

	/**
	 * Get a single image by the given id.
	 *
	 * @param integer $id
	 *
	 * @return \Model\Image
	 */
	public function getById($id)
	{
		return $this->list[$id];
	}

	/**
	 * Recreate all thumbnails.
	 *
	 * @return void
	 */
	public function reCreateThumbnails()
	{
		$path = dirname(__FILE__).'/../../../'.$GLOBALS['config']['thumbsDir'].'/';
		$directory = opendir($path);

		while ($element = readdir($directory))
		{
			if ($element != '.' && $element != '..' && $element != '.htaccess')
			{
				unlink($path.$element);
			}
		}

		closedir($directory);

		/* @var $image \Model\Image */
		foreach ($this->list as $image)
		{
			$image->createThumbnail();
		}
	}
}

