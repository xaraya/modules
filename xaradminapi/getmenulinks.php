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
 * Return the options for the admin menu
 *
 */

    function payments_adminapi_getmenulinks()
    {
        return xarMod::apiFunc('base', 'admin', 'menuarray', array('module' => 'payments'));
    }
