<?php
/**
 * xarTinyMCE  imagelibrary integration
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage xarTinyMCE
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/** *********REMEMBER TO CHECK, COPY AND EDIT THIS FILE!!! *******************
 *
 * INSTRUCTIONS: The main configuration file for the xarTinyMCE image browser
 *
 * 1. Copy this file to var/ibrowser/ibrowserconfig.inc
 * 2. Please make sure you check all configurations settings
 * 3. Update Allowed extensions, Allow Upload, Allow Delete, Base URL for your IMAGES
 * 4. Enter the name and directories of your image libraries
 *     Make sure the directories exist and are writeable
 */

// allowed extentions for uploaded image files
$tinyMCE_valid_imgs = array('gif', 'jpg', 'jpeg', 'png');

// allow upload in image library
//change to false to prevent image uploads through this TinyMCE image browser
$tinyMCE_upload_allowed = true;

// allow delete in image library
$tinyMCE_img_delete_allowed = true;

// BASE URL FOR IMAGES
//change if required
// eg http://www.mysite.com/ Note the trailing slash
$tinyMCE_base_url = 'http://www.mysite.com/';

//Now setup your image libraries
//Each library is defined by a path relative to your website document root
// and a description of that library
//Note the trailing slash
// The more images in a library, the longer time for the image browser to load
$tinyMCE_imglibs=array();

   $tinyMCE_imglibs[]=  array(
                             'value'=>'var/uploads/images/', //trailing slash
                             'text' =>'Image Library');


   $tinyMCE_imglibs[] =     array(
                               'value'   => 'var/uploads/trusted/', //trailing slash
                               'text'    => 'Web Site Standard Images');

//Be careful and make sure no newlines after the end of the file marker ? >
?>
