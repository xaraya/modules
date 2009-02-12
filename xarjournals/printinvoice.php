<?php

function labaccounting_journals_printinvoice($args) {
    require_once 'PEAR.php';
    require_once('HTML_ToPDF.php');

    if (!xarVarFetch('transactionid', 'id', $transactionid)) return;

    extract($args);
    
    $transaction = xarModAPIFunc('labaccounting',
                          'journaltransactions',
                          'get',
                          array('transactionid' => $transactionid));
    if($transaction == false) return;
    
    $journalid = $transaction['journalid'];
    
    
    
    
    
    
    
    
    
    
    
    
    $htmlOutput = xarModFunc('labaccounting', 'journals','invoice', array('journalid' => $journalid, 'printable' => 1, 'pageName' => 'pdf'));
    $temp = 'modules/labaccounting/pdf_quotes/invoice-'.$journalid.'-'.$transactionid.'.pdf';
    $fp = fopen($temp, 'w');
    fwrite($fp, $htmlOutput);
    fclose($fp);
    
    $htmlFile = xarModURL('labaccounting', 'journals','invoice', array('journalid' => $journalid, 'pageName' => 'pdf'));
    $defaultDomain = "www.miragelab.com/xarML";
    $pdfFile = "modules/labaccounting/pdf_quotes/quote-".$journalid."-".$transactionid.".pdf";
    @unlink($pdfFile);
    $pdf = & new HTML_ToPDF($temp, $defaultDomain, $pdfFile);
    $pdf->setHeader('left', ' ');
    $pdf->setHeader('font-family', 'helvetica');
    $pdf->setAdditionalCSS('body {font-family: sans-serif;}');
    $result = $pdf->convert();
    if (PEAR::isError($result)) {
        die($result->getMessage());
    }
    @unlink($temp);
    
    if(!is_string($result)) {
        return $result->getMessage();
    } else {
        xarResponseRedirect($pdfFile);
        return true;
//        $output = "<a href='modules/labaccounting/pdf_quotes/" . basename($result)."'>Download PDF</a>";
//       return $output;
    }
}

?>