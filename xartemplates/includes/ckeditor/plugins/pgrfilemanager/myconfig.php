<?php //Copyright (c) 2009 Grzegorz Żydek
 
require_once('currpageurl.php');

$currurl = curPageURL();
// Some experimental stuff here
if (strstr($currurl, 'PGRFileManager.php')) { 
	$prop = $_GET['CKEditor'];
	//if (!empty($prop)) print $prop;
}

// Should work with this commented out, but it doesn't...
// $prop = 'dd_318';

require(realpath(dirname(__FILE__) . '/config.plugins.php'));

$config = $pluginsConfiguration;

if (!empty($config[$prop]['PGRFileManager.rootPath'])) {
	$rootPath = $config[$prop]['PGRFileManager.rootPath'];
} else {
	$rootPath = $config['default']['PGRFileManager.rootPath'];
}
if (!empty($config[$prop]['PGRFileManager.urlPath'])) {
	$urlPath = $config[$prop]['PGRFileManager.urlPath'];
} else {
	$urlPath = $config['default']['PGRFileManager.urlPath'];
} 

PGRFileManagerConfig::$rootPath = $rootPath;

PGRFileManagerConfig::$urlPath = $urlPath;

//    !!!How to determine rootPath and urlPath!!!
//    1. Copy mypath.php file to directory which you want to use with PGRFileManager
//    2. Run mypath.php script, i.e http://my-super-web-page/gallery/mypath.php
//    3. Insert correct values to myconfig.php
//    4. Delete mypath.php from your root directory


//Max file upload size in bytes
PGRFileManagerConfig::$fileMaxSize = 1024 * 1024 * 10;
//Allowed file extensions
//PGRFileManagerConfig::$allowedExtensions = '' means all files
PGRFileManagerConfig::$allowedExtensions = '';
//Allowed image extensions
PGRFileManagerConfig::$imagesExtensions = 'jpg|gif|jpeg|png|bmp';
//Max image file height in px
PGRFileManagerConfig::$imageMaxHeight = 724;
//Max image file width in px
PGRFileManagerConfig::$imageMaxWidth = 1280;
//Thanks to Cycle.cz
//Allow or disallow edit, delete, move, upload, rename files and folders
PGRFileManagerConfig::$allowEdit = true;		// true - false
//Autorization
PGRFileManagerConfig::$authorize = false;        // true - false
PGRFileManagerConfig::$authorizeUser = 'user';
PGRFileManagerConfig::$authorizePass = 'password';
//Path to CKEditor script
//i.e. http://mypage/ckeditor/ckeditor.js
//PGRFileManagerConfig::$ckEditorScriptPath = '/ckeditor/ckeditor.js';
//File extensions editable by CKEditor
//PGRFileManagerConfig::$ckEditorExtensions = 'html|html|txt';