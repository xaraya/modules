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
 * Create hook function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 */
function crispbb_userapi_createhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'userapi', 'createhook', 'crispBB');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $extrainfo;
    }

    if (empty($modname) || !is_string($modname)) {
        if (isset($extrainfo) && is_array($extrainfo) &&
            isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'userapi', 'createhook', 'crispBB');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $extrainfo;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    $var_to_look_for = $modname;
    if (!empty($itemtype)) {
        $var_to_look_for .= '_' . $itemtype;
    }
    $var_to_look_for .= '_hooks';
    $string = xarModGetVar('crispbb', $var_to_look_for);
    if (empty($string) || !is_string($string)) {
        $string = xarModGetVar('crispbb', 'crispbb_hooks');
    }
    $settings = !empty($string) && is_string($string) ? unserialize($string) : array();

    $data = array();
    $data['fid'] = !empty($settings['fid']) ? $settings['fid'] : NULL;
    $data['postsperpage'] = !empty($settings['postsperpage']) ? $settings['postsperpage'] : 0;
    $data['quickreply'] = !empty($settings['quickreply']) ? $settings['quickreply'] : false;
    $data['newaction'] = !empty($settings['newaction']) ? $settings['newaction'] : 0;

    if (empty($tid) || !is_numeric($tid)) {
        if (empty($data['fid'])) return $extrainfo;
        if (empty($data['newaction'])) return $extrainfo;
        $forum = xarModAPIFunc('crispbb', 'user', 'getforum', array('fid' => $data['fid'], 'privcheck' => true));
        if ($forum == 'BAD_DATA' || $forum == 'NO_PRIVILEGES') return $extrainfo;
        if (empty($forum['newtopicurl'])) return $extrainfo;
        /*
        if (!$tid = xarModAPIFunc('crispbb', 'user', 'createtopic',
            array(
                'fid' => $fid,
                'ttitle' => $ttitle,
                'pdesc' => $pdesc,
                'ptext' => $ptext,
                'towner' => $uid,
                'tstatus' => $tstatus,
                'ttype' => $ttype,
                'topicstype' => $topicstype,
                'tsettings' => $tsettings,
                'psettings' => $psettings,
                'ptime' => $now
            ))) return;
        */
    }

    if (empty($tid)) return $extrainfo;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $hookstable = $xartable['crispbb_hooks'];

    $nextId = $dbconn->GenId($hookstable);

    $query = "INSERT INTO $hookstable (
              xar_hid,
              xar_moduleid,
              xar_itemtype,
              xar_itemid,
              xar_tid
              )
            VALUES (?,?,?,?,?)";

    $bindvars = array();
    $bindvars[] = $nextId;
    $bindvars[] = $modid;
    $bindvars[] = $itemtype;
    $bindvars[] = $objectid;
    $bindvars[] = $tid;

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $hid = $dbconn->PO_Insert_ID($hookstable, 'xar_hid');

    $extrainfo['crispbb_hid'] = $hid;
    $extrainfo['crispbb_tid'] = $tid;

    return $extrainfo;
}
?>