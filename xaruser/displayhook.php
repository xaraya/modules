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
 * The display hook function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array $data An array with the data for the template
 * @param string hook_mod name of the hook module when called by API DEFAULT current module
 * @param integer hook_itemtype itemtype of the hook module OPTIONAL, DEFAULT 0
 * @param integer hook_itemid itemid of the hook module item being displayed REQUIRED OR
 * @param integer objectid itemid of the hook module when called by hooks REQUIRED
 * @param array extrainfo array containing modname and itemtype when called by hooks OPTIONAL
 */
function crispbb_user_displayhook($args)
{

    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (empty($modname)) {
        if (empty($extrainfo['module'])) {
            $modname = xarMod::getName();
        } else {
            $modname = $extrainfo['module'];
        }
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'user', 'displayhook', 'crispBB');
        //throw new BadParameterException($vars, $msg);
        // don't throw an error here, life in hooks goes on...
        return;
    }

    if (empty($itemtype)) {
        $itemtype = 0;
        if (isset($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
    }


    if (isset($objectid) && is_numeric($objectid)) {
        $itemid = $objectid;
    } else {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('object ID', 'user', 'displayhook', 'crispBB');
        //throw new BadParameterException($vars, $msg);
     // life goes on in hook modules, so just return false
       return;
    }

    $data = array();

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hookstable = $xartable['crispbb_hooks'];

    $select = array();
    $where = array();
    $bindvars = array();
    $select[] = $hookstable . '.tid';
    $from = $hookstable;
    $where[] = $hookstable . '.moduleid = ?';
    $bindvars[] = $modid;
    if (!empty($itemtype)) {
    $where[] = $hookstable . '.itemtype = ?';
    $bindvars[] = $itemtype;
    }
    $where[] = $hookstable . '.itemid = ?';
    $bindvars[] = $itemid;

    $query = 'SELECT ' . join(', ', $select);
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    if ($result->EOF) {
    } else {
        list($tid) = $result->fields;
    }
    $result->Close();

    if (!empty($tid)) {
        $topic = xarMod::apiFunc('crispbb', 'user', 'gettopic', array('tid' => $tid, 'privcheck' => true));
        if ($topic == 'BAD_DATA' || $topic == 'NO_PRIVILEGES') return;
        $data = $topic;
        $data['newtopicurl'] = '';
    }

    $var_to_look_for = $modname;
    if (!empty($itemtype)) {
        $var_to_look_for .= '_' . $itemtype;
    }
    $var_to_look_for .= '_hooks';
    $string = xarModVars::get('crispbb', $var_to_look_for);
    if (empty($string) || !is_string($string)) {
        $string = xarModVars::get('crispbb', 'crispbb_hooks');
    }
    $settings = !empty($string) && is_string($string) ? unserialize($string) : array();

    $data['fid'] = !empty($settings['fid']) ? $settings['fid'] : NULL;
    $data['postsperpage'] = isset($settings['postsperpage']) ? $settings['postsperpage'] : 0;
    $data['quickreply'] = isset($settings['quickreply']) ? $settings['quickreply'] : false;

    if (empty($topic)) {
        if (empty($data['fid'])) return;
        $forum = xarMod::apiFunc('crispbb', 'user', 'getforum', array('fid' => $data['fid'], 'privcheck' => true));
        if ($forum == 'NO_PRIVILEGES' || $forum == 'BAD_DATA') return;
        if (!empty($forum['newtopicurl'])) {
            $data['newtopicurl'] = xarController::URL('crispbb', 'user', 'newtopic',
                array(
                    'fid' => $data['fid'],
                    'modname' => $modname,
                    'itemtype' => $itemtype,
                    'itemid' => $itemid,
                    //'return_url' => xarServer::getCurrentURL(),
                    'authid' => xarSec::genAuthKey('crispbb')
                ));
        }
    }

    if (!empty($data['postsperpage']) && !empty($data['numreplies'])) {
        $startnum = $data['numreplies']+1 > $data['postsperpage'] ? ($data['numreplies']+1 - $data['postsperpage']) + 1 : 1;
        $data['startnum'] = $startnum;
    }
    if (!empty($data['quickreply'])) {
        $data['return_url'] = xarServer::getCurrentURL();
    }
    return xarTpl::module('crispbb', 'user', 'displayhook', $data);
}
?>