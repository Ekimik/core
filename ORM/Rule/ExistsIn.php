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
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\ORM\Rule;

use Cake\Datasource\EntityInterface;

/**
 * Checks that the value provided in a field exists as the primary key of another
 * table.
 */
class ExistsIn {

/**
 * Constructor.
 *
 * @param string $field The field to check existance for.
 * @param object|string $repository The repository where the field will be looked for,
 * or the association name for the repository.
 */
	public function __construct($field, $repository) {
		$this->_field = $field;
		$this->_repository = $repository;
	}

/**
 * Performs the existance check
 *
 * @param \Cake\Datasource\EntityInterface $entity The entity form where to extract the fields
 * @param array $options Options passed to the check,
 * where the `repository` key is required.
 */
	public function __invoke(EntityInterface $entity, array $options) {
		if (is_string($this->_repository)) {
			$this->_repository = $options['repository']->association($this->_repository);
		}

		$conditions = array_combine(
			(array)$this->_repository->primaryKey(),
			$entity->extract((array)$this->_field)
		);
		return $this->_repository->exists($conditions);
	}

}
