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
 * Class for the login page.
 *
 * @package    Image Upload
 * @subpackage Page
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Login extends \SmartWork\Page
{
	/**
	 * Set the used template.
	 */
	function __construct()
	{
		parent::__construct('login');
	}

	/**
	 * Process the login.
	 *
	 * @return void
	 */
	public function process()
	{
		$this->logIn(strval($_POST['username']), strval($_POST['password']), strval($_POST['login']));
	}

	/**
	 * Login process with check for the form salt, existing users and a password check.
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $salt
	 *
	 * @return void
	 */
	protected function logIn(string $username, string $password, string $salt)
	{
		if (!$salt || $salt != $_SESSION['formSalts']['login'])
			return;

		if (!$username && !$password)
		{
			$this->template->assign('error', 'emptyLogin');
			return;
		}

		$user = \SmartWork\User::getUser($username, $password);

		if ($user)
		{
			var_dump($user->getUserId());
			$_SESSION['userId'] = $user->getUserId();
			$translator = \SmartWork\Translator::getInstance();
			$translator->setCurrentLanguage($user->getLanguageId());
			redirect('index.php?page=Index');
		}
		else
			$this->template->assign('error', 'invalidLogin');
	}
}
