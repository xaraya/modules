<?php
/**
 * Mime Module
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
 */
/**
 * Uses variants of the UNIX "file" command to attempt to determine the
 * MIME type of an unknown file.
 *
 * (note: based off of the Magic class in Horde <www.horde.org>)
 *
 * @param string $fileName      The path to the file to analyze.
 * @param string $altFileName   Alternate file name to analyze extension (Optional).
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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (!isset($altFileName) || !strlen($altFileName)) {
        $altFileName = $fileName;
    }

    // Start off with admin selected test method
    // Path to magic file without 'mime' extension is for windows, see http://pecl.php.net/bugs/bug.php?id=7555
    $testMethod = xarModGetVar('mime', 'mimemethod');
    $pathToMagic = xarModGetVar('mime', 'mimepath');
    if ($testMethod == 'none') { 
        //rely on php extension 'fileinfo' and xarayas own tests
    } elseif ($testMethod == 'file_get_contents') {
        // PHP5 approach from http://www.jellyandcustard.com/2006/01/19/php-mime-types-and-fileinfo/
        $fi = new finfo(FILEINFO_MIME, $pathToMagic);
        $mime_type = $fi->buffer(file_get_contents($fileName));
        if (isset($mime_type) && strlen($mime_type)) {
            return $mime_type;
        }
    } else {    
        if (function_exists('mime_content_type') && ini_get('mime_magic.magicfile')) {
            $ftype = mime_content_type($fileName);
            if (isset($ftype) && strlen($ftype)) {
                return $ftype;
            }
        }
    }

    //try to use if disponible pecl fileinfo extension
    if(extension_loaded('fileinfo')) {
        $res = finfo_open(FILEINFO_MIME, $pathToMagic);
        $mime_type = finfo_file($res, $fileName);
        finfo_close($res);
        if (isset($mime_type) && strlen($mime_type)) {
            return $mime_type;
        }
     }

    // if that didn't work, try getimagesize to see if the file is an image
    $fileInfo = @getimagesize($fileName);
    if (is_array($fileInfo) && isset($fileInfo['mime'])) {
        return $fileInfo['mime'];
    }

    // Otherwise, see if the file is empty and, if so
    // return it as octet-stream
    $fileSize = filesize($fileName);
    if (!$fileSize) {
        $parts = explode('.', $altFileName);
        if (is_array($parts) && count($parts)) {
            $extension = basename(end($parts));
            $typeInfo = xarModAPIFunc('mime', 'user', 'get_extension', array('extensionName' => $extension));
            if (is_array($typeInfo) && count($typeInfo)) {
                $mimeType = xarModAPIFunc('mime', 'user', 'get_mimetype', array('subtypeId' => $typeInfo['subtypeId']));
                return $mimeType;
            } else {
                return 'application/octet-stream';
            }
        } else {
            return 'application/octet-stream';
        }
    }
    // Otherwise, actually test the contents of the file
    if (!($fp = @fopen($fileName, 'rb'))) {
        $msg = xarML('Unable to analyze file [#(1)]. Cannot open for reading!', $fileName);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_OPEN', new SystemException($msg));
        return FALSE;
    } else {
        $mime_list = xarModAPIFunc('mime', 'user', 'getall_magic');


        foreach($mime_list as $mime_type => $mime_info) {

            // if this mime_type doesn't have a
            // magic string to check against, then
            // go ahead and skip to the next one
            if (!isset($mime_info['needles'])) {
                continue;
            }

            foreach ($mime_info as $magicInfo) {
                // if the offset is beyond the range of the file
                // continue on to the next item
                if ($magicInfo['offset'] >= $fileSize) {
                    continue;
                }

                if ($magicInfo['offset'] >= 0) {
                    if (@fseek($fp, $magicInfo['offset'], SEEK_SET)) {
                        $msg = xarML('Unable to seek to offset [#(1)] within file: [#(2)]',
                                      $magicInfo['offset'], $fileName);
                        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_SEEK', new SystemException($msg));
                        return FALSE;
                    }
                }

                if (!($value = @fread($fp, $magicInfo['length']))) {
                    $msg = xarML('Unable to read (#(1) bytes) from file: [#(2)].',
                                 $magicInfo['length'], $fileName);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
                    return FALSE;
                }

                if ($magicInfo['value'] == base64_encode($value)) {
                    fclose($fp);
                       $mimeType = xarModAPIFunc('mime', 'user', 'get_mimetype',
                                                  array('subtypeId' => $magicInfo['subtypeId']));
                    if (!empty($mimeType)) {
                        return $mimeType;
                    }
                }
            }
        }

        $parts = explode('.', $altFileName);
        if (is_array($parts) && count($parts)) {
            $extension = basename(end($parts));
            $typeInfo = xarModAPIFunc('mime', 'user', 'get_extension', array('extensionName' => $extension));
            if (is_array($typeInfo) && count($typeInfo)) {
                $mimeType = xarModAPIFunc('mime', 'user', 'get_mimetype', array('subtypeId' => $typeInfo['subtypeId']));
                return $mimeType;
            }
        }

        if (!rewind($fp)) {
            $msg = xarML('Unable to rewind to beginning of file: [#(1)]', $fileName);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_REWIND', new SystemException($msg));
            return FALSE;
        }

        if (!($value = @fread($fp, 256))) {
            $msg = xarML('Unable to read (256 bytes) from file: [#(1)]', $fileName);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FILE_NO_READ', new SystemException($msg));
            return FALSE;
        }

        // get rid of printable characters so we can
        // use ctype_print to check for printable characters
        // which, in a binary file, there shouldn't be any
        $value = str_replace(array("\n","\r","\t"), '', $value);

        // if there are non-printable characters,
        // then the file is of application/octet-stream
        // Note that we use preg_match here to search for non-printable
        // characters - it's a "PHP Version Safe" work around for ctype_* problems.
        if (preg_match('/[^[:print:]]/', $value)) {
            $mime_type = 'application/octet-stream';
        } else {
            $mime_type = 'text/plain';
        }

        if ($fp)
            fclose($fp);

        return $mime_type;
    }
}

?>