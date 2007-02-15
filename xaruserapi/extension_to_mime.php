<?php
/**
 * Mime Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
 */
/**
 * Tries to guess the mime type based on the file fileName.
 * If it is unable to do so, it returns FALSE. If there is an error,
 * FALSE is returned along with an exception.
 *
 * Based on the Magic class for horde (www.horde.org)
 *
 * @access public
 * @author Carl P. Corliss
 * @param string $fileName  Filename to grab fileName and check for mimetype for..
 *
 * @return string||boolean  mime-type or FALSE with exception on error, FALSE and no exception if unknown mime-type
 */
function mime_userapi_extension_to_mime( $args )
{

    extract($args);

    if (!isset($fileName) || empty($fileName)) {
        $msg = xarML('Missing fileName parameter!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    if (empty($fileName)) {
        return 'application/octet-stream';
    } else {

        $fileName = strtolower($fileName);
        $parts = explode('.', $fileName);

        // if there is only one part, then there was no '.'
        // seperator, hence no extension. So we fallback
        // to analyze_file()
        if (count($parts) > 1) {

            $extension = $parts[count($parts) - 1];
            $extensionInfo = xarModAPIFunc('mime', 'user', 'get_extension',
                                            array('extensionName' => $extension));
            if (!empty($extensionInfo)) {
                $mimeType = xarModAPIFunc('mime', 'user', 'get_mimetype',
                                           array('subtypeId' => $extensionInfo['subtypeId']));
                if (!empty($mimeType)) {
                    return $mimeType;
                }
            }
        } else {
            return 'application/octet-stream';
        }
    }
}

?>
