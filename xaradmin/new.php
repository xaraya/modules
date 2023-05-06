<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * create new keywords assignment
 * @param string confirm
 * @return array with data
 */
function keywords_admin_new($args)
{
    extract($args);

    if (!xarVar::fetch('confirm',  'isset', $confirm,  NULL, xarVar::NOT_REQUIRED)) {return;}
    if (!xarSecurity::check('AdminKeywords')) return;

    $data = array();
    $data['object'] = xarMod::apiFunc('dynamicdata','user','getobject',
                                     array('module' => 'keywords'));
    if (!isset($data['object'])) return;
    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) return;
        // check the input values for this object
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            // create the item here
            $itemid = $data['object']->createItem();
            if (empty($itemid)) return; // throw back

            // let's go back to the admin view
            xarController::redirect(xarController::URL('keywords', 'admin', 'view'));
            return true;
        }
    }
    $item = array();
    $item['module'] = 'keywords';
    $hooks = xarModHooks::call('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSec::genAuthKey();
    $data['confirm'] = xarML('Create');

    return $data;
}
?>
