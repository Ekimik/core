<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\Network\Exception;

/**
 * Represents an HTTP 406 error.
 *
 */
class NotAcceptableException extends HttpException
{

    /**
     * Constructor
     *
     * @param string $message If no message is given 'Not Acceptable' will be the message
     * @param int $code Status code, defaults to 406
     */
    public function __construct($message = null, $code = 406)
    {
        if (empty($message)) {
            $message = 'Not Acceptable';
        }
        parent::__construct($message, $code);
    }
}
