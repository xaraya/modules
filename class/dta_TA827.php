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

class DTA_TA827 extends DTA{

    protected $transactionType = 827;

    public function setProcessingDay($timestamp=0) {
        $this->processingDay = $this->transformDate($timestamp);
    }

    /**
     * Set the bank clearing number of the recipients bank
     * 
     * @param int $clearingNr
     * @throws Exception
     */
    public function setRecipientClearingNr($clearingNr) {
        if (!is_integer($clearingNr))
            throw new Exception(xarML("Invalid recipient bank clearing number: #(1)", $clearingNr));
        else
            $this->recipientClearingNr = str_pad($clearingNr, 12, $this->fillChar);
    }

    public function setPaymentType($paymentType=0) {
        $this->paymentType = $paymentType;
    }

    public function getRecord()
    {
        $record = array();
        // Segment 01
        $segment01 = '01'
                . $this->getHeader()
                . $this->getReferenceNr()
                . $this->getDebitAccount()
            /*
                . $this->getPaymentAmount()
                . $this->getPadding(14)
                */
                ;var_dump($segment01);exit;
        array_push($record, $segment01);

        // Segment 02
        $segment02 = '02'
                . $this->getClient()
                . $this->getPadding(30);
        array_push($record, $segment02);

        // segment 03
        $segment03 = '03'
                . $this->getRecipient();
        array_push($record, $segment03);

        // segment 04
        $segment04 = '04'
                . $this->getPaymentReason()
                . $this->getPadding(14);
        array_push($record, $segment04);
/*
        // segment 05
        $segment05 = '05'
                . $this->getEndRecipient();
        array_push($record, $segment05);
*/
        return $record;
    }
    
}
?>