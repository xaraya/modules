<?php

function reports_userapi_produce_output($args)
{
    extract($args);
    
    // First unroll the template into some format we can process
    $reportfile = xarModGetVar('reports','reports_location') . '/' . $report['xmlfile'];
    
    // Prepare the basic data
    $data = $report; // Pass the whole report array
    $data['rep_id'] = $report['id'];
    $data['connection'] = $report['conn_id'];
    $data['action'] = $action;
    $unrolled = xarTplFile($reportfile,$data);
    
    switch($action) {
        case 'search':
        case 'results':
            $output = $unrolled;
            break;
        case 'output':
            // If all went well, we have an xsl-fo in unrolled, process this further to
            // produce the proper output format
            switch($format) {
                case 'html':
                    $func = 'html_output';
                    break;
                case 'pdf':
                    $func = 'pdf_output';
                    break;
                case 'text':
                    $func = 'text_output';
                    break;
                default:
                    $msg = "Unknown report format ($format) specified";
                    xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', $msg);
                    return;
            }
            $output = xarModApiFunc('reports','user',$func, array('xmldata' => $unrolled, 'report' => $report));
    }
    return $output;
}