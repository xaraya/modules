<?php
/**
 * return the path for a short URL to xarModURL for this module
 * 
 * @author the Example module development team 
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function headlines_userapi_encode_shorturl($args)
{ 
    // Get arguments from argument array
    extract($args); 
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    } 
    // Note : make sure you don't pass the following variables as arguments in
    // your module too - adapt here if necessary
    // default path is empty -> no short URL
    $path = ''; 
    // if we want to add some common arguments as URL parameters below
    $join = '?'; 
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'headlines'; 
    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/'; 

    } elseif ($func == 'view') {
        // check for required parameters
        if (isset($hid) && is_numeric($hid)) {
            $path = '/' . $module . '/' . $hid; 
        }

    }
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&amp;';
        } 
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&amp;';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            } 
            $path .= $join . 'catid=' . $catid;
            $join = '&amp;';
        } 
    } 

    return $path;
} 

?>