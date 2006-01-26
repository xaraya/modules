<?php
/**
* Support for Short URLs (user functions)
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the eBulletin module development team
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function ebulletin_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) return;

    // use module alias if set
    $usemodulealias = xarModGetVar('ebulletin', 'useModuleAlias');
    $aliasname = xarModGetVar('ebulletin', 'aliasname');
    $module = ($usemodulealias) ? $aliasname : 'ebulletin';

    // set basic path vars
    $path = ($func == 'main') ? "/$module/" : "/$module/$func/";
    $join = '?';

    // add params according to function name
    if ($func == 'display' && !empty($id)) {
        $path .= "$id/";
        unset($args['id']);
    } elseif ($func == 'displayissue' && !empty($id)) {
        $path .= "$id/";
        unset($args['id']);
        if (!empty($displaytype)) {
            $path .= "$displaytype/";
            unset($args['displaytype']);
        }
    } elseif ($func == 'viewissues' && !empty($pid)) {
        $path .= "$pid/";
        unset($args['pid']);
    }

    // append all other args as GET vars
    unset($args['func']);
    foreach ($args as $key => $value) {
        $path .= "$join$key=$value";
        $join = '&';
    }

    return $path;
}

?>
