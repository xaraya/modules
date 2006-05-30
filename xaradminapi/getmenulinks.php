<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
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
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function logconfig_adminapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('AdminLogConfig',0)) {

        $menulinks[] = Array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'newloggers'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Adds a new logger to the configuration.'),
                              'label' => xarML('Add logger'),
                              'func' => 'newloggers');

        $menulinks[] = Array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all loggers currently configured.'),
                              'label' => xarML('List loggers'),
                              'func' => 'view');

        if (!xarModAPIFunc('logconfig','admin','islogon')) {
            $menulinks[] = Array('url'   => xarModURL('logconfig',
                                                       'admin',
                                                       'switchonoff',
                                                       array('authid' => xarSecGenAuthKey())),
                                  // In order to display the tool tips and label in any language,
                                  // we must encapsulate the calls in the xarML in the API.
                                  'title' => xarML('Make logging work as configured.'),
                                  'label' => xarML('Turn logging on'),
                                  'func' => 'switchonoff');
        } else {
            $menulinks[] = Array('url'   => xarModURL('logconfig',
                                                       'admin',
                                                       'switchonoff',
                                                       array('authid' => xarSecGenAuthKey())),
                                  // In order to display the tool tips and label in any language,
                                  // we must encapsulate the calls in the xarML in the API.
                                  'title' => xarML('Make logging stop working.'),
                                  'label' => xarML('Turn logging off'),
                                  'func' => 'switchonoff');
        }
        $menulinks[] = Array('url'   => xarModURL('logconfig',
                                                   'admin',
                                                   'overview'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'),
                              'func' => 'main');


    }

    return $menulinks;
}

?>