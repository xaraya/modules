<?php
/**
 * Produce pdf output from the xmldata supplied
 *
 */
function reports_userapi_pdf_output($args)
{
    extract($args);
    
    // Prepare arguments to push a pdf to the client
    $arguments = array (
                        'format'       => "pdf",
                        'filename'     => $report['name'].".pdf",
                        'disposition'  => "attachment",
                        'xmldata'      => $xmldata);
    
    // Push output to client
    if(!xarModApiFunc('reports','user','push_output',$arguments)) return;
    exit;
}
?>