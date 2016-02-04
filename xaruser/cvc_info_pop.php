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
//Psspl:Added the code for csv number information.
    function payments_user_cvc_info_pop($args)
    {       
        if (!xarSecurityCheck('ReadPayments')) return;
        echo xarTplModule('payments','user','cvc_info_pop');
        exit();
        return array();
    }
?>