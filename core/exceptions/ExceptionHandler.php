<?php
namespace EventEspresso\Core\Exceptions;

use Exception;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class ExceptionHandler
 * makes exceptions look purdy
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 */
class ExceptionHandler {

	/**
	 * @param \Exception $exception
	 */
	protected $exception;

	/**
	 * @param string $time
	 */
	protected $time;

	/**
	 * @param string $pretty_exception
	 */
	protected $pretty_exception;

	/**
	 * @param ExceptionLogger $logger
	 */
	protected $logger;



	/**
	 * ExceptionHandler constructor.
	 *
	 * @param \Exception      $exception
	 * @param ExceptionLogger $logger
	 */
	public function __construct( Exception $exception, ExceptionLogger $logger = null ) {
		$this->time = time();
		$this->exception = $exception;
		$this->logger = $logger;

	}



	public function display() {
		$this->format_error();
		if ( defined( 'DOING_AJAX' ) ) {
			echo json_encode( array( 'error' => $this->pretty_exception ) );
			exit();
		}
		echo $this->pretty_exception;
		exit();
	}



	public function get() {
		return $this->pretty_exception;
	}



	public function log() {
		if ( ! $this->logger instanceof ExceptionLogger ) {
			$this->logger = new ExceptionLogger( $this->exception );
		}
		$this->logger->log();
	}



	public function displayAndLog() {
		$this->log();
		$this->display();
	}



	/**
	 * format_error
	 *
	 * @access public
	 * @echo   string
	 */
	public function format_error() {
		$error_code = '';
		$trace_details = '';
		// get separate user and developer messages if they exist
		$msg = explode( '||', $this->exception->getMessage() );
		$user_msg = $msg[0];
		$dev_msg = isset( $msg[1] ) ? $msg[1] : $msg[0];
		$msg = WP_DEBUG ? $dev_msg : $user_msg;
		$backtrace = $this->exception->getTrace();
		$this->pretty_exception = ExceptionHandler::_exception_styles();
		$this->pretty_exception .= '
<div id="ee-error-message" class="error">';
		if ( ! WP_DEBUG ) {
			$this->pretty_exception .= '
	<p>';
		}
			// process trace info
			if ( empty( $backtrace ) ) {
				$trace_details .= __(
					'Sorry, but no trace information was available for this exception.',
					'event_espresso'
				);
			} else {
				$trace_details .= '
			<div id="ee-trace-details">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr>
					<th scope="col" align="right" style="width:2.5%;">#</th>
					<th scope="col" align="right" style="width:3.5%;">Line</th>
					<th scope="col" align="left" style="width:40%;">File</th>
					<th scope="col" align="left">' . __( 'Class', 'event_espresso' ) . '->' . __(
						'Method( arguments )',
						'event_espresso'
					) . '</th>
				</tr>';
				$last_on_stack = count( $backtrace ) - 1;
				// reverse array so that stack is in proper chronological order
				$sorted_trace = array_reverse( $backtrace );
				foreach ( $sorted_trace as $nmbr => $trace ) {
					$file = isset( $trace['file'] ) ? $trace['file'] : '';
					$class = isset( $trace['class'] ) ? $trace['class'] : '';
					$type = isset( $trace['type'] ) ? $trace['type'] : '';
					$function = isset( $trace['function'] ) ? $trace['function'] : '';
					$args = isset( $trace['args'] ) ? $this->_convert_args_to_string( $trace['args'] ) : '';
					$args = isset( $trace['args'] ) && count( $trace['args'] ) > 4 ? ' <br />' . $args . '<br />'
						: $args;
					$line = isset( $trace['line'] ) ? $trace['line'] : '';
					$corellian_bloodstripe = $nmbr % 2 !== 0 ? ' odd' : '';
					if ( empty( $file ) && ! empty( $class ) ) {
						$a = new \ReflectionClass( $class );
						$file = $a->getFileName();
						if ( empty( $line ) && ! empty( $function ) ) {
							$b = new \ReflectionMethod( $class, $function );
							$line = $b->getStartLine();
						}
					}
					if ( $nmbr === $last_on_stack ) {
						$file = $this->exception->getFile() !== '' ? $this->exception->getFile() : $file;
						$line = $this->exception->getLine() !== '' ? $this->exception->getLine() : $line;
						$error_code = self::generate_error_code( $file, $trace['function'], $line );
					}
					$file = \EEH_File::standardise_directory_separators( $file );
					$nmbr = ! empty( $nmbr ) ? $nmbr : '&nbsp;';
					$line = ! empty( $line ) ? $line : '&nbsp;';
					$file = ! empty( $file ) ? $file : '&nbsp;';
					$class_display = ! empty( $class ) ? $class : '';
					$type = ! empty( $type ) ? $type : '';
					$function = ! empty( $function ) ? $function : '';
					$args = ! empty( $args ) ? '( ' . $args . ' )' : '()';
					$trace_details .= '
					<tr>
						<td align="right" valign="top" class="'
					                  . $corellian_bloodstripe
					                  . '">'
					                  . $nmbr
					                  . '</td>
						<td align="right" valign="top" class="'
					                  . $corellian_bloodstripe
					                  . '">'
					                  . $line
					                  . '</td>
						<td align="left" valign="top" class="'
					                  . $corellian_bloodstripe
					                  . '">'
					                  . $file
					                  . '</td>
						<td align="left" valign="top" class="'
					                  . $corellian_bloodstripe
					                  . '">'
					                  . $class_display
					                  . $type
					                  . $function
					                  . $args
					                  . '</td>
					</tr>';
				}
				$trace_details .= '
			 </table>
			</div>';
			}
		$error_code = $this->exception->getCode() ? $this->exception->getCode() : $error_code;
			// add generic non-identifying messages for non-privileged users
			if ( ! WP_DEBUG ) {
				$this->pretty_exception .= '<span class="ee-error-user-msg-spn">'
				           . trim( $msg )
				           . '</span> &nbsp; <sup>'
				           . $error_code
				           . '</sup><br />';
			} else {
				// or helpful developer messages if debugging is on
				$this->pretty_exception .= '
		<div class="ee-error-dev-msg-dv">
			<p class="ee-error-dev-msg-pg">
				'
				           . sprintf(
					           __( '%1$sAn %2$s was thrown!%3$s code: %4$s', 'event_espresso' ),
					           '<strong class="ee-error-dev-msg-str">',
					           get_class( $this->exception ),
					           '</strong>  &nbsp; <span>',
					           $error_code . '</span>'
				           )
				           . '<br />
				<span class="big-text">"'
				           . trim( $msg )
				           . '"</span><br/>
				<a id="display-ee-error-trace'
				           . $this->time
				           . '" class="display-ee-error-trace-lnk small-text" rel="ee-error-trace'
				           . $this->time
				           . '">
					'
				           . __( 'click to view backtrace and class/method details', 'event_espresso' )
				           . '
				</a><br />
				'
				           . $this->exception->getFile()
				           . sprintf(
					           __( '%1$s( line no: %2$s )%3$s', 'event_espresso' ),
					           ' &nbsp; <span class="small-text lt-grey-text">',
					           $this->exception->getLine(),
					           '</span>'
				           )
				           . '
			</p>
			<div id="ee-error-trace'
				           . $this->time
				           . '-dv" class="ee-error-trace-dv" style="display: none;">
				'
				           . $trace_details;
				if ( ! empty( $class ) ) {
					$this->pretty_exception .= '
				<div style="padding:3px; margin:0 0 1em; border:1px solid #999; background:#fff; border-radius:3px;">
					<div style="padding:1em 2em; border:1px solid #999; background:#fcfcfc;">
						<h3>' . __( 'Class Details', 'event_espresso' ) . '</h3>';
					$a = new \ReflectionClass( $class );
					$this->pretty_exception .= '
						<pre>' . $a . '</pre>
					</div>
				</div>';
				}
				$this->pretty_exception .= '
			</div>
		</div>
		<br />';
			}

		// remove last linebreak
		// $this->pretty_exception = substr( $this->pretty_exception, 0, count( $this->pretty_exception ) - 7 );
		if ( ! WP_DEBUG ) {
			$this->pretty_exception .= '
	</p>';
		}
		$this->pretty_exception .= '
</div>';
		$this->pretty_exception .= self::_print_scripts( true );
	}



	/**
	 * generate string from exception trace args
	 *
	 * @param array $arguments
	 * @param int   $indent
	 * @param bool  $array
	 * @return string
	 */
	private function _convert_args_to_string( $arguments = array(), $indent = 0, $array = false ) {
		$args = array();
		$args_count = count( $arguments );
		if ( $args_count > 2 ) {
			$indent++;
			$args[] = '<br />';
		}
		$x = 0;
		foreach ( $arguments as $arg ) {
			$x++;
			for ( $i = 0; $i < $indent; $i++ ) {
				$args[] = ' &nbsp;&nbsp; ';
			}
			if ( is_string( $arg ) ) {
				if ( ! $array && strlen( $arg ) > 75 ) {
					$args[] = "<br />";
					for ( $i = 0; $i <= $indent; $i++ ) {
						$args[] = ' &nbsp;&nbsp; ';
					}
					$args[] = "'" . $arg . "'<br />";
				} else {
					$args[] = " '" . $arg . "'";
				}
			} elseif ( is_array( $arg ) ) {
				$arg_count = count( $arg );
				if ( $arg_count > 2 ) {
					$indent++;
					$args[] = " array(" . $this->_convert_args_to_string( $arg, $indent, true ) . ")";
					$indent--;
				} else if ( $arg_count === 0 ) {
					$args[] = " array()";
				} else {
					$args[] = " array( " . $this->_convert_args_to_string( $arg ) . " )";
				}
			} elseif ( $arg === null ) {
				$args[] = ' null';
			} elseif ( is_bool( $arg ) ) {
				$args[] = $arg ? ' true' : ' false';
			} elseif ( is_object( $arg ) ) {
				$args[] = get_class( $arg );
			} elseif ( is_resource( $arg ) ) {
				$args[] = get_resource_type( $arg );
			} else {
				$args[] = $arg;
			}
			if ( $x === $args_count ) {
				if ( $args_count > 2 ) {
					$args[] = "<br />";
					$indent--;
					for ( $i = 1; $i < $indent; $i++ ) {
						$args[] = ' &nbsp;&nbsp; ';
					}
				}
			} else {
				$args[] = $args_count > 2 ? ",<br />" : ', ';
			}
		}
		return implode( '', $args );
	}



	/**
	 * create error code from filepath, function name,
	 * and line number where exception or error was thrown
	 *
	 * @access public
	 * @param string $file
	 * @param string $func
	 * @param string $line
	 * @return string
	 */
	public static function generate_error_code( $file = '', $func = '', $line = '' ) {
		$file_bits = explode( '.', basename( $file ) );
		$error_code = ! empty( $file_bits[0] ) ? $file_bits[0] : '';
		$error_code .= ! empty( $func ) ? ' - ' . $func : '';
		$error_code .= ! empty( $line ) ? ' - ' . $line : '';
		return $error_code;
	}



	/**
	 * _exception_styles
	 *
	 * @return string
	 */
	private static function _exception_styles() {
		return '
<style type="text/css">
	#ee-error-message {
		max-width:90% !important;
		margin: 0 5%;
	}
	.ee-error-dev-msg-pg,
	.error .ee-error-dev-msg-pg {
		padding:1em;
		margin:0 0 1em;
		border:2px solid #E44064;
		background:#fff;
		border-radius:3px;
		line-height: 1.5em;;
	}
	#ee-trace-details {
		padding:3px;
		margin:0 0 1em;
		border:1px solid #999;
		background:#f9f9f9;
		border-radius:3px;
	}
	#ee-trace-details table {
		border:1px solid #999;
		border-bottom:none;
		background:#fff;
	}
	#ee-trace-details table th {
		background:#eee;
		border-bottom:1px solid #ccc;
	}
	#ee-trace-details table td {
		border-bottom:1px solid #e8e8e8;
		padding: 10px 5px;
	}
	#ee-trace-details table td.odd {
		background:#f8f8f8;
	}
	.display-ee-error-trace-lnk {
		color:blue;
		cursor:pointer;
	}
	.display-ee-error-trace-lnk:hover {
		text-decoration:underline;
	}
	.hidden {
		display:none;
	}
	.small-text {
		font-size: .85em;
		line-height: 1.4em;
		letter-spacing: 1px;
	}
	.lt-grey-text {
		color: #999;
	}
</style>';
	}



	/**
	 * _print_scripts
	 *
	 * @param bool $force_print
	 * @return string|void
	 */
	private static function _print_scripts( $force_print = false ) {
		if ( ! $force_print && ( did_action( 'admin_enqueue_scripts' ) || did_action( 'wp_enqueue_scripts' ) ) ) {
			if ( wp_script_is( 'ee_error_js', 'enqueued' ) ) {
				return '';
			} else if ( wp_script_is( 'ee_error_js', 'registered' ) ) {
				add_filter( 'FHEE_load_css', '__return_true' );
				add_filter( 'FHEE_load_js', '__return_true' );
				wp_enqueue_script( 'ee_error_js' );
				wp_localize_script( 'ee_error_js', 'ee_settings', array( 'wp_debug' => WP_DEBUG ) );
			}
		} else {
			return '
<script>
/* <![CDATA[ */
var ee_settings = {"wp_debug":"' . WP_DEBUG . '"};
/* ]]> */
</script>
<script src="' . includes_url() . 'js/jquery/jquery.js" type="text/javascript"></script>
<script src="' . EE_GLOBAL_ASSETS_URL . 'scripts/espresso_core.js' . '?ver=' . espresso_version() . '" type="text/javascript"></script>
<script src="' . EE_GLOBAL_ASSETS_URL . 'scripts/EE_Error.js' . '?ver=' . espresso_version() . '" type="text/javascript"></script>
';
		}
		return '';
	}


}
// End of file ExceptionHandler.php
// Location: /ExceptionHandler.php