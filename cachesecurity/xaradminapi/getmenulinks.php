<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Flavio Botelho <nuncanada@xaraya.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function cachesecurity_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminCacheSecurity')) {
        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'view'),
                             'title' => xarML('Central control for the security caching system.'),
                             'label' => xarML('Security cache control'));

        if (!xarModAPIFunc('cachesecurity','admin','ison')) {
            $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                       'admin',
                                                       'turnon', 
                                                       array('authid' => xarSecGenAuthKey())),
                                  // In order to display the tool tips and label in any language,
                                  // we must encapsulate the calls in the xarML in the API.
                                  'title' => xarML('Turn the security cache system on.'),
                                  'label' => xarML('Turn on'),
                                  'func' => 'switchonoff');
        } else {
            $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                       'admin',
                                                       'switchonoff', 
                                                       array('authid' => xarSecGenAuthKey())),
                                  // In order to display the tool tips and label in any language,
                                  // we must encapsulate the calls in the xarML in the API.
                                  'title' => xarML('Turn the security cache system off.'),
                                  'label' => xarML('Turn off'),
                                  'func' => 'turnoff');
        }
    }

    return $menulinks;
}
?>
