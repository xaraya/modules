<?php
/**
 * ebulletin module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ebulletin Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author andrea moro 
 */
/**
 * create new issues as scheduled  
 *
 * @author andream 
 * @access public
 * @args $scheduler: min value of scheduler column TODO:explain
 */
function ebulletin_schedulerapi_createissues($args)
{
    extract($args);
	if (!($pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('scheduler'=> 1)))) {
	    return; 
	} 
	
	// now issue each publication

    if (!xarVarFetch('issuedate', 'str:10:10', $issuedate, date('Y-m-d'), XARVAR_NOT_REQUIRED)) return;
	foreach ($pubs as $pub) {
	    $data = array();
	    $data['pid']       = $pub['id'];
	    $data['issuedate'] = $issuedate;
	    $data['subject']   = '';
	    $data['body_html'] = '';
	    $data['body_txt']  = '';
	
	    // let API function do the creating
	    $id = xarModAPIFunc('ebulletin', 'admin', 'createissue', $data);
		if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

	}
    return true;

}

?>
