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

function accessmethods_userapi_get($args)
{
    extract($args);

    if (!isset($siteid) || !is_numeric($siteid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Access Method ID', 'user', 'get', 'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $query = "SELECT siteid,
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
            FROM $accessmethodstable
            WHERE siteid = ?";
    $result = &$dbconn->Execute($query,array($siteid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

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

    $result->Close();

    if (!xarSecurityCheck('ReadAccessMethods', 1, 'Item', "$site_name:All:$siteid")) {
        $msg = xarML('Not authorized to view this site.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('siteid'          => $siteid,
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

    return $item;
}

?>
