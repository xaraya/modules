<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * Support for short URLs (user functions)
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param  $args the function and arguments passed to xarModURL
 * @return string Path to be added to index.php for a short URL, or empty if failed
 */
function twitter_userapi_encode_shorturl($args)
{
    extract($args);

    if (!isset($func)) {return;}
    $path = array();
    $get = $args;

    // This module name.
    $module = 'twitter';

    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    if (!empty($aliasisset) && !empty($aliasname)) {
        // Check this alias really is a module alias, by mapping
        // it back to its module name.
        $module_for_alias = xarModGetAlias($aliasname);

        if ($module_for_alias == $module) {
            // Yes, we have a valid module alias, so use it
            // now instead of the module name.
            $module = $aliasname;
        }
    }

    $path[] = $module;

    if ($func == 'main') {
        // Consume the 'func' parameter only.
        unset($get['func']);
        if (isset($timeline) && !empty($timeline)) {
          $path[] = $timeline;
          unset($get['timeline']);
        }
    } elseif ($func == 'tweet') {
        $path[] = 'tweet';
        unset($get['func']);
    } else {
        // anything else that you haven't defined a short URL equivalent for
        //  -> don't create a path here
    }

    // Any GET parameters in the args that have not been consumed, will
    // be passed back in the 'get' array, and so will be added to the
    // end of the URL.

    return array('path' => $path, 'get' => $get);
}

?>