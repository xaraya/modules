<?php
/**
 * Return the options for the user menu
 *
 */

function mailer_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewMailer',0)) {
        $menulinks[] = array('url'   => xarModURL('mailer',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
        $menulinks[] = array('url'   => xarModURL('mailer',
                                                  'user',
                                                  'test'),
                              'title' => xarML('Send a test message'),
                              'label' => xarML('Test Message'));
    }

    return $menulinks;
}

?>
