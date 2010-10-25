<?php
/**
 * Main user function, entry point
 * @package ckeditor
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_user_main() {

	// Security Check
	if (!xarSecurityCheck('ReadCKEditor')) return;

	return array(); 
}

?>