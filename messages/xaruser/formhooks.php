<?php
function messages_user_formhooks()
{

    $hooks = array();
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'messages');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'messages');

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}
?>