<?php
/**
 * Window Module ADD URL
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window Module
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Window Module Development Team
 */
function window_adminapi_addurl($args)
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('reg_user_only', 'int', $reg_user_only, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('open_direct', 'int', $open_direct, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('use_fixed_title', 'int', $use_fixed_title, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize', 'int', $auto_resize, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vsize', 'int', $vsize, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hsize', 'str', $hsize, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('host', 'str', $host, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('alias', 'str', $alias, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('label', 'str', $label, "Xaraya Window", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str', $description, "Xaraya Window Display", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'int', $data['status'], 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'id', $itemid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lang_action', 'str', $lang_action, 'Add', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('window_status', 'str', $window_status, 'add', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'int', $status, 1, XARVAR_NOT_REQUIRED)) return;
    extract($args);

    if (!xarSecConfirmAuthKey()) return;

//    $data = array();
//    $data['authid'] = xarSecGenAuthKey();
    if ($host !='' && $alias != '') {
        //Sanitize Url
        //To do: more complex checking
        $host_arr = parse_url($host);

        //Get rid of whitespaces
        $alias = str_replace(' ', '_', $alias);

        if (is_array($host_arr)) {
            $host = '';
            if (empty($host_arr['scheme'])) {
                $host = 'http://';
            } else {
                $host = $host_arr['scheme'] . '://';
            }

            if (!empty($host_arr['host']))  $host .= $host_arr['host'];
            if (!empty($host_arr['port']))  $host .= ':' . $host_arr['port'];
            if (!empty($host_arr['path']))  $host .= '' . $host_arr['path'];
            if (!empty($host_arr['query'])) $host .= '?' . $host_arr['query'];

            $data['message'] = '';
        } else {
            $data['message'] = xarML('Bad URL');
            return false;
        }
       
       // $urlitem = xarModAPIFunc('window','user','getall',array('name'=>$host,'alias' => $alias));

       $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $urltable = $xartable['window'];

        // Check If this URL or Alias allready exists in DB
        // caveman says:
        // This check should happen regardless of either an edit or
        // an add was we don't won't duplicate values in the database.
        $query = "SELECT xar_id FROM $urltable
                  WHERE xar_name = ?
                  OR xar_alias = ?";
        $bindvars = array($host, $alias);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return false;

/*

        if (isset($urlitem) && $window_status == 'add' && count($urlitem)==1) { //we don't want this to find anything
            return false; //do appropriate error here
        } elseif (isset($urlitem) && count($urlitem) != 1) { //we are editing and need to have one that exists and only one
            return false; //do appropriate error here
        }
*/
      /*
        // Check for $hsize
        if(strstr($hsize, "%")) {
            $hzise1 = (int) $hsize;
            $hsize1 = $hsize1."%";
        } else {
            $hzise1 = (int) $hsize;
        }
        */
        $fargs['name']            = $host;
        $fargs['alias']           = $alias;
        $fargs['reg_user_only']   = $reg_user_only;
        $fargs['open_direct']     = $open_direct;
        $fargs['use_fixed_title'] = $use_fixed_title;
        $fargs['auto_resize']     = $auto_resize;
        $fargs['vsize']           = $vsize;
        $fargs['hsize']           = $hsize;
        $fargs['status']          = $status;
        $fargs['label']           = $label;
        $fargs['description']     = $description;

        if ($window_status == 'add') {
            $itemid = xarModAPIFunc('window', 'admin', 'create', $fargs);
            if (!$itemid) return false;
        } else {
            $fargs['itemid'] = $itemid;
            if (!xarModAPIFunc('window', 'admin', 'update', $fargs)) {
                return false;
            }
        }

    }
        xarResponseRedirect(xarModURL('window', 'admin', 'newurl'));
}
?>