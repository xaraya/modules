<?php
/**
* Produce svg output from the xmldata supplied
 *
 */
function reports_userapi_svg_output($args)
{
    extract($args);
    
    // Prepare arguments to push a pdf to the client
    $arguments = array (
                        'format'       => "svg",
                        'filename'     => $report['name'].".svg",
                        'disposition'  => "attachment",
                        'xmldata'      => $xmldata);
    
    // Push output to client
    if(!xarModApiFunc('reports','user','push_output',$arguments)) return;
    exit;
}
?>