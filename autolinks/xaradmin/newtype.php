<?php

/**
 * add new item
 */
function autolinks_admin_newtype($args)
{
    extract($args);

    // Security Check
    if (!xarSecurityCheck('AddAutolinks')) {return;}

    $data['authid'] = xarSecGenAuthKey();

    // Prepare each parameter for re-presentation in the form.
    foreach(array('type_name', 'type_name_error',
        'template_name', 'template_name_error',
        'type_desc', 'type_desc_error',
        'dynamic_replace', 'global_error') as $param) {
        if (isset($$param)) {
            $data[$param] = xarVarPrepForDisplay($$param);
        } else {
            $data[$param] = '';
        }
    }

    // TODO: provide a list of template names in a drop-down, based on
    // the correct file mask.

    $hooks = xarModCallHooks(
        'item', 'new', '',
        array('itemtype' => xarModGetVar('autolinks', 'typeitemtype'), 'module' => 'autolinks')
    );
    $data['hooks'] = $hooks;

    // Return the output
    return $data;
}

?>