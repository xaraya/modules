<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
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
        throw new Exception($msg);
    }

    if (!isset($altFileName) || !strlen($altFileName)) {
        $altFileName = $fileName;
    }

    // Start off trying mime_content_type
    if (function_exists('mime_content_type') && ini_get('mime_magic.magicfile')) {
        $ftype = mime_content_type($fileName);
        if (isset($ftype) && strlen($ftype)) return $ftype;
    }

    // Try to use if disponible pecl fileinfo extension
    // Note: as of PHP 5.3 this is included in the PHP distribution. Leave the if condition here, doesn't do any harm.
    if(extension_loaded('fileinfo')) {
        $res = finfo_open(FILEINFO_MIME);
        $mime_type = finfo_file($res, $fileName);
        finfo_close($res);
        if (isset($mime_type) && strlen($mime_type)) return $mime_type;
     }

    // If that didn't work, try getimagesize to see if the file is an image
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
            $typeInfo = xarMod::apiFunc('mime', 'user', 'get_extension', array('extensionName' => $extension));
            if (is_array($typeInfo) && count($typeInfo)) {
                $mimeType = xarMod::apiFunc('mime', 'user', 'get_mimetype', array('subtypeId' => $typeInfo['subtypeId']));
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
        throw new Exception($msg);
    } else {
        $mime_list = xarMod::apiFunc('mime', 'user', 'getall_magic');


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
                    throw new Exception($msg);
                    }
                }

                if (!($value = @fread($fp, $magicInfo['length']))) {
                    $msg = xarML('Unable to read (#(1) bytes) from file: [#(2)].',
                                 $magicInfo['length'], $fileName);
                    throw new Exception($msg);
                }

                if ($magicInfo['value'] == base64_encode($value)) {
                    fclose($fp);
                       $mimeType = xarMod::apiFunc('mime', 'user', 'get_mimetype',
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
            $typeInfo = xarMod::apiFunc('mime', 'user', 'get_extension', array('extensionName' => $extension));
            if (is_array($typeInfo) && count($typeInfo)) {
                $mimeType = xarMod::apiFunc('mime', 'user', 'get_mimetype', array('subtypeId' => $typeInfo['subtypeId']));
                return $mimeType;
            }
        }

        if (!rewind($fp)) {
            $msg = xarML('Unable to rewind to beginning of file: [#(1)]', $fileName);
            throw new Exception($msg);
        }

        if (!($value = @fread($fp, 256))) {
            $msg = xarML('Unable to read (256 bytes) from file: [#(1)]', $fileName);
            throw new Exception($msg);
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

        if ($fp) fclose($fp);

        return $mime_type;
    }
}

?>