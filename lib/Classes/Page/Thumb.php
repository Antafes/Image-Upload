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
namespace Page;

/**
 * Class for the thumbnail image page.
 *
 * @package    Image Upload
 * @subpackage Page
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Thumb extends \SmartWork\Page
{
	/**
	 * Nothing to do here
	 */
	public function __construct()
	{
	}

	/**
	 * Add javascripts and show the list of the images of the currently logged in user.
	 *
	 * @return void
	 */
	public function process()
	{
		$hash = substr($_GET['image'], 0, -10);
		$date = substr($_GET['image'], -10);

		$image = \Model\Image::loadByHash($hash);
		echo $image->getImage($hash, $date, true, true);
	}

	/**
	 * Nothing to render, all is done in the process method.
	 *
	 * @return void
	 */
	public function render()
	{
	}
}
