<?php

/**
 * Uses variants of the UNIX "file" command to attempt to determine the
 * MIME type of an unknown file.
 *
 * (note: based off of the Magic class in Horde <www.horde.org>)
 *
 * @param string $fileName  The path to the file to analyze.
 *
 * @return string  returns the mime type of the file, or FALSE on error. If it
 *                 can't figure out the type based on the magic entries
 *                 it will try to guess one of either text/plain or 
 *                 application/octet-stream by reading the first 256 bytes of the file
 */
function mime_userapi_analyze_file( $args )
{
    extract($args);
    
    if (!isset($fileName)) {
        $msg = xarML('Unable to retrieve mime type. No filename supplied!');
        xarExeptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!($fp = @fopen($fileName, 'rb'))) {
        $msg = xarML('Unable to analyze file [#(1)]. Cannot open for reading!', $fileName);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_OPEN', new SystemException($msg));
        return FALSE;
    } else {
        $fileSize = filesize($fileName);
        
        include ("modules/mime/xarincludes/mime.magic.php");
        
        foreach($mime_list as $mime_type => $mime_info) {
            
            // if this mime_type doesn't have a 
            // magic string to check against, then
            // go ahead and skip to the next one
            if (!isset($mime_info['needles'])) {
                continue;
            }
            
            foreach ($mime_info['needles'] as $needle => $needle_info) {
                // if the offset is beyond the range of the file
                // continue on to the next item
                if ($needle_info['offset'] >= $fileSize) {
                    continue;
                }

                if ($needle_info['offset'] >= 0) {
                    if (@fseek($fp, $needle_info['offset'], SEEK_SET)) {
                        $msg = xarML('Unable to seek to offset [#(1)] within file: [#(2)]' ,
                                      $needle_info['offset'], $fileName);
                        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_SEEK', new SystemException($msg));
                        return FALSE;
                    }
                }
               
                if (!($value = @fread($fp, $needle_info['length']))) {
                    $msg = xarML('Unable to read (#(1) bytes) from file: [#(2)].' , 
                                 $needle_info['length'], $fileName);
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
                    return FALSE;
                }

                if ($needle == $value) {
                    fclose($fp);
                    return $mime_type;
                }
            }
        } 
        if (!rewind($fp)) {
            $msg = xarML('Unable to rewind to beginning of file: [#(1)]', $fileName);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_REWIND', new SystemException($msg));
            return FALSE;
        }

        if (!($value = @fread($fp, 256))) {
            $msg = xarML('Unable to read (256 bytes) from file: [#(1)]', $fileName);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
            return FALSE;
        }
        
        // get rid of printable characters so we can 
        // use ctype_print to check for printable characters
        // which, in a binary file, there should'nt be any
        $value = str_replace(array("\n","\r","\t"), '', $value);

        if (ctype_print($value)) {
            $mime_type = 'text/plain';
        } else {
            $mime_type = 'application/octet-stream';
        }

        if ($fp) 
            fclose($fp);
    
        return $mime_type;
    }
}

?>