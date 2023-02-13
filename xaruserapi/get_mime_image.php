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
 * Retrieves the name of the image file to use for a given mimetype.
 * If no image file exists for the given mimtype, the unknown image file
 * will be used.
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   string mimeType     The mime type to find a correlating image for
 * @param   string fileSuffix   Image file suffix list (default: '.png')
 * @param   string defaultBase  Default file base name (default: 'default')
 * @returns string
 */

function mime_userapi_get_mime_image( $args )
{
    extract($args);

    if (!isset($mimeType)) {
        // API location handled centrally.
        $msg = xarML('Missing parameter [#(1)].', 'mimeType');
        throw new Exception($msg);
    }

    // Defaults.
    if (empty($fileSuffix)) {$fileSuffix = '.png';}
    if (empty($defaultBase)) {$defaultBase = 'default';}

    // Explode the list of suffixes.
    // A list of suffixes to try can be given, e.g. '-8x8.gif|.png'
    $fileSuffixes = explode('|', $fileSuffix);

    $mimeType = explode('/', $mimeType);
    if (count($mimeType) != 2) {
        $imageFile = $defaultBase . $fileSuffix;
    }

    // Try the complete mimetype-subtype image.
    foreach($fileSuffixes as $fileSuffix) {
        $imageFile = $mimeType[0] . '-' . $mimeType[1] . $fileSuffix;
        if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {break;}
    }

    // Otherwise, try the top level mimetype image.
    if ($imageURI == NULL) {
        foreach($fileSuffixes as $fileSuffix) {
            $imageFile = $mimeType[0] . $fileSuffix;
            if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {break;}
        }

        if ($imageURI == NULL) {
            foreach($fileSuffixes as $fileSuffix) {
                $imageFile = $defaultBase . $fileSuffix;
                if ($imageURI = xarTpl::getImage($imageFile, 'mime')) {break;}
            }
        }
    }

    // Single point of return.
    // We also have 'imageFile' set, which could be a useful (alternative) return value.
    return $imageURI;
}

?>
