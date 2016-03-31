<?php
declare(strict_types=1);
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
namespace Model;

/**
 * Model class for the images.
 *
 * @package    Image Upload
 * @subpackage Model
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Image extends \SmartWork\Model
{
	/**
	 * The path to the thumbnail directory.
	 *
	 * @var string
	 */
	protected $thumbsDirectory;

	/**
	 * The path to the image directory.
	 *
	 * @var string
	 */
	protected $imageDirectory;

	/**
	 * @var integer
	 */
	protected $imageId;

	/**
	 * @var \SmartWork\User
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $imagename;

	/**
	 * @var string
	 */
	protected $mimetype;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var \DateTime
	 */
	protected $addDateTime;

	/**
	 * @var boolean
	 */
	protected $deleted;

	/**
	 * Create a new image model object.
	 */
	function __construct()
	{
		$directory = dirname(__FILE__).'/../../../';
		$this->thumbsDirectory = $directory.$GLOBALS['config']['thumbsDir'].'/';
		$this->imageDirectory = $directory.$GLOBALS['config']['imageDir'].'/';
	}

		/**
	 * Load an image by its id.
	 *
	 * @param integer $id
	 *
	 * @return \self
	 */
	public static function loadById(int $id): Image
	{
		$sql = '
			SELECT
				`imageId`,
				`userId`,
				imagename,
				mimetype,
				`hash`,
				add_datetime,
				deleted
			FROM images
			WHERE `imageId` = '.\sqlval($id).'
		';
		$data = \query($sql);
		$obj = new self();
		$obj->fill($data);

		return $obj;
	}

	/**
	 * Load an image by its hash.
	 *
	 * @param string $hash
	 *
	 * @return \self
	 */
	public static function loadByHash(string $hash): Image
	{
		$sql = '
			SELECT
				`imageId`,
				`userId`,
				imagename,
				mimetype,
				`hash`,
				add_datetime,
				deleted
			FROM images
			WHERE `hash` = '.\sqlval($hash).'
		';
		$data = \query($sql);
		$obj = new self();
		$obj->fill($data);

		return $obj;
	}

	/**
	 * Fill the objects properties with the given data and cast them if possible to the best
	 * matching type. Only existing properties are filled.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function fill(array $data)
	{
		foreach ($data as $key => $value)
		{
			if ($key === 'userId')
			{
				$this->user = \SmartWork\User::getUserById(intval($value));
			}
			elseif ($key === 'add_datetime')
			{
				$this->addDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
			}
			elseif ($key === 'deleted')
			{
				$this->deleted = boolval($value);
			}
			elseif (property_exists($this, $key))
			{
				$this->$key = $this->castToType($value);
			}
		}
	}

	/**
	 * Get the whole properties as an array.
	 *
	 * @return array
	 */
	public function getAsArray(): array
	{
		return array(
			'imageId' => $this->getImageId(),
			'user' => $this->getUser(),
			'imagename' => $this->getImagename(),
			'mimetype' => $this->getMimetype(),
			'hash' => $this->getHash(),
			'addDateTime' => $this->getAddDateTime(),
		);
	}

	/**
	 * @return integer
	 */
	public function getImageId(): int
	{
		return $this->imageId;
	}

	/**
	 * @return \SmartWork\User
	 */
	public function getUser(): \SmartWork\User
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getImagename(): string
	{
		return $this->imagename;
	}

	/**
	 * @return string
	 */
	public function getMimetype(): string
	{
		return $this->mimetype;
	}

	/**
	 * @return string
	 */
	public function getHash(): string
	{
		return $this->hash;
	}

	/**
	 * @return \DateTime
	 */
	public function getAddDateTime(): \DateTime
	{
		return $this->addDateTime;
	}

	/**
	 * Check the given hash and date against the values in the database
	 *
	 * @param string  $hash
	 * @param string  $date
	 * @param boolean $showImage defautl: true
	 * @param boolean $showThumb default: false
	 *
	 * @return string
	 */
	public function getImage(string $hash, string $date, bool $showImage = true, bool $showThumb = false): string
	{
		$translator = \SmartWork\Translator::getInstance();

		if ($this->deleted)
		{
			header('Content-type: image/png');
			$image = imagecreatetruecolor(400, 100);
			imagefill($image, 0, 0, imagecolorallocatealpha($image, 255, 255, 255, 0));
			imagettftext(
				$image, 12, 0, 100, 56,
				imagecolorallocate(
					$image, 0, 0, 0
				),
				'lib/arial.ttf', $translator->gt('imageIsDeleted')
			);
			imagepng($image);
			imagedestroy($image);

			return null;
		}

		if ($this->getAddDateTime()->format('Y-m-d') == $date)
		{
			header('Content-Disposition: inline; filename="'.$this->getImagename().'"');

			if ($showThumb)
			{
				if (file_exists($this->thumbsDirectory.$this->getImageId()))
				{
					if ($showImage)
					{
						header('Content-type: '.$this->getMimetype());
					}

					return file_get_contents($this->thumbsDirectory.$this->getImageId());
				}
				else
				{
					if ($showImage)
					{
						header('Content-type: '.$this->getMimetype());
					}

					return file_get_contents($this->imageDirectory.$this->getImageId());
				}
			}
			else
			{
				if ($showImage)
				{
					header('Content-type: '.$this->getMimetype());
				}

				return file_get_contents($this->imageDirectory.$this->getImageId());
			}
		}
		else
			return $translator->gt('noImageFound');
	}

	/**
	 * Create a thumbnail to the given imagelist_id
	 *
	 * @return void
	 */
	public function createThumbnail()
	{
		$location = $this->imageDirectory.$this->getImageId();
		$thumbLocation = $this->thumbsDirectory.$this->getImageId();

		switch ($this->getMimetype())
		{
			case 'image/jpeg':
				$image = imagecreatefromjpeg($location);
				break;
			case 'image/png':
				$image = imagecreatefrompng($location);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($location);
				break;
		}

		$image_width = imagesx($image);
		$image_height = imagesy($image);

		if ($image_width > 100)
		{
			$new_image_width = 100;
			$new_image_height = intval($image_height / ($image_width / 100));
		}

		$new_image = imagecreatetruecolor($new_image_width, $new_image_height);
		imagecopyresampled(
			$new_image, $image, 0, 0, 0, 0,
			$new_image_width, $new_image_height, $image_width, $image_height
		);

		switch ($this->getMimetype())
		{
			case 'image/jpeg':
				imagejpeg($new_image, $thumbLocation);
				break;
			case 'image/png':
				imagepng($new_image, $thumbLocation);
				break;
			case 'image/gif':
				imagegif($new_image, $thumbLocation);
				break;
		}

		chmod($thumbLocation, 0644);

		imagedestroy($image);
		imagedestroy($new_image);
	}

	/**
	 * Processes the uploaded image
	 *
	 * @param array $file
	 *
	 * @return \self|array Possible return values:
	 *						no_file: there was no file uploaded
	 *						too_big: the file is bigger then the allowed file size
	 *						file_not_allowed: the mime type of the uploaded image is not supported
	 *						self: image was successfully uploaded and processed
	 */
	public static function upload(array $file)
	{
		$userfile_name = $file['name'][0];
		$userfile_tmp = $file['tmp_name'][0];
		$userfile_size = $file['size'][0];
		$userfile_type = $file['type'][0];

		if (empty($userfile_type)) {
			$fileNameParts = explode('.', $userfile_name);

			switch ($fileNameParts[count($fileNameParts) - 1]) {
				case 'png':
					$userfile_type = 'image/png';
					break;
				case 'jpg':
				case 'jpeg':
					$userfile_type = 'image/jpeg';
					break;
				case 'gif':
					$userfile_type = 'image/gif';
					break;
			}
		}

		if (!$userfile_name)
		{
			return 'no_file';
		}

		if ($userfile_size > $GLOBALS['config']['maxImageFileSize'])
		{
			return 'too_big';
		}

		if ($userfile_type != 'image/gif' && $userfile_type != 'image/jpeg'
			&& $userfile_type != 'image/png')
		{
			return 'file_not_allowed';
		}

		$userfile_size = round($userfile_size / 1024);
		$sql = '
			INSERT INTO images (
				imagename,
				mimetype,
				add_datetime,
				userId
			) VALUES (
				'.sqlval($userfile_name).',
				'.sqlval($userfile_type).',
				NOW(),
				'.sqlval($_SESSION['userId']).'
			)
		';
		$id = \query($sql);

		$location = $GLOBALS['config']['imageDir'].'/'.$id;
		move_uploaded_file($userfile_tmp, $location);
		chmod($location, 0644);

		switch ($userfile_type)
		{
			case 'image/jpeg':
				$image = imagecreatefromjpeg($location);
				break;
			case 'image/png':
				$image = imagecreatefrompng($location);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($location);
				break;
		}

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
		imagecopyresampled(
			$new_image, $image, 0, 0, 0, 0, $new_image_width, $new_image_height,
			$image_width, $image_height
		);

		switch ($userfile_type)
		{
			case 'image/jpeg':
				imagejpeg($new_image, $location);
				break;
			case 'image/png':
				imagepng($new_image, $location);
				break;
			case 'image/gif':
				imagegif($new_image, $location);
				break;
		}

		imagedestroy($image);
		imagedestroy($new_image);

		$sql = '
			UPDATE images
			SET hash = '.sqlval(md5_file($location)).'
			WHERE imageId = '.sqlval($id).'
		';
		\query($sql);

		$image = self::loadById($id);
		$image->createThumbnail();

		return $image;
	}
}
