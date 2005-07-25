<?php

function uploads_user_browser($args)
{
    // put all the vars out of the args array and into their own vars
    extract($args);


    // put the pathInfo vars in their own vars out of the array
    extract($pathInfo);

    $files_Linfo = $linkInfo;
    $files_Linfo['args']['action'] = 'info';

    $fileList = xarModAPIFunc('uploads', 'user', 'get_file_list',
                               array('path'     => $path,
                                     'sortby'   => $sortby,
                                     'sortdir'  => $sortdir,
                                     'linkInfo' => $files_Linfo));

    // If fileList is NULL, we have an error
    if (!isset($fileList)) {
        return;
    }

    $data['fileList'] = $fileList;
    $data['fileInfo'] = array();

    // get the prepopulated array of files. this is from both previously attached items, or
    // items the user has attached in this file selecting session
    if (isset($prefix) && !empty($prefix)) {
        $varName = 'files.selected.' . $prefix;
    } else {
        $varName = 'files.selected';
    }

    // get the selected files the calling page that pop us up defined via xarModSetUserVar
    $selectedFiles = @unserialize(xarModGetUserVar('uploads', $varName));

    // make sure we got some files, if note, set selected files to an array
    if (!isset($selectedFiles) || empty($selectedFiles)) {
        $selectedFiles = array();
    } else {
        // make sure each file exists by passing the list of fileIds to db_get_file_entry()
        // if the files exist as db entries, then we should get them all back in the result
        $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file_entry', array('fileId' => $selectedFiles));
        if (is_array($fileList)) {
            // Set the selectedFiles equal to the valid fileIds returned above
            $selectedFiles = array_keys($fileList);
        }
    }

    // if we have a file ID, see if there is an action to do on it
    if (isset($fileId) && $fileId > 0) {
        // determin which actio to do
        switch($action) {
            case 'delsel':
                 // we're deliting, so unset it in the selected files array and then
                 // set the user var with the new array w/o the fileID
                if (in_array($fileId, $selectedFiles)) {
                    unset($selectedFiles[array_search($fileId, $selectedFiles)]);
                    xarModSetUserVar('uploads', $varName, serialize($selectedFiles));
                }
                break;
            case 'addsel':
                 // we're adding, so set it in the selected files array and then
                 // set the user var with the new array and the new the fileID
                if (!in_array($fileId, $selectedFiles)) {
                    $selectedFiles[] = $fileId;
                    xarModSetUserVar('uploads', $varName, serialize($selectedFiles));
                }
                break;
            case 'info':
            default:
                $fInfo = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId));
                $fInfo = $fInfo[$fileId];

                $data['fileInfo']['name']    = $fInfo['name'];
                $data['fileInfo']['id']      = $fInfo['id'];
                $data['fileInfo']['date']    = $fInfo['time']['modified'];
                $data['fileInfo']['size']    = $fInfo['size']['text']['short'];
                $data['fileInfo']['type']    = $fInfo['mimetype']['text'];

                $fInfoArgs = $linkInfo['args'];
                $fInfoArgs['vpath']  = $path . '/' . $fInfo['name'];
                $fInfoArgs['tab']    = 'browser';

                if (in_array($fInfo['id'], $selectedFiles)) {
                    $fInfoArgs['action'] = 'delsel';
                    $link = xarModURL('uploads', 'user', 'file_selector', $fInfoArgs);

                    $actions['unselect']['label'] = xarML('Unselect');
                    $actions['unselect']['link']  = $link;
                } else {
                    $fInfoArgs['action'] = 'addsel';
                    $link = xarModURL('uploads', 'user', 'file_selector', $fInfoArgs);

                    $actions['select']['label'] = xarML('Select');
                    $actions['select']['link']  = $link;
                }
                $data['fileInfo']['actions'] = $actions;
                break;

        }
    }

    $data['attachment_list']  = ';' . implode(';', $selectedFiles);
    $data['attachment_total'] = count($selectedFiles);

    if (count($selectedFiles)) {
        $sList = xarModAPIFunc("uploads","user","db_get_file", array('fileId' => $selectedFiles));
        $selectedFiles = array();

        foreach ($sList as $file) {

            $selectedFiles[$file['id']]['path'] = $file['location']['virtual'];

            $linkargs = $linkInfo['args'];
            $linkargs['vpath']  = $file['location']['virtual'];
            $linkargs['tab']    = 'browser';
            $linkargs['action'] = 'delsel';
            $link = xarModURL('uploads', 'user', 'file_selector', $linkargs);

            $selectedFiles[$file['id']]['dellink'] = $link;
            $selectedFiles[$file['id']]['getlink'] = xarModURL('uploads', 'user', 'download', array('vpath' => $file['location']['virtual']));

            $linkargs['action'] = 'info';
            $selectedFiles[$file['id']]['infolink'] = xarModURL('uploads', 'user', 'file_selector', $linkargs);

            unset($linkargs['action']);
            unset($linkargs['fileId']);
            $linkargs['vpath']  = $file['location']['virtual'];
            $selectedFiles[$file['id']]['dirlink'] = xarModURL('uploads', 'user', 'file_selector', $linkargs);

        }
    } else {
        $selectedFiles = array();
    }

    $data['selectedFiles'] = $selectedFiles;

    // prep the data to be passed to the template
    $data['path']                = $path;
    $data['sortby']                = $sortby;
    $data['sortdir']            = $sortdir;
    $data['prefix']                = $prefix;
    $data['pathInfo']            = $pathInfo;
    $data['linkInfo']            = $linkInfo;
    $data['dirList']            = $dirList;
    $data['finished']            = $finished;
    $data['directorybrowser']    = $directorybrowser;

    // send it on down to the tempalte
    return $data;
}
?>