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

function photoshare_user_editimage()
{
    if (!xarSecurityCheck('EditPhoto')) return;
    if(!xarVarFetch('iid', 'isset:int', $imageID,  NULL, XARVAR_GET_OR_POST)) {return;}

    $data = array();

    $data['image'] = xarModAPIFunc('photoshare',
                        'user',
                        'getimages',
                        array( 'imageID' => $imageID, 'prepareForDisplay' => true));

    if (!isset($data['image'])) return;

    $owner = $data['image']['owner'];
    $folderID = $data['image']['parentfolder'];
    if (!xarSecurityCheck('EditPhoto', 1, 'item', "$imageID:$owner:$folderID")) return;

    // Add top menu
    $data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
            array(    'gotoCurrentFolder' => true,
                    'menuHide' => false,
                    'folderID' => $folderID
                )
        );

    $data['trail'] = xarModAPIFunc('photoshare',
                            'user',
                            'getfoldertrail',
                            array( 'folderID' => $folderID ));
    if (!isset($data['trail'])) return;

    $data['maximum_file_size'] = xarModGetVar('uploads','maximum_upload_size');
    $data['title'] = xarMl('Edit image');
    $data['imagenum'] = 8; //number of images that can be uploaded at once
    return $data;
}

?>
