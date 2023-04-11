<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Return the options for the user menu
 *
 */

function payments_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurity::check('ViewPayments',0)) {
        $menulinks[] = array('url'   => xarController::URL('payments',
                                                  'user',
                                                  'main'),
                              'title' => xarML('Show an overview of this module'),
                              'label' => xarML('Overview'));
        $menulinks[] = array('url'   => xarController::URL('payments',
                                                  'user',
                                                  'main'),
                              'title' => xarML('Execute a payment'),
                              'label' => xarML('Run Order'));                                 
        $menulinks[] = array('url'   => xarController::URL('payments',
                                                  'user',
                                                  'view'),
                              'title' => xarML('View Payments stored in the database'),
                              'label' => xarML('View Payments'));                                 
    }

    return $menulinks;
}

?>
