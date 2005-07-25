<?php

/**
 * lists the contents of the specified directory returning the result as an
 * array of files and folders -> their metadata, in the order specified by sortby/direction
 *
 * @param   integer $vdir_id     ID of the directory whose contents we want to list
 * @param   integer $sortby      attribute to sort by: TYPE, NAME, SIZE, OWNER, DATE_DATE
 * @param   integer $direction   ascending or descending
 * @returns array
 * @return  array of files and directories in the order specified by sortby/direction, or FALSE on error
 */

function uploads_vdirapi_get_dir_contents( $args )
{
    $location   = NULL;      // Location to search for when grabbing list of files
    $vdir_id    = NULL;      // Virtual directory id 
    $linkInfo   = NULL;      // information used to create the link for each file
    $files      = NULL;      // list of files in the specified location
    $list       = NULL;      // the (formatted) list of files returned to the callee
    $type       = 'xarfs';   // default to the xarfs file storage type
    $pathSuffix = NULL;      // the path suffix to append to the location search string (if any)
    $linkInfo   = array('func' => 'download',
                        'args' => array());
    extract($args);

    if (!isset($vdir_id) || empty($vdir_id)) {
        $msg = xarML('Missing parameter [#(1)] for function [(#(2)] in module [#(3)]',
                     'vdir_id', 'vdir_get_dir_contentents', 'uploads');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    // Create the location search string
    $location = "$type://" . str_replace('//', '/', "$vdir_id/$pathSuffix%");
    $vpath = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $vdir_id));

    $files = xarModAPIFunc('uploads', 'user', 'db_get_file_entry', 
                            array('fileLocation' => $location));

    if (count($files)) {
        foreach ($files as $key => $file) {

            $linkfunc =& $linkInfo['func'];
            $linkargs =& $linkInfo['args'];
            $linkargs['vpath'] = str_replace('//', '/', $vpath . '/' . $file['name']);
            
            $list[$key]['name']       = $file['name'];
            $list[$key]['id']         = $file['id'];
            $list[$key]['location']   = $file['location']['virtual'];
            $list[$key]['type']       = $file['mimetype']['text'];
            $list[$key]['type-image'] = $file['mimetype']['imagepath'];
            $list[$key]['link']       = xarModURL('uploads', 'user', $linkfunc, $linkargs);
            $list[$key]['comment']    = '';
            $list[$key]['owner']      = $file['owner']['name'];
            $list[$key]['size']       = $file['size']['text']['short'];
            $list[$key]['sizeval']    = $file['size']['value'];
            $list[$key]['time']       = $file['time']['modified'] ? $file['time']['modified'] : $file['time']['created'];
        }
    } else {
        $list = array();
    }

    return $list;
}

?>
