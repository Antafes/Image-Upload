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
 * Class for the registration page.
 *
 * @package    Image Upload
 * @subpackage Page
 * @author     Marian Pollzien <map@wafriv.de>
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Register extends \SmartWork\Page
{
	/**
	 * Set the used template.
	 */
	public function __construct()
	{
		parent::__construct('register');
	}

	/**
	 * Process the registration.
	 *
	 * @return void
	 */
	public function process()
	{
		$this->register(
            strval($_POST['username']),
            strval($_POST['password']),
            strval($_POST['repeatPassword']),
            strval($_POST['email']),
			strval($_POST['register'])
		);
	}

	/**
	 * Handle the registration process includign the form salt check.
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $repeatPassword
	 * @param string $email
	 * @param string $salt
	 *
	 * @return void
	 */
	protected function register(string $username, string $password, string $repeatPassword, string $email, string $salt)
	{
		if (!$salt || $salt != $_SESSION['formSalts']['register'])
			return;

		if (!$username || !$password || !$repeatPassword || !$email)
		{
			$this->template->assign('error', 'registerEmpty');
			return;
		}

		if ($password !== $repeatPassword)
		{
			$this->template->assign('error', 'passwordsDontMatch');
			return;
		}

		if (\SmartWork\User::checkUsername($username))
		{
			$this->template->assign('error', 'usernameAlreadyInUse');
			return;
		}

		if (\SmartWork\User::checkEmail($email))
		{
			$this->template->assign('error', 'emailAlreadyInUse');
			return;
		}

		if (\SmartWork\User::createUser($username, $password, $email))
			$this->template->assign('message', 'registrationSuccessful');
		else
			$this->template->assign('error', 'registrationUnsuccessful');
	}
}
