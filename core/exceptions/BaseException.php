<?php
namespace EventEspresso\Core\Exceptions;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class BaseException
 * extended by other exceptions
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 */
class BaseException extends \Exception {



	/**
	 * @param string     $message
	 * @param int        $code
	 * @param \Exception $previous
	 * @throws \EventEspresso\Core\Exceptions\BaseException
	 */
	public function __construct( $message, $code = 0, \Exception $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}



}
// End of file BaseException.php
// Location: /core/exceptions/BaseException.php