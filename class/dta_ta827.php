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

class DTA_TA827 extends DTA
{
    protected $transactionType = 827;

    public function setProcessingDay($timestamp=0)
    {
        $this->processingDay = $this->transformDate($timestamp);
    }

    /**
     * Set the bank clearing number of the recipients bank
     *
     * @param int $clearingNr
     * @throws Exception
     */
    public function setRecipientClearingNr($clearingNr=0)
    {
        if (!is_integer($clearingNr)) {
            throw new Exception(xarML("Invalid recipient bank clearing number: #(1)", $clearingNr));
        } else {
            $this->recipientClearingNr = str_pad($clearingNr, 12, $this->fillChar);
        }
    }

    public function setPaymentType($paymentType=0)
    {
        $this->paymentType = $paymentType;
    }

    protected function getConversionRate()
    {
        return '';
    }

    protected function getBankAddressID()
    {
        return '';
    }
    
    public function toString()
    {
        $record = array();
        // Segment 01
        array_push($record, $this->getSegment01());

        // Segment 02
        array_push($record, $this->getSegment02());

        // segment 03
        array_push($record, $this->getSegment03());

        // segment 04
        array_push($record, $this->getSegment04());
        /*
                // segment 05
                array_push($record, $this->getSegment05());
        */
        $string = $this->concatenateRecord($record);
        return $string;
    }
}
