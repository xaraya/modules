<?php

function xarpages_user_main()
{
    return xarModFunc('xarpages', 'user', 'display');

    xarVarFetch('pid', 'id', $pid, 0, XARVAR_NOT_REQUIRED);

    if (!empty($pid)) {
        return xarModFunc('xarpages', 'user', 'display', array('pid' => $pid));
    }
}

?>