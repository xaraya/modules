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
                             'title' => xarML('Views the state of the security cache'),
                             'label' => xarML('View cache state'));

        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'syncall'),
                             'title' => xarML('Synchronize all the parts of the security cache'),
                             'label' => xarML('Synchronize all'));

        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'syncprivs'),
                             'title' => xarML('Synchronize the privileges cache'),
                             'label' => xarML('Synchronize privileges'));

        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'syncmasks'),
                             'title' => xarML('Synchronize the masks cache'),
                             'label' => xarML('Synchronize masks'));

        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'syncprivsstruct'),
                             'title' => xarML('Synchronize the privileges structure cache'),
                             'label' => xarML('Sync privs structure'));

        $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                  'admin',
                                                  'syncmasksstruct'),
                             'title' => xarML('Synchronize the masks structure cache'),
                             'label' => xarML('Sync masks structure'));

        if (!xarModAPIFunc('cachesecurity','admin','ison')) {
            $menulinks[] = Array('url'   => xarModURL('cachesecurity',
                                                       'admin',
                                                       'switchonoff', 
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
                                  'func' => 'switchonoff');
        }
    }

    return $menulinks;
}
?>
