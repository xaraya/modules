<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_user_addimages()
{
    if (!xarSecurityCheck('AddPhoto')) return;
    if(!xarVarFetch('fid', 'isset:int', $folderID,  NULL, XARVAR_GET_OR_POST)) {return;}

    $data = array();

    $data['folder'] = xarModAPIFunc('photoshare',
                        'user',
                        'getfolders',
                        array( 'folderID' => $folderID, 'getForList' => false, 'prepareForDisplay' => true));

    if (!isset($data['folder'])) return;

    $data['trail'] = xarModAPIFunc('photoshare',
                            'user',
                            'getfoldertrail',
                            array( 'folderID' => $folderID ));

    if (!isset($data['trail'])) return;

    if (!xarSecurityCheck('AddPhoto', 1, 'item', "all:all:$folderID")) return;

    // Add top menu
    $data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
            array(    'gotoCurrentFolder' => true,
                    'menuHide' => array( '_PSMENUADDFOLDER'    => true,
                                        '_PSMENUADDIMAGES'    => true,
                                        '_PSMENUFOLDEREDIT'   => true,
                                        '_PSMENUVIEWFOLDER'   => true,
                                        '_PSMENUFOLDERDELETE' => true,
                                        '_PSMENUPUBLISHALBUM' => true ),
                    'folderID' => $folderID
                )
        );

    $data['maximum_file_size'] = xarModGetVar('uploads','maximum_upload_size');
    $data['title'] = xarMl('Add images to album');
    $data['imagenum'] = 8; //number of images that can be uploaded at once
    return $data;
}

?>
