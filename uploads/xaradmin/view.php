<?php

xarModAPILoad('uploads','user');

function uploads_admin_view( ) {
    //security check
    if (!xarSecurityCheck('AdminUploads')) return;
    
    //get filter
    if (!xarVarFetch('fileId',  'int:1:', $args['fileId'],  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fileName','str:1:', $args['fileName'],NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('mimetype','int:0:', $mimetype,        NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('subtype', 'int:0:', $subtype,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('status',  'int:0:', $status,          NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('action',  'int:0:', $action,          NULL, XARVAR_DONT_SET)) return;
  
    // TODO: cache the filter information in a session var so that,
    //       should the admin choose the accept, reject, etc buttons - 
    //       they can retain their filter settings :)
    
    // make sure the keys aren't set if their 
    // corressponding values  are empty in the array
    foreach($args as $key => $value) {
        if (!trim($value)) {
            unset($args[$key]);
        }
    }
    
    /**
     *  Set up the MIME type filter 
     */
     
    if (isset($mimetype) && $mimetype > 0) {
        // returns array[typeid]{typeid, typename}
        $selected_mimetype = xarModAPIFunc('mime','user','get_type', array('typeId' => $mimetype));
    } 
    
    // add the rest of the types to the array
    // array returns is in form of: array[typeid]{[typeId], [typeName]}
    $data['filter']['mimetypes']              = xarModAPIFunc('mime','user','getall_types');
    
    // Set up the default 'all' type
    $default['typeName']           = xarML('All');
    $default['typeId']             = 0;
    
    array_unshift($data['filter']['mimetypes'], $default);
    
    // if selected mimetype isn't set, empty or has an array count of 
    // zero, then we set 
    if (!isset($selected_mimetype) || count($selected_mimetype) != 2) {
        $data['filter']['mimetypes']['selected'] = $data['filter']['mimetypes'][0];
        unset($data['filter']['mimetypes']['default']);
        unset($selected_mimetype);
    } else {
        $data['filter']['mimetypes']['selected'] = $selected_mimetype;
        unset($data['filter']['mimetypes'][$selected_mimetype['typeId']]);
    }
    
    ksort($data['filter']['mimetypes']);
    /**
     *  Set up the MIME subtype filter
     */
    if (isset($selected_mimetype)) {
        if (isset($subtype) && $subtype > 0) {
            // returns array[typeid]{typeid, typename}
            $selected_subtype = xarModAPIFunc('mime','user','get_subtype', array('subtypeId' => $subtype));
        } 

        // add the rest of the types to the array
        // array returns is in form of: array[typeId]{[subtypeId], [subtypeName]}
        $data['filter']['subtypes'] = xarModAPIFunc('mime','user','getall_subtypes',
                                                     array('typeId' => $selected_mimetype['typeId']));
        
        // Set up the default 'all' type
        $data['filter']['subtypes']['default']['subtypeName']           = xarML('All');
        $data['filter']['subtypes']['default']['subtypeId']             = 0;

        // if selected subtype isn't set, empty or has an array count of 
        // zero, then we set 
        if ((!isset($selected_subtype) || !isset($selected_subtype['subtypeId'])) || 
            (isset($selected_subtype['typeId']) && 
             $selected_subtype['typeId'] != $selected_mimetype['typeId'])) {
                $data['filter']['subtypes']['selected'] = $data['filter']['subtypes']['default'];
                unset($data['filter']['subtypes']['default']);
                unset($selected_subtype);
        } else {
            $data['filter']['subtypes']['selected'] = $selected_subtype;
            unset($data['filter']['subtypes'][$selected_subtype['subtypeId']]);
        }
    } else {
        // Set up the default 'all' type
        $data['filter']['subtypes']['default']['subtypeName']           = xarML('All');
        $data['filter']['subtypes']['default']['subtypeId']             = 0;
    }
    if (isset($selected_mimetype)) {
        if (isset($selected_subtype)) {
            $filter['fileType'] = strtolower($selected_mimetype['typeName']) . '/' . strtolower($selected_subtype['subtypeName']);
        } else {
            $filter['fileType'] = strtolower($selected_mimetype['typeName']) . '/%';
        }
    } else {
        $filter['fileType'] = '%';
    }
    
    if (!isset($status)) {
        $status = '';
    }
    /**
     *  Set up the MIME subtype filter
     */
    $data['filter']['status'] = array();
    $data['filter']['status'][0]['statusId']     = 0;
    $data['filter']['status'][0]['statusName']   = xarML('All');
    $data['filter']['status'][_UPLOADS_STATUS_REJECTED]['statusId']     = _UPLOADS_STATUS_REJECTED;
    $data['filter']['status'][_UPLOADS_STATUS_REJECTED]['statusName']   = 'Rejected';
    $data['filter']['status'][_UPLOADS_STATUS_APPROVED]['statusId']     = _UPLOADS_STATUS_APPROVED;
    $data['filter']['status'][_UPLOADS_STATUS_APPROVED]['statusName']   = 'Approved';
    $data['filter']['status'][_UPLOADS_STATUS_SUBMITTED]['statusId']    = _UPLOADS_STATUS_SUBMITTED;
    $data['filter']['status'][_UPLOADS_STATUS_SUBMITTED]['statusName']  = 'Submitted';
      
    switch($status) {
        case _UPLOADS_STATUS_REJECTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_REJECTED;
            $data['filter']['status']['selected'] = $data['filter']['status'][_UPLOADS_STATUS_REJECTED];
            unset($data['filter']['status'][_UPLOADS_STATUS_REJECTED]);
            break;
        case _UPLOADS_STATUS_SUBMITTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_SUBMITTED;
            $data['filter']['status']['selected'] = $data['filter']['status'][_UPLOADS_STATUS_SUBMITTED];
            unset($data['filter']['status'][_UPLOADS_STATUS_SUBMITTED]);
            break;
        case _UPLOADS_STATUS_APPROVED:
            $filter['fileStatus'] = _UPLOADS_STATUS_APPROVED;
            $data['filter']['status']['selected'] = $data['filter']['status'][_UPLOADS_STATUS_APPROVED];
            unset($data['filter']['status'][_UPLOADS_STATUS_APPROVED]);
            break;
        default:
            $data['filter']['status']['selected'] = $data['filter']['status'][0];
            unset($data['filter']['status'][0]);
            break;
    }        
        
    $filter = array_merge($filter, $args);
    // echo "<br /><pre> filter => ";print_r($filter); echo "</pre>";
    if (!isset($filter) || count($filter) <= 0) {
        $items = xarModAPIFunc('uploads', 'user', 'db_getall_files');
    } else {
        $items = xarModAPIFunc('uploads', 'user', 'db_get_file', $filter);
    }
    
    
    // Check for exceptions
    if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    $data['items'] = $items;
    $data['authid'] = xarSecGenAuthKey();
    
    
    // echo "<br /><pre>";print_r($data);echo "</pre><br />";
     
    return $data;   
}

?>
