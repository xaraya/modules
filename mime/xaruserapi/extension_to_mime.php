<?php

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
function mime_userapi_extension_to_mime( $args ) {

    extract($args);

    if (!isset($fileName) || empty($fileName)) {
        $msg = xarML('Missing fileName parameter!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
            echo "<br />Calling array_search_r with extension: [$extension]"; 
            $type = xarModAPIFunc('mime','user','array_search_r',
                                   array('needle'   => $extension,
                                         'haystack' => unserialize(xarModGetVar('mime','mime.magic'))));
        } 
        
        if (count($parts) <= 1 || (!$type || !is_array($type))) {
            return xarModAPIFunc('mime','user','analyze_file',
                                  array('fileName' => $fileName));
        } else {
            return $type[0];
        }
    }
}

?>
