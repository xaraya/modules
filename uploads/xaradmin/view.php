<?php

xarModAPILoad('uploads','user');

function uploads_admin_view( ) {
    //security check
    if (!xarSecurityCheck('AdminUploads')) return;
    
    /**
     *  Validate variables passed back
     */
     
    if (!xarVarFetch('mimetype',    'int:0:'    , $mimetype,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('subtype',     'int:0:',     $subtype,          NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('status',      'int:0:',     $status,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fileId',      'list:int:1', $fileId,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fileDo',      'str:5:',     $fileDo,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('action',      'int:0:',     $action,           NULL, XARVAR_DONT_SET)) return;
    
    /**
     *  Determine the filter settings to use for this view
     */
    
    if (!isset($mimetype) || !isset($subtype) || !isset($status)) {
        // if the filter settings are empty, then 
        // grab the users last view filter

        $options  = unserialize(xarModGetUserVar('uploads','view.filter'));
        $data     = $options['data'];
        $filter   = $options['filter'];
        unset($options);
    } else {
        // otherwise, grab whatever filter options where passed in
        // and process them to create a filter

        $filters['mimetype'] = $mimetype;        
        $filters['subtype']  = $subtype;
        $filters['status']   = $status;

        $options =  xarModAPIFunc('uploads','user','process_filters', $filters);
        $data     = $options['data'];
        $filter   = $options['filter'];
        unset($options);
    }
    
    /**
     * Perform all TYPE specific actions
     */
    
    if (isset($action)) {
        
        
        if ($action > 0) {
            if (isset($fileDo) && strtolower($fileDo) == 'change') {
                $args['fileId']     = $fileId;
            } else {
                $args['fileType']   = $filter['fileType'];
                $args['curStatus']  = $filter['fileStatus'];
            }
        }
        
        switch ($action) {
            case _UPLOADS_STATUS_APPROVED:
                    xarModAPIFunc('uploads','user','db_change_status', $args +  array('newStatus'    => _UPLOADS_STATUS_APPROVED));
                    break;
            case _UPLOADS_STATUS_SUBMITTED:
                    xarModAPIFunc('uploads','user','db_change_status', $args +  array('newStatus'    => _UPLOADS_STATUS_SUBMITTED));
                    break;
            case _UPLOADS_STATUS_REJECTED:
                /**
                 *  If auto-purge is turned on, then don't set to rejected state,
                 *  instead, just get confirmation and delete right away
                 */
                if (xarModGetVar('uploads','file.auto-purge')) {
                    $files = xarModAPIFUnc('uploads','user','db_get_file',      $args);
                    return xarModFunc('uploads','admin','confirm_delete',       array('fileList' => $files));
                } else {
                    xarModAPIFunc('uploads','user','db_change_status', $args +  array('newStatus'   => _UPLOADS_STATUS_REJECTED));
                }                          
                break;
            case 0: /* Change View or anything not defined */
            default:
                break;
        }
    }
    
    /**
     * TODO: Perform all FILE specific actions
     */
     
     
     
	/**
     * Grab a list of files based on the defined filter 
     */
     
    if (!isset($filter) || count($filter) <= 0) {
        $items = xarModAPIFunc('uploads', 'user', 'db_getall_files');
    } else {
        $items = xarModAPIFunc('uploads', 'user', 'db_get_file', $filter);
    }
    
    /**
     *  Check for exceptions
     */
    if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    $data['items'] = $items;
    $data['authid'] = xarSecGenAuthKey();
    
    return $data;   
}

?>
