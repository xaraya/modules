<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */
/**
    Takes an uploaded file and tries to store it.

    @param string  $args['file_id']  Id of file to be read in
    @param string  $args['name']      Name of file to read in
    @param integer $args['index']     Index of file is multiple files were uploaded
    @param string  $args['base_dir']  Base dir of image to store
    @param boolean $args['overwrite'] True if new image should over right any pre-existing image

    @return array Contains file storage info
*/
function gallery_adminapi_process_file($args)
{
    extract($args);

    if( empty($file_id) )
    {
        return false;
    }

    if( empty($name) )
    {
        return false;
    }

    if( is_array($_FILES[$name]['name']) )
    {
        if( !isset($index) ){ return false; }

        $file = array();
        foreach ($_FILES[$name] as $type => $value)
        {
        	$file[$type] = $value[$index];
        }
    }
    else
    {
        $file = $_FILES[$name];
    }

    if( !is_uploaded_file($file['tmp_name']) )
    {
        /*
            Throw exception
        */
        return false;
    }

    /*
        Set defaults
    */
    if( !isset($overwrite) )
    {
        $overwrite = false;
    }

    $file_info = $file;

    /*
        Prepare path and save uploaded file.
    */
    if( xarModGetVar('gallery', 'obfuscate_file_name') == true )
    {
        $salt = "gallery"; // We use this salt so that dervatives don't collide as we are just hashing an int and if  other modules do something similarwe would get the wrong image at times
        $md5_name = md5("$salt-$file_id-{$file['name']}");

        $file_path = realpath(xarModGetVar('gallery', 'file_path'));

        $file_path .= '/' . $md5_name;

        $file_info['file'] = $md5_name;
    }
    else
    {
        $file_name = $file_id . '-' . $file['name'];
        $file_path = realpath(xarModGetVar('gallery', 'file_path')) . '/' . $file_name;
        $file_info['file'] = $file_name;
    }

    if( file_exists($file_path) && $overwrite == false )
    {
        /*
            Throw file already exists exception and/or return false
        */
        return false;
    }

    move_uploaded_file($file['tmp_name'], $file_path);
    chmod($file_path, 0644);

    return $file_info;
}
?>