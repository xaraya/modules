<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Logfig module development team
 * @return array containing the menulinks for the main menu items.
 */
function logconfig_adminapi_getmenulinks()
{
    $menulinks = array();


    if (xarSecurityCheck('ManageLogConfig',0)) {
        $menulinks[] = array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'newloggers'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Adds a new logger to the configuration.'),
                              'label' => xarML('Add logger'),
                              'func' => 'newloggers');

        $menulinks[] = array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all loggers currently configured.'),
                              'label' => xarML('List loggers'),
                              'func' => 'view');

    }
    if (xarSecurityCheck('AdminLogConfig',0)) {
        $menulinks[] = array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration settings of this module'),
                              'label' => xarML('Modify Configuration'),
                              'func' => 'modifyconfig');
    }
        $menulinks[] = array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'overview'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'),
                              'func' => 'main');
    return $menulinks;
}

?>