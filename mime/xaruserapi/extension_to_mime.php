<?php

/**
 * Tries to guess the mime type based on the file extension. 
 * If it is unable to do so, it returns FALSE. If there is an error,
 * FALSE is returned along with an exception.
 *
 * Based on the Magic class for horde (www.horde.org)
 *
 * @access public
 * @author Carl P. Corliss
 * @param string $extension  Extension that needs a MIME type mapping.
 *
 * @return string||boolean  mime-type or FALSE with exception on error, FALSE and no exception if unknown mime-type
 */
function mime_userapi_extension_to_mime( $args ) {

    extract($args);

    if (!isset($extension)) {
        $msg = xarML('Missing extension parameter!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    } 

    if (empty($extension)) {
        return 'application/octet-stream';
    } else {
        $extension = strtolower($extension);

        $type = xarModAPIFunc('mime','user','array_search_r',
                              array('needle'   => $extension,
                                    'haystack' => xarModGetVar('mime','mime.magic')));

        if (FALSE !== $type && is_array($type)) {
            return $type[0];
        } else {
            return FALSE;
        }
    }
}

?>
