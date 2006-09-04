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

function gallery_user_my_view($args)
{
    /*
        If user not logged in then throw an exception saying user needs to be logged in.
        Later a module/event handler can be written to pick up on that and handle it
        by loggin the user in or creating an account.
    */
    if( !xarUserIsLoggedIn() )
    {
        $msg = "";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_LOGGED_IN');
        return false;
    }

    extract($args);

    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('startnum', 'int', $startnum, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('numitems', 'int', $numitems, 10, XARVAR_NOT_REQUIRED) ){ return false; }

    $data = array();

    $files = xarModAPIFunc('gallery', 'user', 'get_files',
        array(
            'album_id' => $album_id,
            'startnum' => $startnum,
            'numitems' => $numitems,
            'level' => SECURITY_WRITE
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
        $files[$key]['delete_link'] = xarModURL('gallery', 'user', 'delete',
            array(
                'what' => 'files',
                'file_id' => $key
            )
        );
    }

    $count = xarModAPIFunc('gallery', 'user', 'count_files',
        array(
            'album_id' => $album_id,
            'level' => SECURITY_WRITE
        )
    );

    $data['states'] = xarModAPIFunc('gallery', 'user', 'get_states');

    $url_template = xarModURL('gallery', 'user', 'my_view',
        array(
            'album_id' => $album_id,
            'startnum' => '%%'
        )
    );
    $data['pager'] = xarTplGetPager($startnum, $count, $url_template, $numitems);

    $data['files_path'] = xarModGetVar('gallery', 'file_path');
    $data['files'] = $files;

    return $data;
}
?>