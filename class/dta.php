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

sys::import('xaraya.structures.datetime');

class DTA {

    const FS1 = 0x01;           // SOH
    const FS2 = 0x0D254E;       // CRLF+
    const FS3 = 0x0D257A;       // CRLF:
    const FS4 = 0x0D2560;       // CRLF-
    const FS5 = 0x03;           // ETX
    const TAG = 0x7A;           // :
    const CS2 = 0x0D25;         // CRLF
    
    private $fillChar = ' ';
    private $processingDay;
    private $recipientClearingNr;
    private $creationDate;
    private $clientClearingNr;
    private $inputSequenceNr;
    private $transactionType;
    private $DtaID;
    private $debitAccount;
    private $paymentAmount;
    private $paymentAmountNumeric;
    private $client;
    
    protected $totalAmount;
    
    
    public function record()
    {
        $string  = self::FS1;
        $string .= $this->header();
        $string .= self::FS2;
        $string .= '20';
        $string .= self::TAG;

        $string .= self::FS3;
        $string .= '25';
        $string .= self::TAG;
        return $string;
    }
    
    protected function getHeader() {
        $header = $this->getProcessingDay()
                . $this->getRecipientClearingNr()
                . $this->getOutputSequenceNr()
                . $this->getCreationDate()
                . $this->getClientClearingNr()
                . $this->getDtaId()
                . $this->getInputSequenceNr()
                . $this->getTransactionType()
                . $this->getPaymentType()
                . $this->getProcessingFlag();
        return $header;
    }

    public function setProcessingDay($processingDay) {
        if ((!is_numeric($processingDay)) && (!(strlen($processingDay) == 6)))
            throw new Exception(xarML("The processign day must have the format DDMMYY"));
        else
            $this->processingDay = $processingDay;
    }

    private function getProcessingDay() {
        if ($this->processingDay == NULL)
            throw new Exception(xarML("The processing day is not set"));
        else
            return $this->processingDay;
    }

    /**
     * Set the bank clearing number of the recipients bank
     * 
     * @param int $clearingNr
     * @throws Exception
     */
    public function setRecipientClearingNr($clearingNr) {
        if (!is_integer($clearingNr))
            throw new Exception(xarML("The clearing number is incorrect"));
        else
            $this->recipientClearingNr = $clearingNr;
    }

    private function getRecipientClearingNr() {
        if ($this->recipientClearingNr != NULL)
            return str_pad($this->recipientClearingNr, 12, $this->fillChar);
        else
            return $this->getPadding(12);
    }

    private function getOutputSequenceNr() {
        return '00000';
    }

    public function setCreationDate($timestamp=0) {
        $this->creationDate =  $this->transformDate($timestamp);
    }

    private function getCreationDate() {
        if ($this->creationDate == NULL)
            throw new Exception("The creation date is not set");
        else
            return $this->creationDate;
    }

    private function transformDate($timestamp=0) {
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

    public function setClientClearingNr($clearingNr) {
        if (!is_integer($clearingNr))
            throw new Exception(xarML("Invalid client bank clearing number: #(1)", $clearingNr));
        else
            $this->clientClearingNr = $clearingNr;
    }

    private function getClientClearingNr() {
        if ($this->clientClearingNr == NULL)
            throw new Exception(xarML("The client bank clearing number is not set"));
        else
            return str_pad($this->clientClearingNr, 7, $this->fillChar);
    }

    public function setInputSequenceNr($sequenceNr) {
        if (!is_integer($sequenceNr))
            throw new Exception(xarML("Invalid input sequence number: #(1)"), $sequenceNr);
        else
            $this->inputSequenceNr = $sequenceNr;
    }

    private function getInputSequenceNr() {
        if ($this->inputSequenceNr == NULL)
            throw new Exception(xarML("The input sequence number is not set"));
        else
            return str_pad($this->inputSequenceNr, 5, '0', STR_PAD_LEFT);
    }

    private function getTransactionType() {
        return $this->transactionType;
    }

    private function getPaymentType() {
        return '0';
    }

    private function getProcessingFlag() {
        return '0';
    }

    public function setDtaId($DtaID) {
        if (!(strlen($DtaID) == 5))
            throw new Exception(xarML("Invalid DTA ID: #(1)"), $DtaID);
        else
            $this->DtaID = $dtaId;
    }

    private function getDtaID() {
        if ($this->DtaID == NULL)
            throw new Exception(xarML("The DTA ID is not set"));
        else
            return $this->DtaID;
    }

    private function getTransactionID() {
        return mt_rand(100000, 999999) . $this->getInputSequenceNr();
    }

    private function getReferenceNr() {
        return $this->getDtaID() . $this->getTransactionID();
    }

    public function setDebitAccount($debitAccount) {
        if (strlen($debitAccount) > 24)
            throw new Exeption(xarML("Invalid debit account: #(1)", $debitAccount));
        else
            $this->debitAccount = str_pad($debitAccount, 24, $this->fillChar);
    }

    private function getDebitAccount() {
        if ($this->debitAccount == NULL)
            throw new Exception(xarML("The debit account is not set"));
        else {
            if (strlen($this->debitAccount) != 24)
                throw new Exception(xarML("Invalid debit account: #(1)", $this->debitAccount));
            else
                return $this->debitAccount;
        }
    }

    public function setPaymentAmount($amount, $currencyCode, $valuta = NULL) {
        $paymentAmount = '';

        // Überprüfen des Valuta
        if ($valuta == NULL)
            $valuta = '      ';
        else {
            $valuta = $this->transformDate($valuta);
            if (!is_numeric($valuta) || (strlen($valuta) != 6 ))
                throw new Exception(xarML("The value date must have the format DDMMYY"));
        }

        // Überprüfen des Betrages
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
            throw new Exception(xarML("Invalid amount"));
        else
            $this->paymentAmount = $paymentAmount;
    }

    public function getPaymentAmountNumeric() {
        if ($this->paymentAmountNumeric == NULL)
            throw new Exception(xarML("The payment amount is not set"));
        else
            return $this->paymentAmountNumeric;
    }

    private function getPaymentAmount() {
        if ($this->paymentAmount == NULL)
            throw new Exception(xarML("The payment amount is not set"));
        else {
            if (strlen($this->paymentAmount) != (6 + 3 + 12))
                throw new Exception(xarML("The payment amount does not have the correct length: #(1)"), $this->paymentAmount);
            else
                return $this->paymentAmount;
        }
    }

    public function setClient($line1, $line2, $line3, $line4) {
        $client = array();
        array_push($client, str_pad(strtoupper($this->replaceChars($line4)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line3)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line2)), 24, $this->fillChar));
        array_push($client, str_pad(strtoupper($this->replaceChars($line1)), 24, $this->fillChar));
        $this->client = $client;
    }

    private function getClient() {
        if ($this->client == NULL)
            throw new Exception(xarML("The client is not set"));
        else {
            $clients = $this->client;
            $client = '';
            while ($line = array_pop($clients)) {
                $client .= $line;
            }
            return $client;
        }
    }

    public function setRecipient($account, $line1, $line2, $line3, $line4) {
        $recipient = array();
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line4, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line3, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line2, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper($this->replaceChars(substr($line1, 0, 24))), 24, $this->fillChar));
        array_push($recipient, str_pad(strtoupper('/C/' . $account), 30, $this->fillChar));
        $this->recipient = $recipient;
    }

    private function getRecipient() {
        if ($this->recipient == NULL)
            throw new Exception(xarML("The recipient is not set"));
        else {
            $recipients = $this->recipient;
            $recipient = '';
            while ($line = array_pop($recipients)) {
                $recipient .= $line;
            }
            return $recipient;
        }
    }

    public function setPaymentReason($lines=array()) {
        $reason = array();
        foreach ($lines as $line) {
            array_push($reason, str_pad(strtoupper($this->replaceChars($line)), 28, $this->fillChar));
        }
        $this->paymentReason = $reason;
    }

    private function getPaymentReason() {
        if ($this->paymentReason == NULL)
            return $this->getPadding(28)
                    . $this->getPadding(28)
                    . $this->getPadding(28)
                    . $this->getPadding(28);
        else {
            $reasons = $this->paymentReason;
            $reason = '';
            while ($line = array_pop($reasons)) {
                $reason .= $line;
            }
            return $reason;
        }
    }

    private function getEndRecipient() {
        return $this->getPadding(30)
                . $this->getPadding(24)
                . $this->getPadding(24)
                . $this->getPadding(24)
                . $this->getPadding(24);
    }

    protected function getPadding($length) {
        $padding = '';
        for ($i = 1; $i <= $length; $i++) {
            $padding .= $this->fillChar;
        }
        return $padding;
    }

    private function replaceChars($string) {
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