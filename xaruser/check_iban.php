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
 * Check an IBAN number
 *
 */

function payments_user_check_iban()
{
    // Security Check
    if (!xarSecurity::check('ReadPayments')) return;
    if (!xarVar::fetch('iban',   'str',  $data['iban'], '', xarVar::NOT_REQUIRED)) return;
    if(!xarVar::fetch('confirm', 'bool', $data['confirm'],      false,  xarVar::NOT_REQUIRED)) {return;}
    
    sys::import('modules.payments.class.iban');
    $data['ibanobject'] = new IBAN($data['iban']);
    
    if ($data['confirm']) {
        if (empty($data['iban'])) return $data;
        
    }
    
    return $data;
    
}
?>
