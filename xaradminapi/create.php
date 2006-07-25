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
function accessmethods_adminapi_create($args)
{
    extract($args);
    
    if(is_array($related_sites)) $related_sites = serialize($related_sites);

    $invalid = array();
    if (!isset($site_name) || !is_string($site_name)) {
        $invalid[] = 'site_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddAccessMethods', 1, 'Item', "$site_name:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $nextId = $dbconn->GenId($accessmethodstable);

    $query = "INSERT INTO $accessmethodstable (
                  siteid,
                  accesstype,
                  clientid,
                  webmasterid,
                  site_name,
                  url,
                  description,
                  sla,
                  accesslogin,
                  accesspwd,
                  related_sites)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $accesstype,
              $clientid,
              $webmasterid,
              $site_name,
              $url,
              $description,
              $sla,
              $accesslogin,
              $accesspwd,
              $related_sites ? $related_sites : "");
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE


    $siteid = $dbconn->PO_Insert_ID($accessmethodstable, 'siteid');

    $item = $args;
    $item['module'] = 'accessmethods';
    $item['itemid'] = $siteid;
    xarModCallHooks('item', 'create', $siteid, $item);

    return $siteid;
}

?>
