<?php
/**
 * File: $Id:
 * 
 * Show library of all available texts
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_user_library()
{
	if (!xarSecurityCheck('ViewBible')) return;

	if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;

	if (!empty($objectid)) {
		$tid = $objectid;
	}

	// validate variables
	$invalid = array();
	if (!empty($sname) && is_numeric($sname)) {
		$invalid[] = 'sname';
	}
	if (!empty($tid) && !is_numeric($tid)) {
		$invalid[] = 'tid';
	}
	if (count($invalid) > 0) {
		$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
			join(',', $invalid), 'user', 'library', 'bible');
		xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
			new SystemException($msg));
		return;
	}

	// initialize template data
	$data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'library')); 

	// if no text is given, display all available texts
	if (empty($sname) && empty($tid)) {
		// get active texts
		$texts = xarModAPIFunc('bible', 'user', 'getall',
							   array('state' => 2,
									 'type' => 1,
									 'order' => 'sname'));
		$data['texts'] = $texts;

		// show Strong's texts if available
		// TODO: DO WE NEED THIS?  What if Concordance page did this for us?
		$strongs = xarModAPIFunc('bible', 'user', 'getall',
								 array('state' => 2,
									   'type' => 2,
									   'order' => 'sname', 'sort' => 'desc'));
		$data['strongs'] = $strongs;


	// otherwise, display info on the requested text
	} else {

	    // work out the sname (could have been passed the tid or the sname)
		if (empty($sname)) {
			$text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid));
			$sname = $text['sname'];
		} else {
			$text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));
		}
	    $data['text'] = $text;
		$data['sname'] = $sname;

		// get table of contents data
		// TODO: DO WE NEED THIS ALONG WITH get() ABOVE?)
		$data['toc'] = xarModAPIFunc('bible', 'user', 'lookup',
								array('sname' => $text['sname']));

	}

	// set page title
	xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Library'))); 

	// Return the template variables defined in this function
	return $data; 
} 

?>
