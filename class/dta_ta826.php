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
    
    public function setClient($line1, $line2, $line3, $line4) 
    {
        $client = array();
        array_push($client, str_pad(strtoupper($this->replaceChars($line4)), 20, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line3)), 20, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line2)), 20, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line1)), 20, $this->fillChar));
        $this->client = $client;
    }

    protected function getConversionRate() 
    {
        return '';
    }

    protected function getSegment03()
    {
        $segment02 = '03'
                . $this->getRecipient()
                . $this->getReason()
                ;
        $segment03 = str_pad($segment03, 128, $this->fillChar);
        return $segment03;
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

        return $record;
    }
}
?>