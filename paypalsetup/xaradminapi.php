<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * PayPal Set-up
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage paypalsetup module
 * @author John Cox <niceguyeddie@xaraya.com> 
 */


/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function paypalsetup_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminPayPalSetUp', 0)) {
        $menulinks[] = Array('url' => xarModURL('paypalsetup',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration.'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}

?>
