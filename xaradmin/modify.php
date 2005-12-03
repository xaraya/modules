<?php
/**
 * File: $Id:
 *
 * Standard function to modify a text
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
 * modify a text
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param  $ 'tid' the id of the text to be modified
 */
function bible_admin_modify($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('tid', 'id', $tid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, $sname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str:1:', $lname, $lname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    // get text
    $args = array();
    if (!empty($tid)) $args['tid'] = $tid;
    if (!empty($sname)) $args['sname'] = $sname;
    $args['state'] = 'all';
    $text = xarModAPIFunc('bible', 'user', 'get', $args);
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditBible', 1, 'Text', "$text[sname]:$text[tid]")) return;

    // get text vars
    if (!isset($sname)) $sname = $text['sname'];
    if (!isset($lname)) $lname = $text['lname'];
    $id = $text['tid'];
    $type = $text['type'];

    // get other vars
    $authid = xarSecGenAuthKey();

    // get hooks
    $item = $text;
    $item['module'] = 'bible';
    $hookoutput = xarModCallHooks('item', 'modify', $id, $item);

    // initialize template data
    $data = xarModAPIFunc('bible', 'admin', 'menu');

    // set template vars
    $data['id']      = $id;
    $data['sname']   = $sname;
    $data['lname']   = $lname;
    $data['type']    = $type;
    $data['authid']  = $authid;
    $data['invalid'] = $invalid;
    $data['return']  = $return;

    return $data;
}

?>
