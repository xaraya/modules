<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * add new item
 */
function accessmethods_admin_new()
{    
    xarModLoad('addressbook', 'admin');
    $data = xarModAPIFunc('accessmethods','admin','menu');

    $data['accessmethods_objectid'] = xarModGetVar('xproject', 'accessmethods_objectid');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['webmasterid'] = xarSessionGetVar('uid');

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Access Method'));

    $item = array();
    $item['module'] = 'accessmethods';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}

?>
