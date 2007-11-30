<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function keywords_userapi_encode_shorturl($args)
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
    $module = 'keywords';
    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';
        if (!empty($tab)) {
            $path .= 'tab' . $tab . '/';
        } elseif (!empty($keyword)) {
			if ( strpos($keyword,'_') === FALSE ) {
			    $keyword = str_replace(' ','_',$keyword);
		    }
            $encodedKey= rawurlencode($keyword);

		    // the URL encoded / (%2F) is not accepted by Apache in PATH_INFO
		    $encodedKey = str_replace('%2F','/',$encodedKey);
            $path .= $encodedKey. '/';
            if (!empty($id)) {
               $path .= $id;
            }
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }
    return $path;
}

?>
