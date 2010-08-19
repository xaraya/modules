<?php //Copyright (c) 2009 Grzegorz Żydek
 
require(realpath(dirname(__FILE__) . '/config.plugins.php'));

PGRFileManagerConfig::$rootPath = $config['PGRFileManager.rootPath'];
PGRFileManagerConfig::$urlPath = $config['PGRFileManager.urlPath'];

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

?>