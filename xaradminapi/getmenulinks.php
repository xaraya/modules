<?php
/**
 * Utility function to pass admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the labAccounting module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function labAccounting_adminapi_getmenulinks()
{
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');

    $menulinks = array();
    
    $menulinks[] = Array('url'   => xarModURL('labaccounting',
                                               'admin',
                                               'overview'),
                          'title' => xarML('The overview of labAccounting and its functions'),
                          'label' => xarML('Overview'));

    if (xarSecurityCheck('ViewXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('labaccounting',
                                                   'journals',
                                                   'summary'),
                              'title' => xarML('Journals dashboard for account review'),
                              'label' => xarML('Account Summary'));
    }

    if (xarSecurityCheck('AddXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('labaccounting',
                                                   'admin',
                                                   'general'),
                              'title' => xarML('A list of all Ledger accounts'),
                              'label' => xarML('General Ledger'));
        $menulinks[] = Array('url'   => xarModURL('labaccounting',
                                                   'ledgers',
                                                   'new'),
                              'title' => xarML('Create a new Ledger'),
                              'label' => xarML('New Ledger'));
        $menulinks[] = Array('url'   => xarModURL('labaccounting',
                                                   'journals',
                                                   'new'),
                              'title' => xarML('Create a new Journal'),
                              'label' => xarML('New Journal'));
    }

    if (xarSecurityCheck('AdminXTask', 0)) {
        $menulinks[] = Array('url'    => xarModURL('labaccounting',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>