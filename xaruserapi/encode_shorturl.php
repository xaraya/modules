<?php
/**
 * Encode module parameters for Short URL support
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/*
 * Support for short URLs (user functions)
 *
 * The following two functions encode module parameters into some
 * virtual path that will be added to index.php, and decode a virtual
 * path back to the original module parameters.
 *
 * The result is that people (and search engines) can use URLs like :
 *
 * - http://mysite.com/index.php/itsp/ (main function)
 * - http://mysite.com/index.php/itsp/list.html (view function)
 * - http://mysite.com/index.php/itsp/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=itsp&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/itsp/list.html?startnum=21
 *
 *
 * Module developers who wish to support this feature are strongly
 * recommended to create virtual paths that are 'semantically meaningful',
 * so that people navigating in your module can understand at a glance what
 * the short URLs mean, and how they could e.g. display item 234 simply
 * by changing the 123.html into 234.html.
 *
 * For older modules with many different optional parameters and functions,
 * this generally implies re-thinking which parameters could easily be set
 * to some default to cover the most frequently-used cases, and rethinking
 * how each function could be represented inside some "virtual directory
 * structure". E.g. .../archive/2002/05/, .../forums/12/345.html, ../recent.html
 * or .../<categoryname>/123.html
 *
 * The same kind of encoding/decoding can be done for admin functions as well,
 * except that by default, the URLs will start with index.php/admin/itsp.
 * The encode/decode functions for admin functions are in xaradminapi.php.
 *
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the ITSP module development team
 * @param  $args the function and arguments passed to xarModURL
 * @return array path to be added to index.php for a short URL, or empty if failed
 */
function itsp_userapi_encode_shorturl($args)
{
    // Get arguments from argument array.
    extract($args);

    // Check if we have something to work with.
    // Returning without a value at any point will result in a 'long' URL
    // being generated, i.e. a URL consisting entirely of GET paramaters.
    if (!isset($func)) {return;}

    // The components of the path.
    // On return, we can pass back two arrays: the 'path' part of the URL
    // and the 'GET' part of the URL.
    // In generating the path-part, we will consume the args passed in.
    // We may even generate further get paramaters.
    $path = array();
    $get = $args;

    // This module name.
    $module = 'itsp';

    // Check if we have a module alias set.
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    // It should be noted that most module aliases are not set in this way.
    // A module can have many aliases, and these can be linked to individual
    // datasets within the module, and so are set dynamically here, according
    // to the args passed in, rather than via module variables (as it is here).

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

    // The first part of the URL must be either the module name or one of its alias.
    // Store the module or alias in the first part of the path.
    $path[] = $module;

    // Specify some short URLs relevant to your module.
    // If you have a module alias make provision for it.
    // The following code should be changed to suit and
    // demonstrates overtly how alias name is added instead
    // of module.

    if ($func == 'main') {
        // Consume the 'func' parameter only.
        unset($get['func']);
    } elseif ($func == 'view') {
        $path[] = 'list';
        unset($get['func']);
    } elseif ($func == 'display') {
        // check for required parameters
        if (isset($planid) && is_numeric($planid)) {
            unset($get['func']);

            // Add the planid to the path, then consume it.
            $path[] = 'display';
            $path[] = $planid;
            unset($get['planid']);

            // You might have some additional parameter that you want to use to
            // create different virtual paths here - for example a category name
            // See above for an example...
        } else {
            // we don't know how to handle that -> don't create a path here
            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            // $path = array($module, 'list')
        }
    } elseif ($func == 'itsp') {
        // check for required parameters
        if (isset($itspid) && is_numeric($itspid)) {
            unset($get['func']);

            // Add the itspid to the path, then consume it.
            $path[] = $itspid;
            unset($get['itspid']);

            // You might have some additional parameter that you want to use to
            // create different virtual paths here - for example a category name
            // See above for an example...
        } else {
           // $path[] = 'itsp';
            // we don't know how to handle that -> don't create a path here
            // Note : this generally means that someone tried to create a
            // link to your module, but used invalid parameters for xarModURL
            // -> you might want to provide a default path to return to
            // $path = array($module, 'list')
        }
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