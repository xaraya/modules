<?php

/**
 * display form for a new poll
 */
function polls_admin_new()
{

    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    $data = array();

    // Title
    $data['buttonlabel'] = xarML('Create Poll');

    // Start form
    $data['authid'] = xarSecGenAuthKey();
    $data['optcount'] = xarModGetVar('polls', 'defaultopts');

    return $data;
}

?>