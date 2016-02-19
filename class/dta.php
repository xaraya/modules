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
 * Based on Six DTA Standards: http://www.six-interbank-clearing.com/en/home/standardization/dta.html
 *
 */

sys::import('xaraya.structures.datetime');

class DTA {

    const CRLF                      = "\r\n";       
    
    protected $fillChar             = ' ';
    
    // Header fields
    protected $processingDay        = '000000';
    protected $recipientClearingNr;
    protected $creationDate;
    protected $clientClearingNr;
    protected $dataFileSender;
    private $inputSequenceNr;
    protected $transactionType;
    protected $paymentType          = '0';
    protected $processingFlag       = '0';
    
    // Record fields
    protected $debitAccount;
    protected $paymentAmount;
    protected $client;
    protected $recipient;
    protected $paymentReason;
    protected $conversionRate;
    
    // Record fields for 890
    private $paymentAmountNumeric;
    protected $totalAmount;
    
    
    protected function concatenateRecord($record)
    {
        $string = '';
        while ($segment = array_pop($record)) {
            $string = $segment . self::CRLF . $string;
        }
        return $string;
    }

    public function getHeader() 
    {
        $header = $this->getProcessingDay()
                . $this->getRecipientClearingNr()
                . $this->getOutputSequenceNr()
                . $this->getCreationDate()
                . $this->getClientClearingNr()
                . $this->getDataFileSender()
                . $this->getInputSequenceNr()
                . $this->getTransactionType()
                . $this->getPaymentType()
                . $this->getProcessingFlag()
                ;
        return $header;
    }

    public function setProcessingDay($timestamp=0) 
    {
        $this->processingDay = $this->transformDate(0);
    }

    public function setRecipientClearingNr($clearingNr=0) 
    {
        $this->recipientClearingNr = $this->getPadding(12);
    }

    public function setCreationDate($timestamp=0) 
    {
        $this->creationDate =  $this->transformDate($timestamp);
    }

    public function setClientClearingNr($clearingNr) 
    {
        if (!is_integer($clearingNr))
            throw new Exception(xarML("Invalid client bank clearing number: #(1)", $clearingNr));
        else
            $this->clientClearingNr = str_pad($clearingNr, 7, $this->fillChar);
    }

    public function setDataFileSender($senderID) 
    {
        if (!(strlen($senderID) == 5))
            throw new Exception(xarML("Invalid DTA ID: #(1)"), $senderID);
        else
            $this->dataFileSender = $senderID;
    }

    public function setInputSequenceNr($sequenceNr) 
    {
        if (!is_integer($sequenceNr))
            throw new Exception(xarML("Invalid input sequence number: #(1)"), $sequenceNr);
        else
            $this->inputSequenceNr = str_pad($sequenceNr, 5, '0', STR_PAD_LEFT);
            
    }

    private function setPaymentType() {}

    public function setPaymentAmount($amount, $currencyCode, $valuta = NULL) 
    {
        $paymentAmount = '';

        // Check the value date
        if ($valuta == NULL)
            $valuta = '      ';
        else {
            $valuta = $this->transformDate($valuta);
            if (!is_numeric($valuta) || (strlen($valuta) != 6 ))
                throw new Exception(xarML("The value date must have the format DDMMYY: #(1)", $valuta));
        }

        // Check the amount
        if (!((is_float($amount)) || (is_integer($amount))))
            throw new Exception(xarML("The amount is not numeric: #(1)"), $amount);
        else {
            $this->paymentAmountNumeric = $amount;
            $amount = str_pad(number_format($amount, 2, ',', ''), 12, $this->fillChar);
        }

        // Check the currency code
        if (!strlen($currencyCode) == 3 )
            throw new Exception(xarML("Invalid currency code"));

        $paymentAmount = $valuta . $currencyCode . $amount;
        if (strlen($paymentAmount) != (6 + 3 + 12 ))
            throw new Exception(xarML("Invalid amount: #(1)", $paymentAmount));
        else
            $this->paymentAmount = $paymentAmount;
    }

    public function setDebitAccount($debitAccount) 
    {
        if (strlen($debitAccount) > 24)
            throw new Exception(xarML("Invalid debit account: #(1)"), $debitAccount);
        else {
            $this->debitAccount = str_pad($debitAccount, 24, $this->fillChar);
        }
    }

    public function setClient($line1, $line2, $line3, $line4) 
    {
        $client = array();
        array_push($client, str_pad(strtoupper($this->replaceChars($line4)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line3)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line2)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line1)), 24, $this->fillChar));
        $this->client = $client;
    }

    public function setRecipient($account, $line1, $line2, $line3, $line4) 
    {
        $recipient = array();
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line4, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line3, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line2, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line1, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper('/C/' . $account), 30, $this->fillChar));
        $this->recipient = $recipient;
    }

    public function setPaymentReason($lines=array()) 
    {
        $reason = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 28)
            throw new Exception(xarML("Exceeds 28 characters: #(1)"), $line);
            array_push($reason, str_pad(strtoupper($this->replaceChars($line)), 28, $this->fillChar));
        }
        $this->paymentReason = $reason;
    }

    public function setConversionRate($rate) 
    {
        // Check the amount
        if (!((is_float($rate)) || (is_integer($rate))))
            throw new Exception(xarML("The rate is not numeric: #(1)"), $rate);
        else {
            $this->conversionRate = str_pad(number_format($rate, 6, ',', ''), 12, $this->fillChar);
        }
    }




    private function getProcessingDay() 
    {
        return $this->processingDay;
    }

    private function getRecipientClearingNr() 
    {
        return $this->recipientClearingNr;
    }

    private function getOutputSequenceNr() 
    {
        return '00000';
    }

    private function getCreationDate() 
    {
        return $this->creationDate;
    }

    private function getClientClearingNr() 
    {
        return $this->clientClearingNr;
    }

    private function getDataFileSender() 
    {
        return $this->dataFileSender;
    }

    private function getInputSequenceNr() 
    {
        return $this->inputSequenceNr;
    }

    private function getTransactionType() 
    {
        return $this->transactionType;
    }

    private function getPaymentType() 
    {
        return $this->paymentType;
    }

    private function getProcessingFlag() 
    {
        return $this->processingFlag;
    }

    protected function getReferenceNr() 
    {
        return $this->getDataFileSender() . $this->getTransactionID();
    }

    private function getTransactionID() 
    {
        return mt_rand(100000, 999999) . $this->getInputSequenceNr();
    }

    protected function getDebitAccount() 
    {
        return $this->debitAccount;
    }

    protected function getPaymentAmount() 
    {
        return $this->paymentAmount;
    }

    protected function getClient() 
    {
        $clients = $this->client;
        $client = '';
        if (!empty($clients)) {
            while ($line = array_pop($clients)) {
                $client .= $line;
            }
        }
        return $client;
    }

    protected function getRecipient() 
    {
        $recipients = $this->recipient;
        $recipient = '';
        if (!empty($recipients)) {
            while ($line = array_pop($recipients)) {
                $recipient .= $line;
            }
        }
        return $recipient;
    }

    private function getPaymentReason() 
    {
        $reasons = $this->paymentReason;
        $reason = '';
        if (!empty($reasons)) {
            while ($line = array_pop($reasons)) {
                $reason .= $line;
            }
        }
        return $reason;
    }

    protected function getConversionRate()
    {
        return $this->conversionRate;
    }


    // Record segments
    protected function getSegment01()
    {
        $segment01 = '01'
                . $this->getHeader()
                . $this->getReferenceNr()
                . $this->getDebitAccount()
                . $this->getPaymentAmount()
                ;
        $segment01 = str_pad($segment01, 128, $this->fillChar);
        return $segment01;
    }

    protected function getSegment02()
    {
        $segment02 = '02'
                . $this->getConversionRate()
                . $this->getClient()
                ;
        $segment02 = str_pad($segment02, 128, $this->fillChar);
        return $segment02;
    }

    protected function getSegment03()
    {
        $segment03 = '03'
                . $this->getRecipient()
                ;
        $segment03 = str_pad($segment03, 128, $this->fillChar);
        return $segment03;
    }

    protected function getSegment04()
    {
        $segment04 = '04'
                . $this->getPaymentReason()
                . $this->getBankAddressID()
                ;
        $segment04 = str_pad($segment04, 128, $this->fillChar);
        return $segment04;
    }

    protected function getSegment05()
    {
        $segment05 = '05'
                . $this->getEndRecipient()
                ;
        $segment05 = str_pad($segment05, 128, $this->fillChar);
        return $segment05;
    }

    protected function transformDate($timestamp=0) 
    {
        $timestamp = (int)$timestamp;
        if (empty($timestamp)) return '000000';

        $date = new XarDateTime();
        $date->setTimestamp($timestamp);
        $day = $date->getDay();
        $day = str_pad($day, 2, "0", STR_PAD_LEFT);
        $month = $date->getMonth();
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $year = $date->getYear();
        $year = substr($year, -2, 2);
        return $day . $month . $year;
    }



    public function getPaymentAmountNumeric() 
    {
        return $this->paymentAmountNumeric;
    }

    private function getEndRecipient() 
    {
        return $this->getPadding(30)
                . $this->getPadding(24)
                . $this->getPadding(24)
                . $this->getPadding(24)
                . $this->getPadding(24);
    }

    protected function getPadding($length) 
    {
        $padding = '';
        for ($i = 1; $i <= $length; $i++) {
            $padding .= $this->fillChar;
        }
        return $padding;
    }

    protected function replaceChars($string) 
    {
         $replace_chars = array(
         'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
         'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
         'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
         'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae',
         'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
         'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'oe', 'ø'=>'o', 'ù'=>'u',
         'ü'=>'ue','ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
         );
        return strtr($string, $replace_chars);
    }
}

?>