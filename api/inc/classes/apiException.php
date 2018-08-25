<?php
/**
 * File, that contains <apiException> throwable
 *
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */

/**
 * Custom exception
 *
 * Terminates the script and displays error, if wasn't catched
 *
 * @package Temply-Account\Core
 * @author Sergey Dilong
 * @license GPL-2.0
 *
 */
class apiException extends Exception
{

    /** @var array $o Additional options
     * @see apiException::getO() Get this field outside the class
     */
    private $o = [];
    /** @var int $APIcode Recieved error code
     * @see apiException::getAPICode() Get this field outside the class
     */
    private $APIcode;

    /**
     * Throw the Exception
     *
     * Define APIError code and options to terminate the script and show an error message to user
     *
     * It's used to stop execution and show an error to user if happens something critical.
     * In some cases classes/methods/functions (such as {@see User class User}) can throw this object.
     * If you would like to prevent terminating and error displaying you can catch the exception
     *
     * @example "inc/classes/User.php" 93 4 The Exception could be throwed by statements
     * @example methods/Users/Get.php 38 7 Catching an exception to do other tasks
     *
     * @param int $code API Error code
     * @param array $o Additional fields for error
     * @return void The script terminates
     * @see api::error() Error init
     * @see api::get_error() Get error message
     */
    public function __construct($code, $o = [])
    {
        // Set vars
        $this->APIcode = $code;
        $this->o = $o;
        // Construct parrent Exception
        parent::__construct("Error $code", $code);
    }

    /**
     * Error Extended
     *
     * Get API Error Extended details, if they were provided.
     * Used for internal purposes.
     *
     * @return array Extended details
     * @see api::get_error() Error generation
     * @uses $this->o to get Extended Error
     */
    public function getO()
    {
        return $this->o;
    }

    /**
     * Error Code
     *
     * Get Error code, which was provided by Exception thrower.
     * Used for internal purposes.
     *
     * @return int The API Error code
     * @see api::get_error() Error generation
     * @uses $this->APIcode to get error code
     */
    public function getAPICode()
    {
        return $this->APIcode;
    }
}
