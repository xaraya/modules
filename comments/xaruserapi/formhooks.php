<?php

/**
 * Sets up any formaction / formdisplay hooks
 *
 */
function comments_userapi_formhooks()
{

    $hooks = array();
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'comments');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'comments');

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