<?php
/**
 * Create an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * create an item
 */
function labAccounting_admin_create($args)
{
    // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
    if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'labAccounting'));
    if (!isset($object)) return;  // throw back

    // check the input values for this object
    $isvalid = $object->checkInput();

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!empty($preview) || !$isvalid) {
        $data = xarModAPIFunc('labAccounting','admin','menu');

        $data['object'] = & $object;

        $data['preview'] = $preview;
        $item = array();
        $item['module'] = 'labAccounting';
        $hooks = xarModCallHooks('item','new','',$item);
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = join('',$hooks);
        } else {
            $data['hooks'] = $hooks;
        }
        return xarTplModule('labAccounting','admin','new', $data);
    }

    // create the item here
    $itemid = $object->createItem();
    if (empty($itemid)) return; // throw back

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('labAccounting', 'admin', 'view'));

    // Return
    return true;
}

?>