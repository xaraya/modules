<?php

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function images_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // if we don't have a fileId, can't do too much
    if (!isset($fileId) || empty($fileId)) {
        return;
    } else {
        $image = end(xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId)));

        if (empty($image)) {
            // fileId is nonexistant...
            return;
        }

        $type = explode('/', $image['fileType']);

        if ($type == 'jpeg')
            $type = 'jpg';

        $fileName = $fileId . '.' . $type[1];
    }

    // clean the array of the items we already have
    // so we can add any other values to the end of the url
    unset($args['func']);
    unset($args['fileId']);

    if (!empty($args)) {

        foreach ($args as $name => $value) {
            $extra[] = "$name=$value";
        }

        $extras = '?' . implode('&', $extra);
    }

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'images';

    // specify some short URLs relevant to your module
    if ($func == 'display') {
        // check for required parameters
        if (!empty($fileId) && is_numeric($fileId)) {
            $path = '/' . $module . '/' . $fileName . (isset($extras) ? $extras : '');
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }

    return $path;
}

?>