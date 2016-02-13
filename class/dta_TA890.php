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
 * Wrapper class for DTA payments
 *
 */

sys::import('modules.payments.class.dta');

class DTA_TA890 {

    protected $transactionType = 890;

    public function setClientClearingNr($clearingNr) {
        $this->clientClearingNr = $this->getPadding(7);
    }

    public function record()
    {
        $record = array();
        $segment01 = '01'
                . $this->getHeader()
                . $this->getTotalAmount()
                . $this->getPadding(59);
        array_push($record, $segment01);
        return $record;
    }

    /**
     * Set the total amount of a transaction
     * 
     * @param float|int $amount
     * @throws Exception
     */
    public function setTotalAmount($amount) {
        // Check the amount
        if (!((is_float($amount)) || (is_integer($amount))))
            throw new Exception(xarML("The total amount is not a number"));
        else
            $this->totalAmount = str_pad(number_format($amount, 3, ',', ''), 16, $this->fillChar);
    }

    /**
     * Get the total amount of a transaction
     * 
     * @return string
     * @throws Exception
     */
    private function getTotalAmount() {
        if ($this->totalAmount == NULL)
            throw new Exception(xarML("The total amount is not set"));
        elseif (strlen($this->totalAmount) != 16)
            throw new Exception(xarML("The total amount does not have the proper length"));
        else
            return $this->totalAmount;
    }
}

?>