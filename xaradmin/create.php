<?php
/**
 * Create an item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * create an item
 *
 * @param str preview When this parameter is set, a preview of the new item is shown.
 */
function dyn_example_admin_create($args)
{
    // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
    if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'dyn_example'));
    if (!isset($object)) return;  // throw back

    // check the input values for this object
    $isvalid = $object->checkInput();

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!empty($preview) || !$isvalid) {
        $data = xarModAPIFunc('dyn_example','admin','menu');

        $data['object'] = & $object;

        $data['preview'] = $preview;
        $item = array();
        $item['module'] = 'dyn_example';
        $hooks = xarModCallHooks('item','new','',$item);
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = join('',$hooks);
        } else {
            $data['hooks'] = $hooks;
        }
        return xarTplModule('dyn_example','admin','new', $data);
    }

    // create the item here
    // For this function, we use the dynamic data function
    $itemid = $object->createItem();
    if (empty($itemid)) return; // throw back

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('dyn_example', 'admin', 'view'));

    // Return
    return true;
}

?>