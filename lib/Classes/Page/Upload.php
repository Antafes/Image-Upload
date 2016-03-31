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
namespace Page;

/**
 * Class for the upload page.
 *
 * @package    Image Upload
 * @subpackage Page
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Upload extends \SmartWork\Page
{
	/**
	 * Set the used template.
	 */
	public function __construct()
	{
		parent::__construct('upload');
	}

	/**
	 * Add javascripts and show the list of the images of the currently logged in user.
	 *
	 * @return void
	 */
	public function process()
	{
		$this->upload((array) $_FILES['fileupload'], strval($_POST['upload']));
	}

	/**
	 * Upload a file.
	 *
	 * @param array  $file
	 * @param string $salt
	 *
	 * @return void
	 */
	protected function upload(array $file, string $salt)
	{
		if (!$salt || $salt != $_SESSION['formSalts']['upload'])
			return;

		$result = \Model\Image::upload($file);
		$userfile_name = $file['name'][0];
		$userfile_size = $file['size'][0];
		$translator = $this->getTemplate()->getTranslator();

		switch ($result) {
			case 'no_file':
				$text = $translator->gt('noFileSelected');
				break;
			case 'too_big':
				$text = $translator->gt(
					'fileTooBig',
					array(
						'filesize' => number_format($userfile_size, 0, ',', '.'),
						'maxfilesize' => number_format(round($GLOBALS['config']['maxImageFileSize'] / 1024, 0), 0, ',', '.'),
					)
				);
				break;
			case 'file_not_allowed':
				$text = $translator->gt('fileTypeNotAllowed');
				break;
			default:
				$text = $translator->gt(
					'fileUploaded',
					array(
						'imagekey' => $result->getHash().$result->getAddDateTime()->format('Y-m-d'),
						'filename' => $userfile_name,
						'filesize' => $userfile_size,
						'filelink' => $GLOBALS['config']['dir_ws'].'/?page=Image&image='.$result->getHash().$result->getAddDateTime()->format('Y-m-d')
					)
				);
				break;
		}

		$this->assign('resultMessage', $text);
	}
}
