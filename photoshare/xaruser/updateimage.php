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

function photoshare_user_updateimage()
{
    if (!xarSecurityCheck('EditPhoto')) return;

    if (!xarVarFetch('iid',            'isset:int',$imageID,            NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('fid',            'isset:int',$folderID,            NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('imagetitle',    'notempty',    $title,             NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('imagedescription','isset',$description,         NULL, XARVAR_POST_ONLY)) {return;}
    if (!xarVarFetch('imagefile',    'isset',       $fileInfo,             NULL, XARVAR_POST_ONLY | XARVAR_DONT_SET)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarSecurityCheck('EditPhoto', 1, 'item', $imageID.':'.xarUserGetVar('uid').':'.$folderID)) return;

    $ok = xarModAPIFunc('photoshare',
                        'user',
                        'updateimage',
                        array('imageID'       => $imageID,
                            'title'         => $title,
                            'description'   => $description,
                            'folderID'        => $folderID,
                            'owner'            => xarUserGetVar('uid'),
                            'inputfield'    => 'imagefile'));

    if (!isset($ok))
        return;

    xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $folderID)));
    return true;
}

?>
