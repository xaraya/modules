<?php
/**
 * Process a delete request for reports
 */
function reports_admin_delete_report($args) {
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
    if (!xarModAPIFunc('reports','admin','delete_report',array('rep_id'=>$rep_id))) {
        return false;
    }
	
	xarResponseRedirect(xarModUrl('reports','admin','view_reports',array()));
	return true;
}

//-----------------------------------------------------------------
//
//  Config display functions
//
//-----------------------------------------------------------------
function reports_admin_modify_config() {
	$backends= array( array('id'=>'ezpdf',
                            'name'=> xarML('EzPDF (pure PHP)')), 
                      array('id'=>'yaps',
                            'name'=> xarML('YaPS (GS based)')), 
                      array('id'=>'pdflib',
                            'name'=> xarML('pdfLib (C-library)'))
                      );
    
	$data = array('authid' => xarSecGenAuthKey(),
                  'rep_location' => xarModGetVar('reports','reports_location'),
                  'img_location' => xarModGetVar('reports','images_location'),
                  'backends' => $backends,
                  'selectedbackend' => xarModGetVar('reports','pdf_backend')
                  );
    
	return $data;
}

?>