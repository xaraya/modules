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

function gallery_user_summary($args)
{
    xarVarFetch('numitems', 'int', $numitems, 10, XARVAR_NOT_REQUIRED);

    extract($args);

    $data = array();

    $files = xarModAPIFunc('gallery', 'user', 'get_files',
        array(
            'numitems' => $numitems,
            'level' => SECURITY_WRITE,
            'uid' => xarUserGetVar('uid')
        )
    );

    /*
        Generate Links
    */
    foreach( $files as $key => $file )
    {
        $files[$key]['view_link'] = xarModURL('gallery', 'user', 'display',
            array(
                'album_id' => $file['album_id'],
                'file_id' => $key,
                'theme' => 'print'
            )
        );

        $files[$key]['modify_link'] = xarModURL('gallery', 'user', 'modify',
            array(
                'what' => 'files',
                'file_id' => $key
            )
        );
    }


    $data['states'] = xarModAPIFunc('gallery', 'user', 'get_states');

    $data['files'] = $files;

    return $data;
}
?>