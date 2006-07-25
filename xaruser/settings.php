<?php
/**
 * Example user settings
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
function accessmethods_user_settings()
{
    $data = xarModAPIFunc('accessmethods','user','menu');

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));

    $data['submitlabel'] = xarML('Submit');
    $data['uid'] = xarUserGetVar('uid');
    return $data;
}

?>
