<?php

/**
 * create an item
 */
function dyn_example_admin_create($args)
{
    // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
    $preview = xarVarCleanFromInput('preview');

    extract($args);

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
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
    $itemid = $object->createItem();
    if (empty($itemid)) return; // throw back

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('dyn_example', 'admin', 'view'));

    // Return
    return true;
}

?>
