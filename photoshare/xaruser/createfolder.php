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

function photoshare_user_createfolder()
{
    if (!xarSecurityCheck('AddFolder')) return;

    if (!xarVarFetch('pid',            'int',        $parentFolderID,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('title',        'notempty',    $title)) {return;}
    if (!xarVarFetch('description',    'isset',       $description,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('viewTemplate','isset',       $viewTemplate,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('hideframe',    'isset:checkbox',   $hideframe, false, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('blockfromlist','isset:checkbox',      $blockfromlist, false)) {return;}

    $tmpParId = isset($parentFolderID) ? $parentFolderID : "all";
    if (!xarSecurityCheck('AddFolder', 1, 'folder', "all:all:$tmpParId")) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModGetVar('photoshare', 'allowframeremove'))
        $hideframe = false;

    $newFolderID = xarModAPIFunc('photoshare',
                                'user',
                                'createfolder',
                                array('parentFolderID'      => $parentFolderID,
                                        'title'          => $title,
                                        'description'    => $description,
                                        'viewTemplate'   => $viewTemplate,
                                        'hideframe'         => $hideframe,
                                        'blockfromlist'  => $blockfromlist,
                                        'owner'          => xarUserGetVar('uid')
                                        ) );

    if (!isset($newFolderID))
        return;

    xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $newFolderID)));
    return true;
}

?>
