<?php
/**
 * Display some photos of a Flickr user
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Flickring module
 */

require_once("modules/flickring/xarclass/phpFlickr/phpFlickr.php");

function flickring_user_showuserphotos()
{
    if (!xarVarFetch('username', 'str:1:', $data['username'], NULL, XARVAR_POST_ONLY, XARVAR_PREP_FOR_DISPLAY)) return;

	$f = new phpFlickr("[API Key]");

	/* Forget this for now
	$f->enableCache(
		"db",
		"mysql://[DB User]:[DB Password]@[DB Server]/[DB Name]"
	);
	*/

	if (!empty($data['username'])) {
		// Find the NSID of the username inputted via the form
		$nsid = $f->people_findByUsername($data['username']);

		// Get the friendly URL of the user's photos
		$data['photos_url'] = $f->urls_getUserPhotos($nsid);

		// Get the user's first 36 public photos
		$data['photos'] = $f->people_getPublicPhotos($nsid, NULL, 36);
//		var_dump($data['photos']);exit;

		// Lazy out: send the object to the template
		$data['f'] = $f;
	}
	return $data;
}
