<?php
/**
 * FormAntiBot module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage FormAntiBot
 * @link http://xaraya.com/index.php/release/147.html
 * @author Carl P. Corliss
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author Carl P. Corliss
 * @param Array $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function formantibot_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // default path is empty -> no short URL
    $path = '';

    // if we want to add some common arguments as URL parameters below
    $join = '?';

    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'formantibot';

    // specify some short URLs relevant to your module
    if ($func == 'image') {
        $path = '/' . $module . '/secure.jpg';
    }

    return $path;
}

?>
