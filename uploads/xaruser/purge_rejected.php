<?php

xarModAPILoad('uploads', 'user');

function uploads_user_purge_rejected( $args ) {
    
    extract ($args);
    
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) 
            return;

    if (!isset($confirmation))
        xarVarFetch('confirmation', 'int:1:', $confirmation, '', XARVAR_NOT_REQUIRED);
    
    if (isset($confirmation) && $confirmation) {
        $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file', 
                                           array('fileStatus' => _UPLOADS_STATUS_REJECTED));

        if (empty($fileList)) {
            xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));
            return;
        } else {
            $result = xarModAPIFunc('uploads', 'user', 'purge_files', 
                                     array('fileList'   => $fileList));
            if (!$result) {
                $msg = xarML('Unable to purge rejected files!');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_NO_PURGE', new SystemException($msg));
                return;
            } 
        }
    } else {
        $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file', 
                                                                     array('fileStatus' => _UPLOADS_STATUS_REJECTED));
        if (empty($fileList)) {
            $data['fileList']   = array();
        } else {
            $data['fileList']   = $fileList;
        }
        $data['authid']     = xarSecGenAuthKey();
        
        return $data;        
    }
                        
    xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));

    return true;
}
?>
