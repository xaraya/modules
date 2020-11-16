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
function mime_userapi_extension_to_mime($args)
{
    extract($args);

    if (!isset($fileName) || empty($fileName)) {
        $msg = xarML('Missing fileName parameter!');
        throw new Exception($msg);
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
            $extensionInfo = xarMod::apiFunc(
                'mime',
                'user',
                'get_extension',
                array('extensionName' => $extension)
            );
            if (!empty($extensionInfo)) {
                $mimeType = xarMod::apiFunc(
                    'mime',
                    'user',
                    'get_mimetype',
                    array('subtypeId' => $extensionInfo['subtypeId'])
                );
                if (!empty($mimeType)) {
                    return $mimeType;
                }
            }
        } else {
            return 'application/octet-stream';
        }
    }
}
