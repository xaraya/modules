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

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[site_name]:All:$siteid")) {
        return;
    }
    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$site_name:All:$siteid")) {
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
                  related_sites = ?
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
              $siteid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $item['module'] = 'accessmethods';
    $item['itemid'] = $siteid;
    $item['name'] = $site_name;
    xarModCallHooks('item', 'update', $siteid, $item);

    return true;
}
?>
