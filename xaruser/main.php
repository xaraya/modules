<?php

function xarpages_user_main()
{
    return xarMod::guiFunc('xarpages', 'user', 'display');

    xarVar::fetch('pid', 'id', $pid, 0, xarVar::NOT_REQUIRED);

    if (!empty($pid)) {
        return xarMod::guiFunc('xarpages', 'user', 'display', array('pid' => $pid));
    }
}
