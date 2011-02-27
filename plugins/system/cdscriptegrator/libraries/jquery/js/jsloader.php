<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

define('DS', DIRECTORY_SEPARATOR);
$dir = dirname(__FILE__);

$filename = array();
$filename []= $dir . DS . 'jquery-latest.packed.js';
$filename []= $dir . DS . 'jquery-noconflict.js';

if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) @ob_start('ob_gzhandler');

header("Content-type: application/x-javascript");
header('Cache-Control: must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

foreach ($filename as $file) {
	if (file_exists($file)) {
		include($file);
		echo "\n";
	}
}

?>
