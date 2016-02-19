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
 * Class for DTA 830 payments
 *
 */

sys::import('modules.payments.class.dta');

class DTA_TA830 extends DTA{

    protected $transactionType = 830;
    
    protected function getSegment02()
    {
        $segment02 = '02'
                . $this->getConversionRate()
                . $this->getClient()
                . $this->getPadding(18)
                ;
        return $segment02;
    }
    
}
?>