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

function photoshare_user_moveimage()
{
    if (!xarSecurityCheck('EditPhoto')) return;

    if (!xarVarFetch('iid',    'isset:int',$imageID,    NULL, XARVAR_GET_OR_POST)) {return;}
    if (!xarVarFetch('pos',    'isset:int',$position,    NULL, XARVAR_GET_OR_POST)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    $image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID));
    if (!isset($image)) return;

    if (!xarSecurityCheck('EditPhoto', 1, 'item', $imageID.':'.$image['owner'].':'.$image['parentfolder'])) return;

    $ok = xarModAPIFunc('photoshare',
                        'user',
                        'moveimage',
                        array('image'  => $image,
                            'position' => $position) );

    if (!isset($ok)) return;

    xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $image['parentfolder'])));
    return true;
}

?>
