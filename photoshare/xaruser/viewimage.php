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
 
function photoshare_user_viewimage()
{
	if (!xarSecurityCheck('ViewPhoto')) return;
	// Create list of folders
	if(!xarVarFetch('iid', 'int', $imageID,  NULL, XARVAR_GET_OR_POST)) {return;}

	$image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID));
	if (!isset($image)) return;

	$thumbsize = xarModGetVar('photoshare', 'thumbnailsize');
    return xarModAPIFunc('uploads',
                          'admin',
                          'download',
						  array('ulid'=>$image['uploadid'],
						  		'ulname' => '', //TODO: huh? why is this one required when it HAS to be empty!!
								'thumb' => 0, //AND THIS ONE TOO!
						  		'thumbwidth'=>$thumbsize,
						  		'thumbheight'=>$thumbsize
								));
}

?>
