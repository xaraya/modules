<?php

/**
 * the main administration function
 */
function recommend_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditRecommend')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('recommend', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>