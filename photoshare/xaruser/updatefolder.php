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

function photoshare_user_updatefolder()
{
    if (!xarSecurityCheck('EditFolder')) return;

    if (!xarVarFetch('fid',            'isset:int',$folderID,            NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('title',        'notempty',    $title,             NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('description',    'isset',       $description,         NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('viewTemplate','isset',       $viewTemplate,         NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('hideframe',    'isset:checkbox',   $hideframe, false, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('blockfromlist','isset:checkbox',  $blockfromlist, false, XARVAR_POST_ONLY)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $tmpParId = isset($parentFolderID) ? $parentFolderID : "all";
    $folder = xarModAPIFunc('photoshare', 'user', 'getfolders', array('folderID' => $folderID ));

    if (!isset($folder))
        return;

    if (!xarSecurityCheck('EditFolder', 1, 'folder', $folder['id'].':'.$folder['owner'].':'.$tmpParId)) return;

    if (!xarModGetVar('photoshare', 'allowframeremove'))
        $hideframe = false;

    $ok = xarModAPIFunc('photoshare',
                                'user',
                                'updatefolder',
                                array(    'folderID'         => $folderID,
                                        'title'            => $title,
                                        'description'    => $description,
                                        'viewTemplate'    => $viewTemplate,
                                        'hideframe'        => $hideframe,
                                        'blockfromlist'    => $blockfromlist
                                        ) );

    if (!isset($ok))
        return;

    xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $folderID)));
    return $ok;
}

?>
