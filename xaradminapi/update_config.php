<?php
/**
 * Config administrative functions
 *
 * Only the update functions exists, config cannot be deleted and is
 * the only instance of the object
 */
function reports_adminapi_update_config($args)
{
    // Get parameters
    extract($args);

    // Update config variables
    xarModSetVar('reports','reports_location',$config_replocation);
    xarModSetVar('reports','images_location',$config_imglocation);
    xarModSetVar('reports','pdf_backend',$config_pdfbackend);
    xarModSetVar('reports','default_output',$config_defaultoutput);
    xarModSetVar('reports','itemsperpage', $config_itemsperpage);
    xarModSetVar('reports','fop_location', $config_foplocation);
    return true;
}


?>