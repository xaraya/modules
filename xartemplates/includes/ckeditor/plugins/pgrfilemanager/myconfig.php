<?php //Copyright (c) 2009 Grzegorz Żydek
 
$path = realpath(dirname(__FILE__));
$num = substr_count($path,'ckeditor');
//Unlikely, but just in case...
if ($num > 2) die('You have more than 2 occurrences of "ckeditor" in the path to pgrfilemanager. See '.$path.'/myconfig.php'); 
//Assume the first instance of 'ckeditor' in the path is the module name
$end = strstr($path,'ckeditor');
$path = str_replace($end,'',$path);
require($path . 'ckeditor/config.plugins.php');

PGRFileManagerConfig::$rootPath = $config['PGRFileManager.rootPath'];
PGRFileManagerConfig::$urlPath = $config['PGRFileManager.urlPath'];

//Max file upload size in bytes
PGRFileManagerConfig::$fileMaxSize = $config['PGRFileManager.fileMaxSize'];

//Allowed file extensions 
$allowedExt = $config['PGRFileManager.allowedExtensions'];
$allowedExt = str_replace(' ', '', $allowedExt);
$allowedExt = explode(',', $allowedExt);
$allowedExt = implode('|', $allowedExt);
PGRFileManagerConfig::$allowedExtensions = $allowedExt;

//Allowed image extensions
$allowedImg = $config['PGRFileManager.imagesExtensions'];
$allowedImg = str_replace(' ', '', $allowedImg);
$allowedImg = explode(',', $allowedImg);
$allowedImg = implode('|', $allowedImg);
PGRFileManagerConfig::$imagesExtensions = $allowedImg;

//Max image file height in px
PGRFileManagerConfig::$imageMaxHeight = $config['PGRFileManager.imageMaxHeight'];
//Max image file width in px
PGRFileManagerConfig::$imageMaxWidth = $config['PGRFileManager.imageMaxWidth'];
//Allow or disallow edit, delete, move, upload, rename files and folders
if ($config['PGRFileManager.allowEdit'] == 'true') {
	$config['PGRFileManager.allowEdit'] = true;
} else {
	$config['PGRFileManager.allowEdit'] = false;
}

PGRFileManagerConfig::$allowEdit = $config['PGRFileManager.allowEdit'];

//Authorization
PGRFileManagerConfig::$authorize = false;        // true - false
PGRFileManagerConfig::$authorizeUser = 'user';
PGRFileManagerConfig::$authorizePass = 'password';

//Path to CKEditor script
//i.e. http://mypage/ckeditor/ckeditor.js
//PGRFileManagerConfig::$ckEditorScriptPath = '/ckeditor/ckeditor.js';
//File extensions editable by CKEditor
//PGRFileManagerConfig::$ckEditorExtensions = 'html|html|txt';

?>