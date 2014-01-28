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
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\View\Form;

use Cake\Network\Request;
use Cake\Utility\Hash;

/**
 * Provides a basic array based context provider for FormHelper
 * this adapter is useful in testing or when you have forms backed by
 * by simple array data structures.
 *
 * Important keys:
 *
 * - `defaults` The default values for fields. These values
 *   will be used when there is no request data set.
 * - `required` A nested array of fields, relationships and boolean
 *   flags to indicate a field is required.
 * - `schema` An array of data that emulate the structures that
 *   Cake\Database\Schema\Table uses. This array allows you to control
 *   the inferred type for fields and allows auto generation of attributes
 *   like maxlength, step and other HTML attributes.
 */
class ArrayContext {

	protected $_request;
	protected $_context;

	public function __construct(Request $request, array $context) {
		$this->_request = $request;
		$this->_context = $context;
	}

/**
 * Get the current value for a given field.
 *
 * This method will coalesce the current request data and the 'defaults'
 * array.
 *
 * @param string $field A dot separated path to the field a value
 *   is needed for.
 * @return mixed
 */
	public function val($field) {
	}

/**
 * Check if a given field is 'required'.
 *
 * In this context class, this is simply defined by the 'required' array.
 *
 * @param string $field A dot separated path to check required-ness for.
 * @return boolean
 */
	public function isRequired($field) {
	}

/**
 * Get the abstract field type for a given field name.
 *
 * @param string $field A dot separated path to get a schema type for.
 * @return string An abstract data type.
 * @see Cake\Database\Type
 */
	public function type($field) {

	}

/**
 * Get an associative array of other attributes for a field name.
 *
 * @param string $field A dot separated path to get a additional data on.
 * @return array An array of data describing the additional attributes on a field.
 */
	public function attributes($field) {

	}

}
