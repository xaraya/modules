<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * Create a new forum
 *
 * This is a standard adminapi function to create a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  string   $args['fname']      forum name
 * @param  string   $args['fdesc']      forum description
 * @param  int      $args['fstatus']    forum status id
 * @param  int      $args['fowner']     forum owner id
 * @param  int      $args['forder']     forum order
 * @param  array    $args['fsettings']  forum settings
 * @return int forum id on success, false on failure
 * @throws BAD_PARAM, DATABASE_ERROR
 */
function crispbb_adminapi_create($args)
{
    extract($args);

    $invalid = array();

    if (!isset($fname) || !is_string($fname) || empty($fname) || strlen($fname) > 100) {
        $invalid[] = 'fname';
    }

    if (!isset($fdesc) || !is_string($fdesc) || strlen($fdesc) > 255) {
        $invalid[] = 'fdesc';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'crispBB');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!isset($fstatus) || !is_numeric($fstatus)) {
        $fstatus = 0;
    }

    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'fsettings,fprivileges'));
    if (empty($fsettings) || !is_array($fsettings)) {
        $fsettings = xarModAPIFunc('crispbb', 'user', 'getsettings',
            array('setting' => 'fsettings'));
    }

    if (empty($fprivileges) || !is_array($fprivileges)) {
        $fprivileges = xarModAPIFunc('crispbb', 'user', 'getsettings',
            array('setting' => 'fprivileges'));
    }

    if (empty($fowner) || !is_numeric($fowner)) {
        $fowner = xarModGetVar('roles', 'admin');
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $forumstable = $xartable['crispbb_forums'];

    $nextId = $dbconn->GenId($forumstable);

    $query = "INSERT INTO $forumstable (
              xar_fid,
              xar_fname,
              xar_fdesc,
              xar_fstatus,
              xar_fowner,
              xar_forder,
              xar_fsettings,
              xar_fprivileges
              )
            VALUES (?,?,?,?,?,?,?,?)";

    $bindvars = array();
    $bindvars[] = $nextId;
    $bindvars[] = $fname;
    $bindvars[] = $fdesc;
    $bindvars[] = $fstatus;
    $bindvars[] = $fowner;
    $bindvars[] = 0;
    $bindvars[] = serialize($fsettings);
    $bindvars[] = serialize($fprivileges);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $fid = $dbconn->PO_Insert_ID($forumstable, 'xar_fid');

    // set the forum order
    if (!xarModAPIFunc('crispbb', 'admin', 'update',
        array('fid' => $fid, 'forder' => $fid, 'nohooks' => true))) return;

    // create itemtypes for this forum
    $forumtype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'forum'));
    $topicstype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'topics'));
    $poststype = xarModAPIFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'posts'));

    $mastertype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));

    // create mastercids and numcats for this forum itemtype
    $numcats = xarModGetVar('crispbb', 'number_of_categories.'.$mastertype);
    if (!empty($numcats)) {
        // get mastercids back
        $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
        if (empty($mastercids)) {
            $mastercids = '';
        }
    } else {
        $mastercids = '';
    }
    xarModSetVar('crispbb', 'number_of_categories.'.$forumtype, $numcats);
    xarModSetVar('crispbb', 'mastercids.'.$forumtype, $mastercids);

    // synch hooks
    $itemtypes = xarModAPIFunc('crispbb', 'user', 'getitemtypes');

    // call hooks for new forum
    $item = $args;
    $item['module'] = 'crispbb';
    $item['itemtype'] = $forumtype;
    $item['itemid'] = $fid;
    $item['cids'] = $cids;
    xarModCallHooks('item', 'create', $fid, $item);

    // let the tracker know this forum was created
    $fstring = xarModGetVar('crispbb', 'ftracking');
    $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
    $ftracking[$fid] = time();
    xarModSetVar('crispbb', 'ftracking', serialize($ftracking));

    // return the new forum id
    return $fid;
}
?>