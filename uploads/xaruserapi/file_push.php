<?php

/**
 *  Pushes a file to the client browser
 *
 *  @author   Carl P. Corliss
 *  @access   public
 *  @param    string    fileName        The name of the file
 *  @param    string    fileLocation    The full path to the file
 *  @param    string    fileType        The mimetype of the file
 *  @param    long int  fileSize        The size of the file (in bytes)
 *  @returns  boolean                   This function will exit upon succes and, returns False and throws an exception otherwise
 *  @throws   BAD_PARAM                 missing or invalid parameter
 *  @throws   UPLOADS_ERR_NO_READ       couldn't read from the specified file
 */

function uploads_userapi_file_push( $args )
{

    extract ( $args );

    if (!isset($fileName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileName','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($fileLocation)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileLocation','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($fileType)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileType','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($storeType)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'storeType','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } elseif ($storeType & _UPLOADS_STORE_DB_DATA) {
        if (!isset($fileId)) {
            $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                        'fileId','file_push','uploads');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return FALSE;
        }
    }

    if (!isset($fileSize)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileSize','file_push','uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    // Close the buffer, saving it's current contents for possible future use
    // then restart the buffer to store the file
    do {
        $pageBuffer[] = ob_get_contents();
    } while (@ob_end_clean())

    $buffer = array_reverse($pageBuffer);
    $pageBuffer = $buffer;
    unset($buffer);

    // Start buffering for the file
    ob_start();

    $finished = FALSE;

    if ($storeType & _UPLOADS_STORE_FILESYSTEM || ($storeType == _UPLOADS_STORE_DB_ENTRY)) {

        $fp = @fopen($fileLocation, 'rb');
        if(is_resource($fp))   {
            do {
                $data = fread($fp, 65536);
                if (strlen($data) == 0) {
                    break;
                } else {
                    print("$data");
                }
            } while (TRUE);

            fclose($fp);
        }

        // Headers -can- be sent after the actual data
        // Why do it this way? So we can capture any errors and return if need be :)
        // not that we would have any errors to catch at this point but, mine as well
        // do it incase I think of some errors to catch
        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-type: $fileType");
        header("Content-disposition: attachment; filename=\"$fileName\"");
        if ($fileSize) {
            header("Content-length: $fileSize");
        }

        // TODO: evaluate registering shutdown functions to take care of
        //       ending Xaraya in a safe manner
        $finished = TRUE;

    } elseif ($storeType & _UPLOADS_STORE_DB_DATA) {

        // FIXME: <rabbitt> if we happen to be pushing a really big file, this
        //        method of grabbing it from the database and pushing will consume
        //        WAY too much memory. Think of an alternate method
        $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $fileId));
        echo implode('', $data);

        // Headers -can- be sent after the actual data
        // Why do it this way? So we can capture any errors and return if need be :)
        // not that we would have any errors to catch at this point but, mine as well
        // do it incase I think of some errors to catch
        header("Pragma: ");
        header("Cache-Control: ");
        header("Content-type: $fileType");
        header("Content-disposition: attachment; filename=\"$fileName\"");
        if ($fileSize) {
            header("Content-length: $fileSize");
        }

        // TODO: evaluate registering shutdown functions to take care of
        //       ending Xaraya in a safe manner
        $finished = TRUE;
    }

    send out an
    if ($finished) {
        // Let any hooked modules know that we've just pushed a file
        // the hitcount module in particular needs to know to save the fact
        // that we just pushed a file and not display the count
        xarVarSetCached('Hooks.hitcount','save', 1)

        xarModCallHooks('item', 'display', $fileId,
                         array('module'    => 'uploads', 'itemtype'  => 0,
                               'returnurl' => xarModURL('uploads', 'user', 'download', array('fileId' => $fileId))),
                         'uploads');
        // File has been pushed to the client, now shut down.
        exit()
    }

    // Otherwise, there was some error - so let's bring the state
    // back to where it was and display any 'trapped' error

    // make sure we're starting with a fresh and clean buffer space
    while(@ob_end_clean());

    // rebuffer the old page data
    for ($i = 0, $total = count($pageBuffer); $i < $total; $i++) {
        ob_start();
        echo $pageBuffer[$i];
    }
    unset($pageBuffer);

    $msg = xarML('Could not open file [#(1)] for reading', $fileName);
    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READ', new SystemException($msg));
    return FALSE;

}

?>