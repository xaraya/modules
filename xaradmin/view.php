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
include_once ('modules/addressbook/xarglobal.php');
/**
 * view items
 */
function accessmethods_admin_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    $data = xarModAPIFunc('accessmethods', 'admin', 'menu');

    $data['accessmethods_objectid'] = xarModGetVar('accessmethods', 'accessmethods_objectid');
//    xarModAPILoad('accessmethodss', 'user');
    $items = xarModAPIFunc('accessmethods', 'user', 'getall',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('accessmethods','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['link'] = xarModURL('accessmethods',
            'admin',
            'display',
            array('siteid' => $item['siteid']));
        if (xarSecurityCheck('EditAccessMethods', 0, 'Item', "$item[site_name]:All:$item[siteid]")) {
            $items[$i]['editurl'] = xarModURL('accessmethods',
                'admin',
                'modify',
                array('siteid' => $item['siteid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteAccessMethods', 0, 'Item', "$item[site_name]:All:$item[siteid]")) {
            $items[$i]['deleteurl'] = xarModURL('accessmethods',
                'admin',
                'delete',
                array('siteid' => $item['siteid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    
    $data['items'] = $items;
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('accessmethods', 'user', 'countitems'),
        xarModURL('accessmethods', 'admin', 'view', array('startnum' => '%%')),
        xarModGetUserVar('accessmethods', 'itemsperpage', $uid));
        
	return $data;
}

?>
