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

function gallery_ajax_server($args)
{
    ob_end_clean();

    if( !xarVarFetch('action', 'str', $action, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $output = null;
    switch( $action )
    {
        case 'TestFilePath':
            if( !xarVarFetch('file_path', 'str', $filepath, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( file_exists($filepath) and is_writeable($filepath) ){
                $output = 'true';
            } else {
                $output = 'false';
            }
            break;

        default:

            break;
    }

    echo $output;

    exit();
}
?>