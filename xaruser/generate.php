<?php
/**
 * Generate a report
 *
 */
function reports_user_generate($args) 
{
    xarVarFetch('rep_id','id::',$rep_id);
    xarVarFetch('action','str::',$action,'search', XARVAR_NOT_REQUIRED);
    xarVarFetch('format','str::',$format,xarModGetVar('reports','default_output'), XARVAR_NOT_REQUIRED);
    extract($args);
    
    // Get the details of the report
    $report = xarModAPIFunc('reports','user','report_get',array('rep_id' => $rep_id));
       
    // Get the settings for the connection for this report
    $reploc = xarModGetVar('reports','reports_location');
    $imgloc = xarModGetVar('reports','images_location');
  
    // Test whether the report definition is actually there
    if(!file_exists($reploc . '/' . $report['xmlfile'])) {
        $msg = xarML('The report definition file "#(1)" does not exist', $report['xmlfile']);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', $msg);
        return;
    }

    return xarModApiFunc('reports','user','produce_output',array('report' => $report, 'action' => $action, 'format' => $format));
}

?>