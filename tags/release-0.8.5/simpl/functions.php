<?php
/**
 * Autoload classes (no need to include them one by one)
 *
 * @param $className string
 */
function __autoload($className){
	if (is_file(FS_SIMPL . strtolower($className) . '.php'))
		include_once(FS_SIMPL . strtolower($className) . '.php');
	else if (defined('DIR_CLASSES') && is_file(DIR_CLASSES . strtolower($className) . '.php'))
		include_once(DIR_CLASSES . strtolower($className) . '.php');
}

/**
 * Display an array of alerts with a div class
 *
 * @param $alerts An Array with the alerts
 * @param $type A string with the type of alert, usually ("error","success")
 * @return NULL
 */
if (!function_exists('Alert')){
	function Alert($alerts, $type=''){
		// Decide what class to display
		$class = ($type == '')?'Error':$type;
		
		//Display all errors to user
		if ( is_array($alerts) && count($alerts) > 0){
			while ( list($key,$data) = each($alerts) ){
				echo '<div class="form' . ucfirst($class) . '" id="form' . ucfirst($class) . '"><p>' . $data . '</p></div>'. "\n";
			}
		}else if ( is_string($alerts) ){
			echo '<div class="form' . ucfirst($class) . '" id="form' . ucfirst($class) . '"><p>' . $alerts . '</p></div>'. "\n";
		}
	}
}

/**
 * Set a string as an alert
 * 
 * @param $alert A string with the Alert text in it
 * @param $type A string with the type of alert, usually ("error","success")
 * @return bool
 */
if (!function_exists('SetAlert')){
	function SetAlert($alert,$type='error'){
		// Set the Alert into the correct session type
		if (is_array($alert))
			foreach($alert as $value)
				$_SESSION[$type][] = $value;
		else
			$_SESSION[$type][] = $alert;
		
		return true;
	}
}

/**
 * Is there a certain type of alerts waiting
 * 
 * @param $type A string containing the type of alert to return
 * @return array
 */
if (!function_exists('IsAlert')){
	function IsAlert($type){
		// Return if there are strings waiting the the session type array
		return (is_array($_SESSION[$type]) && count($_SESSION[$type]) > 0);
	}
}

/**
 * Get the Alert from the session
 * This will clear the session alerts when done.
 * 
 * @param $type A string containing the type of alert to return
 * @return array
 */
if (!function_exists('GetAlert')){
	function GetAlert($type){
		// Get the array
		$return = $_SESSION[$type];
		// Reset the array
		$_SESSION[$type] = array();
		// Return the array
		return $return;
	}
}

/**
 * Display text or an array in HTML <pre> tags
 *
 * @param $text A mixed set, anything with a predefined format
 * @return null
 */
if (!function_exists('Pre')){
	function Pre($text, $ip=''){
		$ready = true;
		
		if (is_string($ip) && $ip != '' && $_SERVER['REMOTE_ADDR'] != $ip)
			$ready = false;
		else if (is_array($ip) && !in_array($_SERVER['REMOTE_ADDR'], $ip))
			$ready = false;
		
		if ($ready == true){
			echo '<pre>';
			print_r($text);
			echo '</pre>';
		}
	}
}

/**
 * Display Debug Information if set
 *
 * @param $output A mixed variable that needs to be outputted with predefined formatting
 * @return NULL
 */
if (!function_exists('Debug')){
	function Debug($output, $class=''){
		if (DEBUG === true){
			$backtrace = debug_backtrace();
			$debug = array();
			$stack = (isset($backtrace[1]['class']) ? "{$backtrace[1]['class']}::" : '') . (isset($backtrace[1]['function']) ? "{$backtrace[1]['function']}" : '');
			
			if ($stack)
				$debug[] = $stack;
				
			$debug[] = "Line {$backtrace[0]['line']} of {$backtrace[0]['file']}";
			
			$debug = implode('<br />', $debug);
			
			print '<pre class="debug' . (($class != '')?' ' . $class:'') . '">' . "{$label}: {$debug}:<br />" . print_r($output, 1) . "\n";
		}
		
		/*
		if (DEBUG === true){
			echo '<pre class="debug' . (($class != '')?' ' . $class:'') . '">DEBUG:' . "\n";
			print_r($output);
			echo '</pre>';
		}
		*/
		
		if (DEBUG_LOG === true){
			 if (!$fp = fopen(FS_CACHE . 'debug.log', "a"))
			 	return;
			 
			 if (fwrite($fp, date("Y-m-d H:i:s") . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . print_r($output, true) . "\n") === FALSE)
			 	return;
			 	
			 fclose($fp);
			 chmod (FS_CACHE . 'debug.log', 0777);
		}
	}
}

/**
 * Displays time difference in readable way
 *
 * @param $data_ref date to compare against in 0000:00:00 00:00:00 format
 * @return string
 */
if (!function_exists('DateTimeDiff')){
function DateTimeDiff($data_ref){
	$current_date = date('Y-m-d H:i:s');

	// Extract $current_date
	$current_year = substr($current_date,0,4);	
	$current_month = substr($current_date,5,2);
	$current_day = substr($current_date,8,2);

	// Extract from $data_ref
	$ref_year = substr($data_ref,0,4);
	$ref_month = substr($data_ref,5,2);
	$ref_day = substr($data_ref,8,2);

	// create a string yyyymmdd 20071021
	$tempMaxDate = $current_year . $current_month . $current_day;
	$tempDataRef = $ref_year . $ref_month . $ref_day;

	$tempDifference = $tempMaxDate-$tempDataRef;

	// If the difference is GT 10 days show the date
	if($tempDifference >= 10){
		$str = 'F j, Y \\a\\t g:i a';
		if (date('Y', strtotime($data_ref)) == date('Y'))
			$str = 'F j \\a\\t g:i a';
		
		return date($str, strtotime($data_ref));
	}else{
		// Extract $current_date H:m:ss
		$current_hour = substr($current_date,11,2);
		$current_min = substr($current_date,14,2);
		$current_seconds = substr($current_date,17,2);

		// Extract $data_ref Date H:m:ss
		$ref_hour = substr($data_ref,11,2);
		$ref_min = substr($data_ref,14,2);
		$ref_seconds = substr($data_ref,17,2);
	
		$hDf = $current_hour-$ref_hour;
		$mDf = $current_min-$ref_min;
		$sDf = $current_seconds-$ref_seconds;
	
		// Show time difference ex: 2 min 54 sec ago.
		if($dDf<1){
			if($hDf>0){
				if($mDf<0){
					$mDf = 60 + $mDf;
					$hDf = $hDf - 1;
					return $mDf . ' min ago';
				} else {
					return $hDf. ' hr ' . $mDf . ' min ago';
				}
			} else {
				if($mDf>0){
					return $mDf . ' min ' . $sDf . ' sec ago';
				} else {
					return $sDf . ' sec ago';
				}
			}
		} else {
			return $dDf . ' days ago';
		}
	}
}
}

function search_split_terms($terms){
	$terms = preg_replace("/\"(.*?)\"/e", "search_transform_term('\$1')", $terms);
	$terms = preg_split("/\s+|,/", $terms);

	$out = array();
	foreach($terms as $term){
		$term = preg_replace("/\{WHITESPACE-([0-9]+)\}/e", "chr(\$1)", $term);
		$term = preg_replace("/\{COMMA\}/", ",", $term);
		$out[] = $term;
	}

	return $out;
}

function search_transform_term($term){
	$term = preg_replace("/(\s)/e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
	$term = preg_replace("/,/", "{COMMA}", $term);
	return $term;
}

function search_escape_rlike($string){
	return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
}

function search_db_escape_terms($terms){
	$out = array();
	foreach($terms as $term){
		$out[] = '[[:<:]]'.AddSlashes(search_escape_rlike($term)).'[[:>:]]';
	}
	return $out;
}

function search_rx_escape_terms($terms){
	$out = array();
	foreach($terms as $term){
		$out[] = '\b'.preg_quote($term, '/').'\b';
	}
	return $out;
}

function search_sort_results($a, $b){
	$ax = $a[score];
	$bx = $b[score];

	if ($ax == $bx){ return 0; }
	return ($ax > $bx) ? -1 : 1;
}

function search_html_escape_terms($terms){
	$out = array();

	foreach($terms as $term){
		if (preg_match("/\s|,/", $term)){
			$out[] = '"'.HtmlSpecialChars($term).'"';
		}else{
			$out[] = HtmlSpecialChars($term);
		}
	}

	return $out;	
}

function search_pretty_terms($terms_html){
	if (count($terms_html) == 1){
		return array_pop($terms_html);
	}

	$last = array_pop($terms_html);
	return implode(', ', $terms_html)." and $last";
}

/**
 * Checks for multiarray (2 or more levels deep)
 * 
 * @param $multiarray Array
 * @return bool
 */
function isMultiArray($multiarray) {
  if (is_array($multiarray))
   foreach ($multiarray as $array)
     if (is_array($array))
       return true;
  return false;
}
?>