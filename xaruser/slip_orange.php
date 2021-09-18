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
 * Create an invoice with a Swiss orange payment slip
 *
 */

// Make sure we have the required libraries
$filepath = sys::lib() . 'tcpdf/tcpdf.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the tcpdf library'));
}
$filepath = sys::lib() . 'SwissPaymentSlipTcpdf/src/SwissPaymentSlip/SwissPaymentSlipTcpdf/SwissPaymentSlipTcpdf.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the SwissPaymentSlipTcpdf library'));
}
$filepath = sys::lib() . 'SwissPaymentSlipPdf/src/SwissPaymentSlip/SwissPaymentSlipPdf/SwissPaymentSlipPdf.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the SwissPaymentSlipPdf library'));
}
$filepath = sys::lib() . 'SwissPaymentSlip/src/SwissPaymentSlip/SwissPaymentSlip/SwissPaymentSlip.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the SwissPaymentSlip library'));
}
$filepath = sys::lib() . 'SwissPaymentSlip/src/SwissPaymentSlip/SwissPaymentSlip/SwissPaymentSlipData.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the SwissPaymentSlipData library'));
}

// Import necessary classes
sys::import('tcpdf/tcpdf');
sys::import('SwissPaymentSlip.src.SwissPaymentSlip.SwissPaymentSlip.SwissPaymentSlip');
sys::import('SwissPaymentSlip.src.SwissPaymentSlip.SwissPaymentSlip.SwissPaymentSlipData');
sys::import('SwissPaymentSlipPdf.src.SwissPaymentSlip.SwissPaymentSlipPdf.SwissPaymentSlipPdf');
sys::import('SwissPaymentSlipTcpdf.src.SwissPaymentSlip.SwissPaymentSlipTcpdf.SwissPaymentSlipTcpdf');

function payments_user_slip_orange()
{
    // Create an instance of TCPDF, setup default settings
    $tcPdf = new TCPDF('P', 'mm', 'A4', false, 'ISO-8859-1');

    // Since we currently don't have a OCRB font for TCPDF, we disable this
    //$tcPdf->AddFont('OCRB10');

    // Disable TCPDF's default behaviour of print header and paymentster
    $tcPdf->setPrintHeader(false);
    $tcPdf->setPrintpaymentster(false);

    // Add page, don't break page automatically
    $tcPdf->AddPage();
    $tcPdf->SetAutoPageBreak(false);

    // Insert a dummy invoice text, not part of the payment slip itself
    $tcPdf->SetFont('Arial', '', 9);
    $tcPdf->Cell(50, 4, "Just some dummy text.");

    // Create a payment slip data container (value object)
    $paymentSlipData = new SwissPaymentSlipData();

    // Fill the data container with your data
    $paymentSlipData->setBankData('Seldwyla Bank', '8001 Zürich');
    $paymentSlipData->setAccountNumber('01-145-6');
    $paymentSlipData->setRecipientData('H. Muster AG', 'Versandhaus', 'Industriestrasse 88', '8000 Zürich');
    $paymentSlipData->setPayerData('Rutschmann Pia', 'Marktgasse 28', '9400 Rorschach');
    $paymentSlipData->setAmount(2830.50);
    $paymentSlipData->setReferenceNumber('7520033455900012');
    $paymentSlipData->setBankingCustomerId('215703');

    // Create a payment slip object, pass in the prepared data container
    $paymentSlip = new SwissPaymentSlip($paymentSlipData, 0, 191);

    // Since we currently don't have a OCRB font for TCPDF, we set it to one we certainly have
    $paymentSlip->setCodeLineAttr(null, null, null, null, null, 'Helvetica');

    // Create an instance of the TCPDF implementation, can be used for TCPDF, too
    $paymentSlipTcpdf = new SwissPaymentSlipTcpdf($tcPdf, $paymentSlip);

    // "Print" the slip with its elements according to their attributes
    $paymentSlipTcpdf->createPaymentSlip();

    // Output PDF named example_tcpdf_orange_slips.pdf to examples folder
    $tcPdf->Output(__DIR__ . DIRECTORY_SEPARATOR . 'example_tcpdf_orange_slips.pdf', 'F');
}
