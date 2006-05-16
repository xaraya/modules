<?php
/**
* Encode Short URL's
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Encode Short URL's
*
* Take input from xarModURL() and create a filesystem-like path for it.
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param  $args the function and arguments passed to xarModURL
* @return  string
* @returns path to be added to index.php for a short URL, or empty if failed
*/
function files_userapi_encode_shorturl($args)
{
    extract($args);

    /**
    * Note: since we use $path routinely in this module,
    * the standard $path variable in this function has been
    * renamed to $uri.
    */

    // Check if we have something to work with
    if (!isset($func)) return;

    // use module alias if set
    $usemodulealias = xarModGetVar('files', 'useModuleAlias');
    $aliasname = xarModGetVar('files', 'aliasname');
    $module = ($usemodulealias) ? $aliasname : 'files';

    // set basic path vars
    $uri = '';
    $join = '?';

    // determine base URL
    if ($func == 'main') {
        $uri = "/$module/list";
    } else {
        $uri = "/$module/$func";
    }

    // add path if we have it
    if (!empty($path)) {
        $uri .= $path;
    }

    // add standard GET arguments if necessary
    if (!empty($uri)) {
        if (isset($itemtype)) {
            $uri .= $join . 'itemtype=' . $itemtype;
            $join = '&';
        }
    }

    return $uri;
}

?>
