<?php
 /**
 * File: $Id:
 *
 * Display GUI for passage lookup
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */

/**
 * display GUI for passage lookup
 */
function bible_user_lookup($args)
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('sname', 'str:0', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int', $numitems, '', XARVAR_NOT_REQUIRED)) return;

    // set page title
    xarTplSetPageTitle(xarML('Passage Lookup'));

    // get active texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
        array('state' => 2, 'type' => 1, 'order' => 'sname')
    );

    // if no texts, we have to throw some kind of error
    if (empty($texts)) {
        // API function failed, so return false
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return;
        // No API error, so we must not have any texts available.  Send system message.
        } else {
            $msg = xarML('No Bible texts are available for searching or passage lookup!  '
                . 'Sorry, I am unable to proceed.');
            xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
            return;
        }
    }

    // get text
    $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));
    $tid = $text['tid'];

    // get database parameters
    list($textdbconn, $texttable) = xarModAPIFunc(
        'bible', 'user', 'getdbconn', array('tid' => $tid)
    );

    // get book names
    list($booknames) = xarModAPIFunc(
        'bible', 'user', 'getaliases', array('type' => 'display')
    );

    // get default text for dropdown list
    if (empty($sname)) {
        $sname = xarSessionGetVar('bible_sname');
        if (empty($sname)) {
            // none is set for this session, so use the first one in the texts list
            $sname = $texts[key($texts)]['sname'];
            xarSessionSetVar('bible_sname', $sname);
        }
    }

    // initialize template data
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'lookup'));

    $data['sname'] = $sname;
    $data['texts'] = $texts;
    $data['booknames'] = $booknames;

    return $data;

}

?>
