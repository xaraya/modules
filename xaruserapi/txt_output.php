<?php
/**
* Produce text output from the xmldata supplied
 *
 */
function reports_userapi_txt_output($args)
{
    extract($args);
    
    // Prepare arguments to push a text document to the client
    $arguments = array (
                        'format'       => "txt",
                        'filename'     => $report['name'].".txt",
                        'disposition'  => "attachment",
                        'xmldata'      => $xmldata);
    
    // Push output to client
    xarModApiFunc('reports','user','push_output',$arguments);
    exit;
}
?>