<?php

function xarpages_user_main()
{
    return xarMod::guiFunc('xarpages', 'user', 'display');

    xarVarFetch('pid', 'id', $pid, 0, XARVAR_NOT_REQUIRED);

    if (!empty($pid)) {
        return xarMod::guiFunc('xarpages', 'user', 'display', array('pid' => $pid));
    }
}

?>