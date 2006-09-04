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
function gallery_userapi_getmenulinks($args)
{
    if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('file_id', 'int', $file_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('catid', 'int', $catid, null, XARVAR_NOT_REQUIRED) ){ return false; }

    extract($args);

    $current_url = xarServerGetCurrentURL();

    if( !isset($entry_on_new_line) )
        $entry_on_new_line = false;

    $menulinks = array();

    /*
        If we have a file id and user has write privs, provide a modify link
    */
    if( !empty($file_id) )
    {
        if( Security::check(SECURITY_WRITE, 'gallery', FILE_ITEMTYPE, $file_id, false) )
        {
            $url = xarModURL('gallery', 'user', 'modify',
                array(
                    'album_id' => $album_id,
                    'file_id' => $file_id,
                    'redirecturl' => $current_url
                )
            );
            $menulinks[] = array(
                'label'   => xarML('Modify Photo')
                , 'title' => xarML('Modify Photo')
                , 'url'   => $url
            );

            if( !is_null($album_id) )
                {
                $url = xarModURL('gallery', 'admin', 'set_preview_file',
                    array(
                        'album_id' => $album_id,
                        'file_id' => $file_id,
                        'returnurl' => xarServerGetCurrentURL()
                    )
                );
                $menulinks[] = array(
                    'label'   => xarML('Make Album Cover')
                    , 'title' => xarML('Make Album Cover')
                    , 'url'   => $url
                );
            }
        }
    }

    if( xarUserIsLoggedIn() )
    {
        $menulinks[] = array(
            'label'   => xarML('Browse My Photos')
            , 'title' => xarML('Browse My Photos')
            , 'url'   => xarModURL('gallery', 'user', 'my_view')
        );
    }

    $menulinks[] = array(
        'label' => xarML('Browse Albums')
        , 'title' => xarML('Browse Albums')
        , 'url' => xarModURL('gallery', 'user', 'main')
    );

    $menulinks[] = array(
        'label' => xarML('Browse Photos')
        , 'title' => xarML('Browse Albums')
        , 'url' => xarModURL('gallery', 'user', 'view')
    );

    $url = xarModURL('gallery', 'user', 'new_files',
        array('album_id' => $album_id)
    );
    $menulinks[] = array(
        'label'   => xarML('Upload New Photos')
        , 'title' => xarML('Upload New Photos')
        , 'url'   => $url
    );

    $menulinks[] = array(
        'label' => xarML('Create an Album')
        , 'title' => xarML('Create an Album')
        , 'url' => xarModURL('gallery', 'user', 'new_album_wizard', array('catid' => $catid))
    );

    return $menulinks;

}
?>