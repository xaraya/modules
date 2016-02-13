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
 * Class for DTA 826 payments
 *
 */

sys::import('modules.payments.class.dta');

class DTA_TA826 extends DTA{

    protected $transactionType = 826;

    public function setProcessingDay($timestamp=0) {
        $this->processingDay = $this->transformDate($timestamp);
    }
    
}
?>