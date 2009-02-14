<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * Update an example item
 *
 * @author the Example module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function accessmethods_adminapi_update($args)
{
    extract($args);
    
    if(is_array($related_sites)) $related_sites = serialize($related_sites);

    $invalid = array();
    if (!isset($siteid) || !is_numeric($siteid)) {
        $invalid[] = 'Access Method ID';
    }
    if (!isset($site_name) || !is_string($site_name)) {
        $invalid[] = 'site_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('accessmethods',
                            'user',
                            'get',
                            array('siteid' => $siteid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditAccessMethods', 1, 'All', "$item[webmasterid]")) {
        return;
    }
    if (!xarSecurityCheck('EditAccessMethods', 1, 'All', "$webmasterid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $query = "UPDATE $accessmethodstable
            SET clientid = ?, 
                  webmasterid = ?,
                  site_name = ?,
                  url = ?,
                  description = ?,
                  accesstype = ?,
                  sla = ?,
                  accesslogin = ?,
                  accesspwd = ?,
                  related_sites = ?,
                  lastmodifiedby = ?,
                  lastmodifiedon = NOW()
            WHERE siteid = ?";

    $bindvars = array(
              $clientid,
              $webmasterid,
              $site_name,
              $url,
              $description,
              $accesstype,
              $sla,
              $accesslogin,
              $accesspwd,
              $related_sites,
              xarUserGetVar('uid'),
              $siteid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Access listing modified.";
    if($site_name != $item['site_name'])
        $logdetails .= "<br>Access listing name changed from ".$item['site_name']." to ".$site_name;
    if($clientid != $item['clientid'])
        $logdetails .= "<br>Access listing clientid changed from ".$item['clientid']." to ".$clientid;
    if($webmasterid != $item['webmasterid'])
        $logdetails .= "<br>Access listing webmasterid changed from ".$item['webmasterid']." to ".$webmasterid;
    if($url != $item['url'])
        $logdetails .= "<br>Access listing url changed from ".$item['url']." to ".$url;
    if($description != $item['description'])
        $logdetails .= "<br>Access listing description changed from ".$item['description']." to ".$description;
    if($accesstype != $item['accesstype'])
        $logdetails .= "<br>Access listing accesstype changed from ".$item['accesstype']." to ".$accesstype;
    if($sla != $item['sla'])
        $logdetails .= "<br>Access listing sla changed from ".$item['sla']." to ".$sla;
    if($accesslogin != $item['accesslogin'])
        $logdetails .= "<br>Access listing accesslogin changed from ".$item['accesslogin']." to ".$accesslogin;
    if($accesspwd != $item['accesspwd'])
        $logdetails .= "<br>Access listing accesspwd changed from ".$item['accesspwd']." to ".$accesspwd;

    $logid = xarModAPIFunc('accessmethods',
                        'log',
                        'create',
                        array('siteid'   => $siteid,
                            'userid'        => $userid,
                            'details'        => $logdetails,
                            'changetype'    => "MODIFIED"));
                            
    if($logid == false) return;
    
    $item['module'] = 'accessmethods';
    $item['itemid'] = $siteid;
    $item['name'] = $site_name;
    xarModCallHooks('item', 'update', $siteid, $item);

    return true;
}
?>
