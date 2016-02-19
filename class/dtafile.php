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
 * Class to collect transaction data into a file
 *
 */

class DTA_File {

    private $transactions = array();
    private $transactionCounter = 0;
    private $creationDate;
    private $ident;
    private $clearingNr;
    public  $currentTransaction = NULL;

    public function __construct($ident, $clearingNr) {
        $this->creationDate = date('ymd');
        $this->ident = $ident;
        $this->clearingNr = $clearingNr;
    }

    public function addTransaction($type) {
        $this->transactionCounter++;
        $seqNr = $this->transactionCounter;
        sys::import('modules.payments.class.dta_TA' . $type);
        $class = 'DTA_TA' . $type;
        $this->transactions[$seqNr] = new $class();
        $this->transactions[$seqNr]->setInputSequenceNr($seqNr);
        $this->transactions[$seqNr]->setCreationDate($this->creationDate);
        $this->transactions[$seqNr]->setDataFileSender($this->ident);
        return $seqNr;
    }

    public function loadTransaction($seqNr) {
        return $this->transactions[$seqNr];
    }

    public function saveTransaction($seqNr, $transaction) {
        return $this->transactions[$seqNr] = $transaction;
    }

    private function createTotalRecord() {
        $sum = 0;
        foreach ($this->transactions as $transaction) {
            $sum += $transaction->getPaymentAmountNumeric();
        }
//        echo "Sum Amount: " . $sum . " &euro;<br />\n";
        $id = $this->addTransaction(890);
        $totalRecord = $this->loadTransaction($id);
//        echo "Sum Records: " . $id . " <br />\n";
        $totalRecord->setTotalAmount($sum);
        $this->saveTransaction($id, $totalRecord);
    }

    public function toFile($filename) {
        $this->createTotalRecord();
        $fptr = fopen($filename, 'w+');
        if (!$fptr)
            throw new Exception(xarML("Cannot open the file #(1)", $filename));
        foreach ($this->transactions as $transaction) {
            fwrite($fptr, $transaction->toString());
        }
        fclose($fptr);
    }

    public function toString() {
        $this->createTotalRecord();
        $output = '';
        foreach ($this->transactions as $transaction) {
            $output .= $transaction->toString();
        }

        return $output;
    }

    public function download() {
        $output = $this->toString();

        $filename = 'DTAExport_' . time() . ".txt";
        file_put_contents('DTAExport_' . time() . ".txt", $output);
        
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $output;
        exit;
    }
    
}

?>