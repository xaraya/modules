<?php

function xtasks_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    
    xarModLoad('xtasks','user');
    $data = xarModAPIFunc('xtasks','user','menu');

    if (!xarSecurityCheck('AddXTask')) {
        return;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    $item = array();
    $item['module'] = 'xtasks';
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
