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
    extract($args);

    if (!xarVarFetch('sname', 'str:0', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int', $numitems, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewBible')) return;

    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'lookup')); 

    xarTplSetPageTitle(xarML('Passage Lookup')); 

    // get active texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('state' => 2,
                                 'type' => 1,
                                 'order' => 'sname'));

    if (empty($texts)) {
        $data['status'] = xarML('No texts are installed and active!  Please contact the website administrator.');
        return $data;
    }
    $data['texts'] = $texts;

    // set the default shortname of the text
    if (empty($sname)) {
        $sname = xarSessionGetVar('bible_sname');
        if (empty($sname)) {
            // none is set for this session, so use the first one in the texts list
            $sname = $texts[key($texts)]['sname'];
            xarSessionSetVar('bible_sname', $sname);
        }
    }
    $data['sname'] = $sname;

    // get text
    $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));
    $tid = $text['tid'];

    // get database parameters
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                     array('tid' => $tid));

    // get book names
    list($booknames) = xarModAPIFunc('bible', 'user', 'getaliases', array('type' => 'display'));
    $data['booknames'] = $booknames;


    // Return the template variables defined in this function
    return $data;

} 

?>
