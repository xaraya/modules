<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
 */

function photoshare_user_deleteimage()
{
	if(!xarVarFetch('iid', 'isset:int', $imageID,  NULL, XARVAR_GET_OR_POST)) {return;}
	// Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

	if (!xarSecurityCheck('DeletePhoto')) return;

	$data = array();

	$image = xarModAPIFunc('photoshare',
						'user',
						'getimages',
						array( 'imageID' => $imageID, 'getForList' => false));

	if (!isset($image)) return;

	if (!xarSecurityCheck('DeletePhoto', 1, 'item', "$image[id]:$image[owner]:$image[parentfolder]")) return;

	$ok = xarModAPIFunc('photoshare',
						'user',
						'deleteimage',
						array( 'imageID' => $imageID ));

	if (!isset($ok)) return;

	xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $image['parentfolder'])));
	return true;
}

?>
