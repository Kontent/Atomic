<?php
/**
 * @copyright	Copyright (C) 2020 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if (isset($_POST["id"]))  {
	$path =  $_SERVER["PHP_SELF"];
	$path = substr($path, 0, strpos($path, "templates"));
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path."templates/atomic/css/template.css")) {
		echo '0';
	}
	else {
		$content = "/* Add your custom CSS here. */";
		$fp = fopen($_SERVER['DOCUMENT_ROOT'] . $path."templates/atomic/css/template.css","wb") or die('File already exists!');
		echo "1";
		fwrite($fp,$content);
		fclose($fp);
	}
}

if (isset($_POST["script_id"])) {
	$path =  $_SERVER["PHP_SELF"];
	$path = substr($path, 0, strpos($path, "templates"));
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path."templates/atomic/css/template.css")) {
		echo '2';
	}
}
?>