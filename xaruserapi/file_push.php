<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Pushes a file to the client browser
 *
 *  Note: on success, the calling GUI function should exit()
 *
 *  @author   Carl P. Corliss
 *  @access   public
 *  @param    string    fileName        The name of the file
 *  @param    string    fileLocation    The full path to the file
 *  @param    string    fileType        The mimetype of the file
 *  @param    long int  fileSize        The size of the file (in bytes)
 *  @return  boolean                   This function will return true upon succes and, returns False and throws an exception otherwise
 *  @throws   BAD_PARAM                 missing or invalid parameter
 *  @throws   UPLOADS_ERR_NO_READ       couldn't read from the specified file
 */

function uploads_userapi_file_push( $args )
{

    extract ( $args );

    if (!isset($fileName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileName','file_push','uploads');
        throw new Exception($msg);             
    }

    if (!isset($fileLocation)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileLocation','file_push','uploads');
        throw new Exception($msg);             
    }

    if (!isset($fileType)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileType','file_push','uploads');
        throw new Exception($msg);             
    }

    if (!isset($storeType)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'storeType','file_push','uploads');
        throw new Exception($msg);             
    } elseif ($storeType & _UPLOADS_STORE_DB_DATA) {
        if (!isset($fileId)) {
            $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                        'fileId','file_push','uploads');
        throw new Exception($msg);             
        }
    }

    if (!isset($fileSize)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)]',
                     'fileSize','file_push','uploads');
        throw new Exception($msg);             
    }
    // Close the buffer, saving it's current contents for possible future use
    // then restart the buffer to store the file
    $finished = FALSE;

    $pageBuffer = xarModAPIFunc('uploads', 'user', 'flush_page_buffer');


    if ($storeType & _UPLOADS_STORE_FILESYSTEM || ($storeType == _UPLOADS_STORE_DB_ENTRY)) {

        // Start buffering for the file
        ob_start();

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

        // Start buffering for the file
        ob_start();

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

    if ($finished) {
        return TRUE;
    }

    // rebuffer the old page data
    for ($i = 0, $total = count($pageBuffer); $i < $total; $i++) {
        ob_start();
        echo $pageBuffer[$i];
    }
    unset($pageBuffer);

    $msg = xarML('Could not open file [#(1)] for reading', $fileName);
    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UPLOADS_ERR_NO_READ', new SystemException($msg));
    return FALSE;

}

?>
