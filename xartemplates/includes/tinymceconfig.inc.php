<?php
/**
    * xarTinyMCE  imanager, ibrowser and filebrowser integration
    * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
    * @link http://www.xaraya.com
    * @subpackage xarTinyMCE
    * @author Jo Dalle Nogare jo@2skies.com or visit http://xarigami.com
    * Last update 2007/9/1
    */
    // ================================================
    // PHP Image and  File Browser Configurations
    // ================================================
    // Configuration for ibrowser, imanager and filebrowser
    // ================================================
    // Developed: net4visions.com
    // Copyright: net4visions.com
    // License: GPL - see license.txt
    // (c)2005 All rights reserved.
    // Consolidated for all files and adjusted for Xaraya xartinymce
    // ================================================
    // Revision: 1.3                   Date: 08/12/2005
    // ================================================
    /** *********REMEMBER TO CHECK, COPY AND EDIT THIS FILE!!! *******************
    *
    * INSTRUCTIONS: The main configuration file for the xarTinyMCE ibrowser, imanager and filebrowser
    *
    * 1. Copy this file to var/tinymce/tinymceconfig.inc.php
    * 2. Please make sure you check all configurations settings
    * 3. Update the configurations especially:
    *     - $cfg['thumbs'] - set your sizes for image uploads and uncomment where necessary
    *     - $cfg['ilibs'] - **VERY IMPORTANT*** make sure you set these to browse and upload images
    *     - $cfg['ilibs_dir']  - set this if you want DYNAMIC image directory browsing/creation
    *     - $cfg['ilibs_inc'] - make sure you uncomment this line for DYNAMIC image directory browsing
    *     - $cfg['filebrowser_dir'] - filebrowser main directory MUST BE SET and CORRECTLY
    * 4. Check directories exist and are writeable (eg chmod 0755 or 0777) else it WILL NOT WORK!!
    *     - $cfg['ilibs'] and $cfg['ilibs_dir'] and
    *       the dynamic library directory if used ...$cfg['ilibs_inc']. Uncomment this for dynamic image dirs
    *     - $cfg['filebrowser_dir'] should be writeable eg chmod 07555 or 0777 depending on server setup
    *     - $PHPTHUMB_CONFIG['cache_directory'] as set below
    *     - $cfg['temp'] eg var/cache as set below
    */
    //-------------------------------------------------------------------------
    // CONFIGURATIONS
    $cfg['mode']        = 1;                                                        // 1 = plugin mode (default); 2 = standalone mode'
    $cfg['lang']        = 'en';                                                     // default language; e.g. 'en'
    $cfg['valid']       = array('gif', 'jpg', 'jpeg', 'png');                       // valid extentions for image files
    $cfg['upload']      = true;                                                     // allow uploading of image: 'true' or 'false'
    $cfg['umax']        = 1;                                                        // max. number of image files to be uploaded; default: 1; value > 1
    $cfg['create']      = true;                                                     // allow to create directory: 'true' or 'false'
    $cfg['delete']      = true;                                                     // allow deletion of image: 'true' or 'false'
    $cfg['rename']      = true;                                                     // allow renaming of image: 'true' or 'false'
    $cfg['attrib']      = false;                                                    // allow changing image attributes: 'true' or 'false'; default = false;     
    $cfg['furl']        = true;                                                     // default: true; if set to true, full url incl. domain will be added to image src
    $cfg['random']      = '&w=150&h=150&zc=1';                                      // random image parameters (see phpThumb readme for more information)
    $cfg['style'] = array (                                                         // css styles for images ('class' => 'descr'); - please make sure that the classes exist in your css file
            'left'              => 'align left',                                    // image: float left
            'right'             => 'align right',                                   // image: float right
            'capDivRightBrd'    => 'align right, border',                           // caption: float right with border
            'capDivRight'       => 'align right',                                   // caption: float right
            'capDivLeftBrd'     => 'align left, border',                            // caption: float left with border
            'capDivLeft'        => 'align left',                                    // caption: float left
    );
    $cfg['list']        = true;                                                     // default: true; if set to true, image selection will be shown as list; if set to false, image selection will show thumbnails
    //-------------------------------------------------------------------------
    // set image formats    
    $cfg['thumbs'] = array (                                                            
        /* array (                                                                  //              settings                                                                    
            'size'      => '*',                                                     //              'size' = if set to '*' or '0', no image resizing will be done, otherwise set to desired width or height, e.g. 640
            'ext'       => '*',                                                     //              'ext'  = if set to '*' width or height will be set as identifier. If set to '', no identifier will be set.
            'crop'      => false,                                                   //              'crop' = if set to true, image will be zoom cropped resulting in a square image                   
        ), */
        /*  array (                                                                     
            'size'      => 1280,                                                
            'ext'       => '*',
            'crop'      => false,
        ),
        array (
            'size'      => 1024,                                                
            'ext'       => '*',
            'crop'      => false,
        ),
        array (
            'size'      => 640,                                             
            'ext'       => '*',
            'crop'      => false,
        ),
        array (
            'size'      => 512,                                             
            'ext'       => '*',
            'crop'      => false,
        ), */
        array (
            'size'      => 400,                                             
            'ext'       => '*',
            'crop'      => false,
        ),
        array (
            'size'      => 400,                                             
            'ext'       => '*',
            'crop'      => true,
        ),
        array (
            'size'      => 120,                                             
            'ext'       => '*',
            'crop'      => false,
        ),
/*      array (
            'size'      => 75,                                              
            'ext'       => '*',
            'crop'      => false,
        ), */
    );
    //-------------------------------------------------------------------------
    // ibrowser and imanager - change to use for static image libraries
    //- change the value and text description to suit your own directories, use more or less array entries as necessary
    $cfg['ilibs'] = array (
         // image library path with slashes; absolute to xaraya doc root directory
         //- please make sure that the directories have write permissions
        array (
             'value'    => '/var/uploads/',
             'text'      => 'Stock Images',
        ),

        array (
            'value'     => '/var/images/Image',
            'text'      => 'Image Library',
        ),
    );
    //-------------------------------------------------------------------------
    //IMANAGER and IBROWSER settings
    // if you want to use DYNAMIC IMAGE LIBRARIES  - if $cfg['ilibs_inc'] is set, static image libraries above are ignored
    // image directories to be scanned
    $cfg['ilibs_dir']      = array('/var/uploads/Image/'); // image library path with slashes; absolute to xar root directory - please make sure that the directories have write permissions
    $cfg['ilibs_dir_show'] = true;                  // show main library (true) or only sub-dirs (false)
     //You must uncomment the following to get dynamic library creation and scanning
    $cfg['ilibs_inc']      = 'scripts/rdirs.php';   // uncomment to allow dynamic file library

    //-------------------------------------------------------------------------
    //FILEBROWSER only settings - you MUST set this correctly
    $cfg['filebrowser_dir']= '/var/uploads/'; //Filebrowser will look for images here and create allowed types directories

    $cfg['Enabled'] = 0 ; // 1 - on, 0 off Set this to enable or disable Filebrowser upload and browse facility. If disabled filebrowser appears but a disabled message is shown

    // Configurations below can be left at default
    //Types of media users can 'browse' - remove type to prevent browsing of that type. Do not change names unless you know what you are doing. You can remo
    $Config['AllowedResources']['Types']    = array('File','Images','Flash','Media') ;

    $Config['AllowedExtensions']['File']    = array() ;
    $Config['DeniedExtensions']['File']     = array('php','php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;

    $Config['AllowedExtensions']['Image']   = array('jpg','gif','jpeg','png') ;
    $Config['DeniedExtensions']['Image']    = array() ;

    $Config['AllowedExtensions']['Flash']   = array('swf','fla') ;
    $Config['DeniedExtensions']['Flash']    = array() ;

    $Config['AllowedExtensions']['Media']   = array('swf','fla','jpg','gif','jpeg','png','avi','mpg','mpeg') ;
    $Config['DeniedExtensions']['Media']    = array() ;

    /* -------------------------------------------------------------------------
     * NOTE: You should usually  ****NOT*** need to make changes to variables beyond this line!
     * -------------------------------------------------------------------------
     */
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
    $cfg['ver']         = '1.3 - build 08122005';                                       // iBrowser version
    //$cfg['root_dir']  = realpath((getenv('DOCUMENT_ROOT') && preg_match('/^'.preg_quote(realpath(getenv('DOCUMENT_ROOT')).'/'), realpath(__FILE__))) ? getenv('DOCUMENT_ROOT') : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace($osslash, '/', dirname(__FILE__))));
    $cfg['root_dir']    = ((@$_SERVER['DOCUMENT_ROOT'] && file_exists(@$_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace('\\', '/', realpath('.'))));
    $cfg['base_url']    = 'http://' . $_SERVER['SERVER_NAME'];                          // base url; e.g. 'http://localhost/'
    $cfg['main_dir']    = dirname($_SERVER['PHP_SELF']);                                // iBrowser main dir; e.g. '/home/domain/public_html/ibrowser/'
    $cfg['scripts']     = $cfg['main_dir'] . '/scripts/';                               // scripts dir; e.g. '/home/domain/public_html/ibrowser/scripts/'
    $cfg['fonts']       = dirname($_SERVER['PHP_SELF']) . '/fonts/';                    // ttf font dir; absolute path
    $cfg['mask']        = dirname($_SERVER['PHP_SELF']) . '/masks/';                    // mask dir; absolute path
    $cfg['olay']        = dirname($_SERVER['PHP_SELF']) . '/olays/';                    // overlay dir; absolute path
    $cfg['mark']        = dirname($_SERVER['PHP_SELF']) . '/wmarks/';                   // watermarks dir; absolute path
    $cfg['pop_url']     = $cfg['scripts'] . 'popup.php';                                // popup dir; relative to 'script' dir
    $cfg['temp']        = $realpath.'/var/cache';                   // temp dir; e.g. 'D:/www/temp'
    $PHPTHUMB_CONFIG['cache_directory'] = $realpath.'/var/cache/templates'; // used by phpthumbs in ibrowser for image caching
    $PHPTHUMB_CONFIG['document_root']=$cfg['root_dir'];
?>
