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
 *
 * @NOTE: this function is deprecated, use the crispbb_forums dd object and its methods instead
 */
function crispbb_adminapi_create($args)
{
    // @TODO: Deprecate this
    extract($args);

    $invalid = array();

    if (!isset($fname) || !is_string($fname) || empty($fname) || strlen($fname) > 100) {
        $invalid[] = 'fname';
    }

    if (!isset($fdesc) || !is_string($fdesc) || strlen($fdesc) > 255) {
        $invalid[] = 'fdesc';
    }

    if (count($invalid) > 0) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array(join(', ', $invalid), 'admin', 'create', 'crispBB');
        throw new BadParameterException($vars, $msg);
        return;
    }

    if (!isset($fstatus) || !is_numeric($fstatus)) {
        $fstatus = 0;
    }

    if (!isset($ftype) || !is_numeric($ftype)) {
        $ftype = 0;
    }

    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'fsettings,fprivileges'));
    if (empty($fsettings) || !is_array($fsettings)) {
        $fsettings = xarMod::apiFunc('crispbb', 'user', 'getsettings',
            array('setting' => 'fsettings'));
    }

    if (empty($fprivileges) || !is_array($fprivileges)) {
        $fprivileges = xarMod::apiFunc('crispbb', 'user', 'getsettings',
            array('setting' => 'fprivileges'));
    }

    if (empty($fowner) || !is_numeric($fowner)) {
        $fowner = xarModVars::get('roles', 'admin');
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $forumstable = $xartable['crispbb_forums'];

    $nextId = $dbconn->GenId($forumstable);

    $query = "INSERT INTO $forumstable (
              id,
              fname,
              fdesc,
              fstatus,
              ftype,
              fowner,
              forder,
              fsettings,
              fprivileges,
              lasttid
              )
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array();
    $bindvars[] = $nextId;
    $bindvars[] = $fname;
    $bindvars[] = $fdesc;
    $bindvars[] = $fstatus;
    $bindvars[] = $ftype;
    $bindvars[] = $fowner;
    $bindvars[] = 0;
    $bindvars[] = serialize($fsettings);
    $bindvars[] = serialize($fprivileges);
    $bindvars[] = 0;

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $fid = $dbconn->PO_Insert_ID($forumstable, 'id');

    // set the forum order
    if (!xarMod::apiFunc('crispbb', 'admin', 'update',
        array('fid' => $fid, 'forder' => $fid, 'nohooks' => true))) return;

    // create itemtypes for this forum
    $forumtype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'forum'));
    $topicstype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'topics'));
    $poststype = xarMod::apiFunc('crispbb', 'admin', 'createitemtype',
        array('fid' => $fid, 'component' => 'posts'));

    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));

    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $numcats = count($basecats);
    $parentcat = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    if (!empty($numcats)) {
        // get mastercids back
        $mastercids = array_keys($basecats);
        $mastercids = join(';', $mastercids);
        if (empty($mastercids)) {
            $mastercids = '';
        }
    } else {
        $mastercids = '';
    }
    xarModVars::set('crispbb', 'number_of_categories.'.$forumtype, $numcats);
    xarModVars::set('crispbb', 'mastercids.'.$forumtype, $mastercids);

    // synch hooks
    $itemtypes = xarMod::apiFunc('crispbb', 'user', 'getitemtypes');

    // call hooks for new forum
    $item = $args;
    $item['module'] = 'crispbb';
    $item['itemtype'] = $forumtype;
    $item['itemid'] = $fid;
    $item['cids'] = $cids;
    xarModCallHooks('item', 'create', $fid, $item);

    // let the tracker know this forum was created
    $fstring = xarModVars::get('crispbb', 'ftracking');
    $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
    $ftracking[$fid] = time();
    xarModVars::set('crispbb', 'ftracking', serialize($ftracking));

    // return the new forum id
    return $fid;
}
?>