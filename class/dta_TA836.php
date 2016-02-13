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
 * Class for DTA 836 payments
 *
 */

sys::import('modules.payments.class.dta');

class DTA_TA836 extends DTA{

    protected $transactionType = 836;
    
    public function setPaymentType($paymentType=0) {
        $this->paymentType = $paymentType;
    }

}
?>