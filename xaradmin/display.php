<?php
/**
 * AccessMethods Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AccessMethods Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego
 */
function accessmethods_admin_display($args)
{
    extract($args);
    if (!xarVarFetch('siteid', 'id', $siteid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    $data['accessmethods_objectid'] = xarModGetVar('accessmethods', 'accessmethods_objectid');

    if (!xarModAPILoad('accessmethods', 'user')) return;

    if (!empty($objectid)) {
        $siteid = $objectid;
    }

    $data = xarModAPIFunc('accessmethods','admin','menu');
    $data['siteid'] = $siteid;
    $data['status'] = '';

    $item = xarModAPIFunc('accessmethods',
                          'user',
                          'get',
                          array('siteid' => $siteid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'accessmethods');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return $msg;
    }
    
    list($item['site_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['siteid'],
                                         array($item['site_name']));
    
    $data['item'] = $item;
    $data['authid'] = xarSecGenAuthKey();
    $data['site_name'] = $item['site_name'];
    $data['description'] = $item['description'];

    $hooks = xarModCallHooks('item',
                             'display',
                             $siteid,
                             xarModURL('accessmethods',
                                       'admin',
                                       'display',
                                       array('siteid' => $siteid)));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    return $data;
}
?>