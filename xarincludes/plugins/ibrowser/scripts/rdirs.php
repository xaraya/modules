<?php
	// ================================================
	// PHP image browser - iBrowser 
	// ================================================
	// iBrowser dialog - dir functions
	// ================================================
	// Developed: net4visions.com
	// Copyright: net4visions.com
	// License: GPL - see license.txt
	// (c)2005 All rights reserved.
	// ================================================
	// Revision: 1.0                   Date: 12/12/2005
	// ================================================
	/**** START XARAYA MODIFICATION ****/
    // we need to find the directory our server is opperating in
    // hopefully this is complete :)
    if(isset($_SERVER['DOCUMENT_ROOT'])) {
        $root_path = $_SERVER['DOCUMENT_ROOT'];
    } elseif(isset($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
        $root_path = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
    } else {
        $root_path = getenv('DOCUMENT_ROOT');
    }
    // Now for same hacking ;)
    if(isset($_SERVER['PHP_SELF'])) {
        $scriptpath= dirname($_SERVER['PHP_SELF']);
    } elseif(isset($HTTP_SERVER_VARS['PHP_SELF'])) {
        $scriptpath = dirname($HTTP_SERVER_VARS['PHP_SELF']);
    } else {
        $scriptpath= dirname(getenv('PHP_SELF'));
    }
    //ew .. but it should work ;)
    $scriptpath=parse_url($scriptpath);
    $scriptbase=preg_replace("/index\.php.*|\/modules.*|/is",'',$scriptpath['path']);
    $realpath=$root_path.$scriptbase;
    $realpath=str_replace('//','/',$realpath); //get rid of any double slashes

    // include image library config settings
    if (is_file($realpath.'/var/tinymce/tinymceconfig.inc')) {
        include_once $realpath.'/var/tinymce/tinymceconfig.inc';
   } else {
        // look in the templates directory of this module for the default file
        include_once $realpath.'/modules/tinymce/xartemplates/includes/tinymceconfig.inc';
   }
	$files = array();	
	foreach ($cfg['ilibs_dir'] as $dir) {		
		if ($cfg['ilibs_dir_show'] == true) {
			$files[] = array('value' => absPath(str_replace($cfg['root_dir'],'',$dir)), 'text' => ucfirst(basename($dir)));				
		}		
		if(dirlist($files, str_replace('//','/',$cfg['root_dir'] . $dir))) { // get dirlist
			$cfg['ilibs'] = $files;			
		} else {
			echo 'directory error';
			return false;
		}	
	}	
	function dirlist(&$files,$dir) {		
		global $cfg;
		if ($handle = opendir($dir)) {			
			while ($file = readdir($handle)) {				
				if ($file == '.' || $file == '..') {
					continue;					
				}				
				$fullpath = str_replace('//','/',$dir . '/' . $file);	
				if (is_dir($fullpath)) {						
					$indent = str_repeat('&nbsp;', count(explode('/', trim(str_replace($cfg['root_dir'],'',$dir), '/')))*2);
					$files[] = array('value' => absPath(str_replace( $cfg['root_dir'],'',$fullpath ) . '/'), 'text' => $indent . ucfirst(basename($fullpath)));	
					dirlist($files,$fullpath);														   	
				}
		   	}
			closedir($handle);
			asort($files);	
		   	return true;		
		}
		return false; 		
	}
?>