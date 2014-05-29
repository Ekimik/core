<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         2.4.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Controller\Component\Auth;

use Cake\Controller\Component\Auth\AbstractPasswordHasher;
use Cake\Utility\Security;

/**
 * Blowfish password hashing class.
 *
 * @deprecated
 */
class BlowfishPasswordHasher extends AbstractPasswordHasher {

/**
 * Generates password hash.
 *
 * @param string $password Plain text password to hash.
 * @return string Password hash
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#using-bcrypt-for-passwords
 */
	public function hash($password) {
		return Security::hash($password, 'blowfish', false);
	}

/**
 * Returns true if the password need to be rehashed, due to the password being
 * created with anything else than the passwords generated by this class.
 *
 * @param string $password The password to verify
 * @param mixed $hashType the algorithm used to hash the password
 * @return boolean
 */
	public function check($password, $hashedPassword) {
		return $hashedPassword === Security::hash($password, 'blowfish', $hashedPassword);
	}

/**
 * Returns true if the password need to be rehashed, due to the password being
 * created with anything else than the passwords generated by this class.
 *
 * @param string $password The password to verify
 * @return boolean
 */
	public function needsRehash($password) {
		return password_needs_rehash($password, PASSWORD_BCRYPT);
	}

}
