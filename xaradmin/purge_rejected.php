<?php

/**
 *  Purges all files with REJECTED status from the system 
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   boolean    confirmation    whether or not to skip confirmation 
 *  @param   string     authid          the authentication id 
 *  @returns  void
 *  
 */

xarModAPILoad('filemanager', 'user');

function filemanager_admin_purge_rejected( $args ) 
{
    
    extract ($args);
    
    if (!xarSecurityCheck('DeleteFileManager')) return;
    
    if (isset($authid)) {
        $_GET['authid'] = $authid;
    }
    
    if (!isset($confirmation)) {
        xarVarFetch('confirmation', 'int:1:', $confirmation, '', XARVAR_NOT_REQUIRED);
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) 
        return;

    
    if ((isset($confirmation) && $confirmation) || !xarModGetVar('filemanager', 'file.delete-confirmation')) {
        $fileList = xarModAPIFunc('filemanager', 'user', 'db_get_file', 
                                   array('fileStatus' => _FILEMANAGER_STATUS_REJECTED));

        if (empty($fileList)) {
            xarResponseRedirect(xarModURL('filemanager', 'admin', 'view'));
            return;
        } else {
            $result = xarModAPIFunc('filemanager', 'user', 'purge_files', array('fileList' => $fileList));
            if (!$result) {
                $msg = xarML('Unable to purge rejected files!');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILEMANAGER_NO_PURGE', new SystemException($msg));
                return;
            } 
        }
    } else {
        $fileList = xarModAPIFunc('filemanager', 'user', 'db_get_file', 
                                   array('fileStatus' => _FILEMANAGER_STATUS_REJECTED));
        if (empty($fileList)) {
            $data['fileList']   = array();
        } else {
            $data['fileList']   = $fileList;
        }
        $data['authid']     = xarSecGenAuthKey();
        
        return $data;        
    }
                        
    xarResponseRedirect(xarModURL('filemanager', 'admin', 'view'));
}
?>