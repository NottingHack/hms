<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.Lib.Error.Exception
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * An exception thrown by certain Member methods if they are called on a member id who has the wrong status.
 *
 * This is it's own exception as such results are usually handled differently from the calling code, for example the
 * displaying a flash message if the Member method returned true or false but redirecting if this exception is thrown.
 */
class InvalidStatusException extends CakeException {

}