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
/*
    Moves the file to the main gallery storage location when linkage changes

    @param integer $args['file_id']

    @return string current base dir path
*/
function gallery_adminapi_fix_file_paths($args)
{
    extract($args);

    if( empty($file_id) )
    {
        return false;
    }

    if( empty($links) )
    {
        return false;
    }

    /*
        First we need to check if file is still in a linked gallery
        We will need the current file location
    */
    $old_file_data =& xarModAPIFunc('gallery', 'user', 'get_file',
        array(
            'file_id' => $file_id
        )
    );

    // returns the gallery name w/o too much work, but is rather hacky
    $current_base_dir = dirname(dirname($old_file_data['file']));
    $found = false;
    foreach( $links as $album_name )
    {
        if( $current_base_dir == $album_name )
        {
            $found = true;
        }
    }
    reset($links);

    if( !$found )
    {
        /*
            If not found then we need to move the image to another gallery directory
        */
        $new_base_dir = current($links);
        if( empty($new_base_dir) )
        {
            /*
                Something is wrong abort update so we don't lose the file
            */
            var_dump($links);
            var_dump($new_base_dir);
            return false;
        }

        /*
            make sure user directory exists in the new base dir. if not create it
        */
        $uid = xarUserGetVar('uid');
        $path = xarModGetVar('gallery', 'file_path') . $new_base_dir . '/' . $uid . '/';
        // generate user dir for file if none existant
        if( !file_exists($path) )
        {
            mkdir($path, 0755);
        }

        /**
            Move the file and remove the old one if the new location is writable
        */
        $file_name = basename($old_file_data['file']);
        $old_file = xarModGetVar('gallery', 'file_path') . $old_file_data['file'];
        $new_path = $path . $file_name;
        if( is_writable($path) )
        {
            /*
                NOTE: Only move the file if a file is not already in the $newpath
                cause we have the potential to lose data if there are two different
                files by the same name
            */
            if( !file_exists($new_path) )
            {
                copy($old_file, $new_path);
                unlink($old_file);

                $file = "$new_base_dir/$uid/$file_name";
                $current_base_dir = $new_base_dir;

                xarModAPIFunc('gallery', 'admin', 'update_file',
                    array(
                        'file_id' => $file_id,
                        'file' => $file
                    )
                );
            }
        }
    }

    return $current_base_dir;
}
?>