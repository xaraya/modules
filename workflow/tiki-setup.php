<?php
/**
 * Fake Tiki setup file so that TikiWiki modules can work under Xaraya
 */

if (!defined('TIKI_LOADED')) {
/**
 * Dummy Smarty class to trap $smarty->assign() and $smarty->display()
 * calls from inside lib/Galaxia, and save them for the BL template
 */
class Tiki_Smarty {
    var $tplData;

    function Smarty2BL()
    {
        $this->tplData = array();
    }

    function assign($name,$value)
    {
       $this->tplData[$name] = $value;
    }

    function assign_by_ref($name,&$value)
    {
       $this->tplData[$name] =& $value;
    }

    function display($template)
    {
        echo var_dump($this->tplData);
        if (substr($template,-8) == 'tiki.tpl') {
           echo $this->tplData['mid'];
        } elseif (substr($template,-9) == 'error.tpl') {
           die($this->tplData['msg']);
        } else {
           echo $template;
        }
        echo xarTplModule('workflow','admin','error',$this->tplData);
        die;
    }
}

/**
 * Dummy users class to retrieve Xaraya users/groups
 */
class Tiki_UsersLib
{
    function get_users($startnum = 0, $numitems = -1, $sort = 'login_asc', $find = '')
    {
        $users = array();
        $users['data'] = array();
        $startnum++;
        if (!empty($find)) {
            $selection = " AND xar_name LIKE '%" . xarVarPrepForStore($find) . "%'";
        } else {
            $selection = '';
        }
        $users['cant'] = xarModAPIFunc('roles','user','countall');
    // don't show thousands of users here without filtering
        if ($numitems < 0 && $users['cant'] > 1000 && empty($find)) {
            return $users;
        }
        $roles = xarModAPIFunc('roles','user','getall',
                               array('startnum' => $startnum,
                                     'numitems' => $numitems,
                                     'selection' => $selection,
                                     'order' => 'name'));
        foreach ($roles as $role) {
            $users['data'][] = array('user' => $role['name'],
                                     'login' => $role['uname'],
                                     'userId' => $role['uid']);
        }
        $users['cant'] = xarModAPIFunc('roles','user','countall');
        return $users;
    }

    function get_groups($startnum = 0, $numitems = -1, $sort = 'groupName_asc', $find = '')
    {
        $groups = array();
        $groups['data'] = array();
        $roles = xarModAPIFunc('roles','user','getallgroups');
        foreach ($roles as $role) {
            $groups['data'][] = array('groupName' => $role['name'],
                                      'groupId' => $role['uid']);
        }
        $groups['cant'] = xarModAPIFunc('roles','user','countgroups');
        return $groups;
    }

    function get_group_users($group = '')
    {
        $users = array();
        if (empty($group)) return $users;
        if (!is_numeric($group)) {
            $groupinfo = xarModAPIFunc('roles','user','get',
                                       array('name' => $group,
                                             'type' => 1));
            if (empty($groupinfo)) return $users;
            $groupid = $groupinfo['uid'];
        } else {
            $groupid = $group;
        }
        $roles = xarModAPIFunc('roles','user','getusers',
                               array('uid' => $groupid));
        foreach ($roles as $role) {
            $users[] = $role['uid'];
        }
        return $users;
    }
}

/**
 * Dummy DB class to trap DB::isError() calls from inside lib/Galaxia
 */
class DB extends ADOConnection
{
    function isError($result)
    {
        if (!isset($result)) return true;
        return false;
    }
}

/**
 * Frequent fetchRow() argument used inside lib/Galaxia
 */
if (!defined('DB_FETCHMODE_ASSOC')) {
    define('DB_FETCHMODE_ASSOC', 2);
}

/**
 * Translate strings and variables
 */
function tra($what)
{
    return xarML($what);
}

function httpPrefix()
{
    return xarServerGetBaseURL();
}

// Specify the location of the lib/Galaxia directory
define('GALAXIA_DIR', 'modules/workflow/lib/Galaxia');

// Specify the directory where dot and neato are located for GraphViz
// (see http://www.research.att.com/sw/tools/graphviz/ for download)
// Note the trailing / here !!!
define('GRAPHVIZ_DIR', 'd:/wintools/ATT/GraphViz/bin/');
//define('GRAPVIZ_DIR', '');

define('TIKI_LOADED', 1);
}

/**
 * Common Tiki database connection handler
 */
list($dbconn) = xarDBGetConn();
$tikilib =& $dbconn;
$dbTiki =& $dbconn;

/* from db/tiki-db.php
define('ADODB_FORCE_NULLS', 1);
define('ADODB_ASSOC_CASE', 2);
define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
include_once ('adodb.inc.php');
//include_once('adodb-error.inc.php');
//include_once('adodb-errorhandler.inc.php');
//include_once('adodb-errorpear.inc.php');
include_once ('adodb-pear.inc.php');
global $ADODB_FETCH_MODE;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
*/

// Set the fetch mode to assoc by default
$oldmode = $dbTiki->SetFetchMode(DB_FETCHMODE_ASSOC);

// Create a dummy $smarty
$smarty = new Tiki_Smarty();

// Create a dummy $userlib
$userlib = new Tiki_UsersLib();

// Define a dummy $style_base
$style_base = 'test';

// Retrieve the current user
$user = xarUserGetVar('uid');

/*
$include_path = ini_get('include_path');
$include_path .= ';./modules/workflow'; // : on non-Windows
ini_set('include_path',$include_path);
*/

// Some other variables used inside Galaxia
$feature_help = 'n';
$feature_workflow = 'y';

if (xarSecurityCheck('AdminWorkflow',0)) {
    $tiki_p_admin_workflow = 'y';
    $tiki_p_use_workflow = 'y';
} else {
    $tiki_p_admin_workflow = 'n';
    if (xarSecurityCheck('ReadWorkflow',0)) {
        $tiki_p_use_workflow = 'y';
    } else {
        $tiki_p_use_workflow = 'n';
    }
}
$maxRecords = 20;
$direct_pagination = 'y';
?>
