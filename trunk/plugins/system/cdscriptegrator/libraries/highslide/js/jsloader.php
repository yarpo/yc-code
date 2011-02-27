<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

define('DS', DIRECTORY_SEPARATOR);
$dir = dirname(__FILE__);

$file = $dir . DS . 'highslide.packed.js';

if (file_exists($file)) {
	
	if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) @ob_start('ob_gzhandler');
	
	header("Content-type: application/x-javascript");
	header('Cache-Control: must-revalidate');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
	
	include($file);

}
?>
