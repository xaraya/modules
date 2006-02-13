<?php
	// ================================================
	// PHP image browser - iBrowser 
	// ================================================
	// iBrowser - configuration
	// ================================================
	// Developed: net4visions.com
	// Copyright: net4visions.com
	// License: GPL - see license.txt
	// (c)2005 All rights reserved.
	// ================================================
	// Revision: 1.3                   Date: 08/12/2005
	// ================================================
   /**
    * xarTinyMCE  imagelibrary integration
    * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
    * @link http://www.xaraya.com
    * @subpackage xarTinyMCE
    * @author Jo Dalle Nogare <jojodee@xaraya.com>
    * Updated for Ibrowser 1.3 on 2005/09/07
    */
    /** *********REMEMBER TO CHECK, COPY AND EDIT THIS FILE!!! *******************
    *
    * INSTRUCTIONS: The main configuration file for the xarTinyMCE image browser
    *
    * 1. Copy this file to var/ibrowser/ibrowserconfig.inc
    * 2. Please make sure you check all configurations settings
    * 3. Update the configurations
    * 4. Make sure  $cfg['base_url'] below is set correctly
    * 4. Enter the name and directories of your image libraries
    *     Make sure the directories exist and are writeable!!
	* 	  - image libraries (chmod 0755 or 0777) eg $cfg['ilibs'] and $cfg['ilibs_dir'] and
    *       the dynamic library directory if used ...$cfg['ilibs_inc']. Uncomment this for dynamic image dirs
	* 	  - ibrowser/scripts/phpThumb/cache  (chmod 0755 or 0777)
	*     - $cfg['temp'] eg var/cache as set below
    */
	//-------------------------------------------------------------------------
	// CONFIGURATIONS
    $cfg['lang']   		= 'en';														// default language; e.g. 'en'
	$cfg['valid']  		= array('gif', 'jpg', 'jpeg', 'png');						// valid extentions for image files	
	$cfg['upload'] 		= true; 													// allow uploading of image: 'true' or 'false'
	$cfg['umax'] 		= 1;														// max. number of image files to be uploaded; default: 1; value > 1
	$cfg['create'] 		= true; 													// allow to create directory: 'true' or 'false'
	$cfg['delete'] 		= true; 													// allow deletion of image: 'true' or 'false'
	$cfg['rename'] 		= true; 													// allow renaming of image: 'true' or 'false'
	$cfg['attrib'] 		= false; 													// allow changing image attributes: 'true' or 'false'; default = false; 	
	$cfg['furl'] 		= true;														// default: true; if set to true, full url incl. domain will be added to image src
	$cfg['random']      = '&w=150&h=150&zc=1';										// random image parameters (see phpThumb readme for more information)
	$cfg['style'] = array ( 														// css styles for images ('class' => 'descr'); - please make sure that the classes exist in your css file
			'left' 				=> 'align left',									// image: float left
			'right' 			=> 'align right',									// image: float right
			'capDivRightBrd' 	=> 'align right, border',							// caption: float right with border
			'capDivRight' 		=> 'align right',									// caption: float right
			'capDivLeftBrd' 	=> 'align left, border',							// caption: float left with border
			'capDivLeft' 		=> 'align left',									// caption: float left
	);
	$cfg['list']		= true;														// default: true; if set to true, image selection will be shown as list; if set to false, image selection will show thumbnails
	//-------------------------------------------------------------------------
	// set image formats	
	$cfg['thumbs'] = array (														 	
		/* array (																	//				settings																	
			'size'   	=> '*',														//				'size' = if set to '*' or '0', no image resizing will be done, otherwise set to desired width or height, e.g. 640
			'ext'    	=> '*',														//				'ext'  = if set to '*' width or height will be set as identifier. If set to '', no identifier will be set.
			'crop'    	=> false,													//				'crop' = if set to true, image will be zoom cropped resulting in a square image		              
		), */
		/*	array (																		
			'size'   	=> 1280,												
			'ext'    	=> '*',
			'crop'    	=> false,
		),
		array (
			'size'   	=> 1024,												
			'ext'    	=> '*',
			'crop'    	=> false,
		),
		array (
			'size'   	=> 640,												
			'ext'    	=> '*',
			'crop'    	=> false,
		),
		array (
			'size'   	=> 512,												
			'ext'    	=> '*',
			'crop'    	=> false,
		), */
		array (
			'size'   	=> 400,												
			'ext'    	=> '*',
			'crop'    	=> false,
		),
		array (
			'size'   	=> 400,												
			'ext'    	=> '*',
			'crop'    	=> true,
		),
		array (
			'size'   	=> 120,												
			'ext'    	=> '*',
			'crop'    	=> false,
		),
/*		array (
			'size'   	=> 75,												
			'ext'    	=> '*',
			'crop'    	=> false,
		), */
	);
	//-------------------------------------------------------------------------
	// use static image libraries	
	$cfg['ilibs'] = array (																// image library path with slashes; absolute to root directory - please make sure that the directories have write permissions	
		array (																		
			'value'   	=> '/xar10x/html/var/images//',
			'text'    	=> 'Tutorials',
		),

				array (
			'value'   	=> '/xar10x/html/var/uploads/',
			'text'    	=> 'Module Overview',
		),
	);
	//-------------------------------------------------------------------------
	// use dynamic image libraries - if $cfg['ilibs_inc'] is set, static image libraries above are ignored
	// image directories to be scanned
	$cfg['ilibs_dir'] 	   = array('/xar10x/html/var/images/');						   	// image library path with slashes; absolute to root directory - please make sure that the directories have write permissions
	$cfg['ilibs_dir_show'] = true;														// show main library (true) or only sub-dirs (false)
     //You must uncomment the following to get dynamic library creation and scanning
	$cfg['ilibs_inc']      = 'scripts/rdirs.php';  	// uncomment to allow dynamic file library

    //-------------------------------------------------------------------------
	// you shouldn't need to make any changes to the config variable beyond this line!
	//-------------------------------------------------------------------------
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

  	/**** END XARAYA MODIFICATION ****/
	$osslash = ((strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? '\\' : '/');
	$cfg['ver'] 		= '1.3 - build 08122005';										// iBrowser version	
	//$cfg['root_dir']	= realpath((getenv('DOCUMENT_ROOT') && ereg('^'.preg_quote(realpath(getenv('DOCUMENT_ROOT'))), realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace($osslash, '/', dirname(__FILE__))));
	$cfg['root_dir']    = ((@$_SERVER['DOCUMENT_ROOT'] && file_exists(@$_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace('\\', '/', realpath('.'))));
	$cfg['base_url'] 	= 'http://' . $_SERVER['SERVER_NAME']; 							// base url; e.g. 'http://localhost/'
	$cfg['main_dir'] 	= dirname($_SERVER['PHP_SELF']); 								// iBrowser main dir; e.g. '/home/domain/public_html/ibrowser/'
    $cfg['scripts']  	= $cfg['main_dir'] . '/scripts/'; 								// scripts dir; e.g. '/home/domain/public_html/ibrowser/scripts/'
	$cfg['pop_url']    	= $cfg['scripts'] . 'popup.php'; 								// popup dir; relative to 'script' dir
    $cfg['temp']     	= $realpath.'/var/cache'; 					// temp dir; e.g. 'D:/www/temp'
    $PHPTHUMB_CONFIG['cache_directory'] = $realpath.'/var/cache/templates'; //only used by phpthumbs
?>