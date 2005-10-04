<?php

function filemanager_admin_get_files() 
{
    
    if (!xarSecurityCheck('AddFileManager')) return;

    $actionList[] = _FILEMANAGER_GET_UPLOAD;
    $actionList[] = _FILEMANAGER_GET_EXTERNAL;
    $actionList[] = _FILEMANAGER_GET_LOCAL;
    $actionList[] = _FILEMANAGER_GET_REFRESH_LOCAL;
    $actionList = 'enum:' . implode(':', $actionList);

    if (!xarVarFetch('action',        $actionList, $action, '', XARVAR_NOT_REQUIRED)) return;
    
    // StoreType can -only- be one of FSDB or DB_FULL
    $storeTypes = _FILEMANAGER_STORE_FSDB . ':' . _FILEMANAGER_STORE_DB_FULL;
    if (!xarVarFetch('storeType',     "enum:$storeTypes", $storeType, '', XARVAR_NOT_REQUIRED)) return;

    // now make sure someone hasn't tried to change our maxsize on us ;-)
    $file_maxsize = xarModGetVar('filemanager', 'file.maxsize');

    if (!isset($action)) {
        $action = NULL;
    }
    $args['action']    = $action;

    switch ($action) {
        case _FILEMANAGER_GET_UPLOAD:
            if (!xarVarFetch('MAX_FILE_SIZE', "int::$file_maxsize", $maxsize)) return;
            $uploadList = xarModAPIFunc('filemanager', 'user', 'prepare_uploads');
            $resultList = xarModAPIFunc('filemanager', 'user', 'store', 
                array(
                    'fileList'  => $uploadList,
                    'storeType' => $storeType
                )
            );
            xarResponseRedirect(xarModURL('filemanager', 'admin', 'get_files'));                
            return;
            break;
        case _FILEMANAGER_GET_EXTERNAL:
            // minimum external import link must be: ftp://a.ws  <-- 10 characters total
            if (!xarVarFetch('import', 'regexp:/^([a-z]*).\/\/(.{7,})/', $import, 'NULL', XARVAR_NOT_REQUIRED)) return;
            $args['import'] = $import;
            break;
        case _FILEMANAGER_GET_LOCAL:
            if (!xarVarFetch('fileList', 'list:regexp:/(?<!\.{2,2}\/)[\w\d]*/', $fileList)) return;
            if (!xarVarFetch('file_all', 'checkbox', $file_all, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('addbutton', 'str:1', $addbutton, '',  XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('delbutton', 'str:1', $delbutton, '',  XARVAR_NOT_REQUIRED)) return;
            
            if (empty($addbutton) && empty($delbutton)) {
                $msg = xarML('Unsure how to proceed - missing button action!');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            } else {
                $args['bAction'] = (!empty($addbutton)) ? $addbutton : $delbutton;
            }
            
            $cwd = xarModGetUserVar('filemanager', 'path.imports-cwd');
            foreach ($fileList as $file) {
                $args['fileList']["$cwd/$file"] = xarModAPIFunc('filemanager', 'fs', 'get_metadata', 
                                                                 array('fileLocation' => "$cwd/$file"));
            }
            $args['getAll'] = $file_all;
        
            break;
        default:
        case _FILEMANAGER_GET_REFRESH_LOCAL:
            $conflictList = xarModAPIFunc('filemanager', 'user', 'db_get_file_entry', 
                array(
                    'userId' => xarUserGetVar('uid'),
                    'fileLocation' => 'conflict://%'
                )
            );
            
            if (count($conflictList)) {
                foreach ($conflictList as $fileId => $fileInfo) {
                    eregi('conflict\:\/\/([0-9]+)\/(xarfs|xardb|mount)(.*)', 
                        $fileInfo['location']['uri'], $matches);

                    $original = @end(xarModAPIFunc('filemanager', 'user', 'db_get_file_entry',
                        array(
                            'fileName'      => $fileInfo['name'],
                            'fileLocation'  => "$matches[2]://$matches[1]/%",
                        )
                    ));
                    $conflicts[$fileId]['id']['original'] = $original['id'];
                    $conflicts[$fileId]['id']['conflict'] = $fileId;
                    $conflicts[$fileId]['name']['original'] = $original['name'];
                    $conflicts[$fileId]['name']['conflict'] = $fileInfo['name'];
                    $conflicts[$fileId]['size']['original'] = $original['size'];
                    $conflicts[$fileId]['size']['conflict'] = $fileInfo['size'];
                    $conflicts[$fileId]['type']['original'] = $original['mimetype'];
                    $conflicts[$fileId]['type']['conflict'] = $fileInfo['mimetype'];
                    $conflicts[$fileId]['time']['original'] = $original['time'];
                    $conflicts[$fileId]['time']['conflict'] = $fileInfo['time'];
                    $conflicts[$fileId]['location']['original'] = $original['location'];
                    $conflicts[$fileId]['location']['conflict'] = $fileInfo['location'];
               }
               xarDerefData('$conflicts', $conflicts, 0);
            }
            if (!xarVarFetch('inode', 'regexp:/(?<!\.{2,2}\/)[\w\d]*/', $inode, '', XARVAR_NOT_REQUIRED)) return;
            
            $cwd = '';

            $data['storeType']['DB_FULL']     = _FILEMANAGER_STORE_DB_FULL;
            $data['storeType']['FSDB']        = _FILEMANAGER_STORE_FSDB;
            $data['inodeType']['DIRECTORY']   = _INODE_TYPE_DIRECTORY;
            $data['inodeType']['FILE']        = _INODE_TYPE_FILE;
            $data['getAction']['LOCAL']       = _FILEMANAGER_GET_LOCAL;
            $data['getAction']['EXTERNAL']    = _FILEMANAGER_GET_EXTERNAL;
            $data['getAction']['UPLOAD']      = _FILEMANAGER_GET_UPLOAD;
            $data['getAction']['REFRESH']     = _FILEMANAGER_GET_REFRESH_LOCAL;
            $data['local_add_button']         = xarML('Add Files');
            $data['local_del_button']         = xarML('Delete Files');
            $data['local_import_post_url']    = xarModURL('filemanager', 'admin', 'get_files');
            $data['external_import_post_url'] = xarModURL('filemanager', 'admin', 'get_files');
            $data['upload_post_url'] = xarModURL('filemanager', 'admin', 'get_files');
            $data['fileList'] = array();
            $data['curDir'] = '';
            $data['noPrevDir'] = FALSE;
            // reset the CWD for the local import
            // then only display the: 'check for new imports' button
            $data['authid'] = xarSecGenAuthKey();
            $data['file_maxsize'] = $file_maxsize;
            return $data;
            break;
    }
    if (isset($storeType)) {
        $args['storeType'] = $storeType;
    }
    $list = xarModAPIFunc('filemanager','user','process_files', $args);
    if (is_array($list) && count($list)) {
        return xarTplModule('filemanager','admin', 'addfile-status', array('fileList' => $list), NULL);
    } else {
        xarResponseRedirect(xarModURL('filemanager', 'admin', 'get_files'));                return;
    }

    return $data;
}
?>