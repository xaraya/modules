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

function photoshare_user_deletefolder()
{
    if(!xarVarFetch('fid', 'int', $folderID,  NULL, XARVAR_GET_OR_POST)) {return;}
    if (!xarSecurityCheck('DeleteFolder')) return;

    $data = array();

    $data['folder'] = xarModAPIFunc('photoshare',
                                'user',
                                'getfolders',
                                array( 'folderID' => $folderID, 'prepareForDisplay' => true));
    if (!isset($data['folder'])) return;

    $data['folderID'] = $data['folder']['id'];

    $data['trail'] = xarModAPIFunc('photoshare',
                            'user',
                            'getfoldertrail',
                            array( 'folderID' => $folderID ));
    if (!isset($data['trail'])) return;

    if (!xarSecurityCheck('DeleteFolder', 0, 'folder', $data['folder']['id'].':'.$data['folder']['owner'].':'.$data['folder']['parentFolder']))
        return;

    // Add top menu
    $data['menuitems'] = xarModAPIFunc('photoshare', 'user', 'makemainmenu',
            array(    'gotoCurrentFolder' => true,
                    'menuHide' => false
                )
        );

    $data['actionUrl'] = xarModURL('photoshare', 'user', 'deletefolder_confirmed', array('fid' => $folderID));
    $data['title'] = xarMl('Delete album');

    return $data;
}

?>
