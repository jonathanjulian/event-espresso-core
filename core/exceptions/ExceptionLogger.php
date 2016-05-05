<?php
namespace EventEspresso\Core\Exceptions;

use Exception;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class ExceptionLogger
 * logs exceptions
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 */
class ExceptionLogger {

	/**
	 * @param \Exception $exception
	 */
	protected $exception;


	/**
	 * @var string $log_file_name
	 */
	protected $log_file_name = 'espresso_error_log.txt';



	/**
	 * ExceptionLogger constructor.
	 *
	 * @param Exception $exception
	 * @param string    $log_file_name
	 * @throws InvalidDataTypeException
	 */
	public function __construct( Exception $exception, $log_file_name = 'espresso_error_log.txt' ) {
		$this->exception = $exception;
		$this->setLogFileName( $log_file_name );
	}



	/**
	 * @param string $log_file_name
	 * @throws InvalidDataTypeException
	 */
	public function setLogFileName( $log_file_name ) {
		if ( ! is_string( $log_file_name ) ) {
			throw new InvalidDataTypeException( '$log_file_name', $log_file_name, 'string' );
		}
		$this->log_file_name = $log_file_name;
	}




	/**
	 * write exception details to log file
	 *
	 * @param bool  $clear
	 */
	public function log( $clear = false ) {
		if ( ! $this->exception instanceof Exception ) {
			return;
		}
		$time = time();
		$exception_log = '----------------------------------------------------------------------------------------'
		                 . PHP_EOL;
		$exception_log .= '[' . date( 'Y-m-d H:i:s', $time ) . ']  Exception Details' . PHP_EOL;
		$exception_log .= 'Message: ' . $this->exception->getMessage() . PHP_EOL;
		$exception_log .= 'Code: ' . $this->exception->getCode() . PHP_EOL;
		$exception_log .= 'File: ' . $this->exception->getFile() . PHP_EOL;
		$exception_log .= 'Line No: ' . $this->exception->getLine() . PHP_EOL;
		$exception_log .= 'Stack trace: ' . PHP_EOL;
		$exception_log .= $this->exception->getMessage() . PHP_EOL;
		$exception_log .= '----------------------------------------------------------------------------------------'
		                  . PHP_EOL;
		try {
			\EEH_File::ensure_file_exists_and_is_writable(
				EVENT_ESPRESSO_UPLOAD_DIR . 'logs' . DS . $this->log_file_name
			);
			\EEH_File::add_htaccess_deny_from_all( EVENT_ESPRESSO_UPLOAD_DIR . 'logs' );
			if ( ! $clear ) {
				//get existing log file and append new log info
				$exception_log = \EEH_File::get_file_contents(
						EVENT_ESPRESSO_UPLOAD_DIR . 'logs' . DS . $this->log_file_name
					) . $exception_log;
			}
			\EEH_File::write_to_file(
				EVENT_ESPRESSO_UPLOAD_DIR . 'logs' . DS . $this->log_file_name,
				$exception_log
			);
		} catch ( \Exception $e ) {
			$handler = new \EventEspresso\Core\Exceptions\ExceptionHandler( $e );
			$handler->display();
		}
	}


}
// End of file ExceptionLogger.php
// Location: /ExceptionLogger.php