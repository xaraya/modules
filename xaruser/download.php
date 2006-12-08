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
 * Allows the original file to be downloaded.
 *
 * @param array $args
 * @return void
 */
function gallery_user_download($args){

    if( !xarModGetVar('gallery', 'original_file_downloads') ){ return false; }

    if( !xarVarFetch('file_id', 'int', $file_id, null) ){ return false; }

    if( $file_id != null ){

        if( !Security::check(SECURITY_READ, 'gallery', FILE_ITEMTYPE, $file_id) ){ return false; }

        $file = xarModAPIFunc('gallery', 'user', 'get_file',
            array(
                'file_id'  => $file_id
                , 'level'    => SECURITY_READ
            )
        );
        if( empty($file) ){ return false; }

        $base_path = xarModGetVar('gallery', 'file_path');

        $file_name = $file['name'];
        $file_path = realpath($base_path . $file['file']);

        ob_end_clean();

        $file_contents = file_get_contents($file_path);

        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-type: {$file['type']}");
        header("Content-disposition: attachment; filename=\"$file_name\"");
        header("Content-length: {$file['size']}");

        echo $file_contents;
        exit();
    }
}
?>