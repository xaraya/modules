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
 * Attempt to convert a MIME type to a file extension.
 * If we cannot map the type to a file extension, we return false.
 *
 * Code originally based on hordes Magic class (www.horde.org)
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   string      $mime_type MIME type to be mapped to a file extension.
 * @return  string      The file extension of the MIME type.
 */
function mime_userapi_mime_to_extension( $args )
{

    extract($args);

    if (!isset($mime_type) || empty($mime_type)) {
        $msg = xarML('Missing \'mime_type\' parameter!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $mime_list = unserialize(xarModGetVar('mime','mime.magic'));

    if (isset($mime_list[$mime_type])) {
        if (isset($mime_list[$mime_type]['extension_list']) && is_array($mime_list[$mime_type]['extension_list'])) {
            // return the first extension we find for this mime_type
            return $mime_list[$mime_type]['extension_list'][0];
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

?>
