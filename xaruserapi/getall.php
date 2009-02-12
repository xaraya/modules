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
function accessmethods_userapi_getall($args)
{
    extract($args);

    if (!isset($sortby)) {
        $sortby = "";
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getall', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $sql = "SELECT siteid,
                  clientid,
                  webmasterid,
                  site_name,
                  url,
                  description,
                  accesstype,
                  sla,
                  accesslogin,
                  accesspwd,
                  related_sites
            FROM $accessmethodstable";

    $whereclause = array();
    if(!empty($clientid)) {
        $whereclause[] = "clientid = '".$clientid."'";
    }
    if(!empty($accesstype)) {
        $whereclause[] = "accesstype = '".$accesstype."'";
    }
    if(!empty($sla)) {
        $whereclause[] = "sla = '".$sla."'";
    }
    if(!empty($webmasterid)) {
        $whereclause[] = "webmasterid = '".$webmasterid."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
            
    switch($sortby) {
        case "accesstype":
            $sql .= " ORDER BY accesstype, site_name";
            break;
        case "sla":
            $sql .= " ORDER BY sla, site_name";
            break;
        case "client":
            $sql .= " ORDER BY clientid, site_name";
            break;
        case "site_name":
        default:
            $sql .= " ORDER BY site_name";
            break;
    }
    
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR: '.$sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sitelist = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($siteid,
              $clientid,
              $webmasterid,
              $site_name,
              $url,
              $description,
              $accesstype,
              $sla,
              $accesslogin,
              $accesspwd,
              $related_sites) = $result->fields;
        if (xarSecurityCheck('ReadAccessMethods', 0, 'Item', "$site_name:All:$siteid")) {
            $sitelist[] = array('siteid'        => $siteid,
                              'clientid'        => $clientid,
                              'webmasterid'     => $webmasterid,
                              'site_name'       => $site_name,
                              'url'             => $url,
                              'description'     => $description,
                              'accesstype'        => $accesstype,
                              'sla'             => $sla,
                              'accesslogin'        => $accesslogin,
                              'accesspwd'        => $accesspwd,
                              'related_sites'   => $related_sites);
        }
    }

    $result->Close();

    return $sitelist;
}

?>
