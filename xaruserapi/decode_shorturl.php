<?php

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the Example module development team
 * @param $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */

function xarpages_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();
    $func = 'main';

    // Analyse the different parts of the virtual path
    // $params[0] contains the first part after index.php/example

    // Save the module alias and shift it away if necessary.
    $args['module_alias'] = strtolower($params[0]);

    // If the alias is not the module name, then it will be a part of the path.
    // Shift it away only if it is the module name.
    if ($params[0] == 'xarpages') {
        array_shift($params);
    }

    // Look for a root page with the name as the first part of the path.
    if (isset($params[0])) {
        $rootpage = xarMod::apiFunc(
            'xarpages', 'user', 'getpage',
            array('name' => strtolower($params[0]), 'parent' => 0, 'status' => 'ACTIVE,EMPTY', 'key' => 'pid')
        );
    }

    // If no root page matches, and an alias was provided, look for a non-root start page.
    // These are used as short-cuts.
    if (empty($rootpage) && $args['module_alias'] != 'xarpages') {
        $rootpage = xarMod::apiFunc(
            'xarpages', 'user', 'getpage',
            array('name' => strtolower($params[0]), 'status' => 'ACTIVE,EMPTY', 'key' => 'pid')
        );
    }

    // TODO: allow any starting point to be a module alias, and so provide
    // short-cuts to the requested page. For example, the 'about' page could
    // be set as an alias. That page could also be under /site/about, but
    // just 'index.php/about' would work, and would be equivalent to
    // index.php/site/about or index.php/xarpages/site/about

    if (!empty($rootpage)) {
        // The first part of the path matches 

        // Shift away the matched first part of the path.
        array_shift($params);

        // Fetch the complete page tree for the root page.
        $tree = xarMod::apiFunc(
            'xarpages', 'user', 'getpagestree',
            array(
                'left_range' => array($rootpage['left'], $rootpage['right']),
                'dd_flag' => false,
                'key' => 'pid',
                'status' => 'ACTIVE,EMPTY'
            )
        );

        // TODO: Cache the tree away for use in the main module (perhaps getpagestree can go that?).
        // If doing that, then ensure the dd data is retrieved at some point.

        // Walk the page tree, matching as many path components as possible.
        $pid = $rootpage['pid'];
        
        while (isset($params[0]) && isset($tree['child_refs']['names'][$pid]) && array_key_exists(strtolower($params[0]), $tree['child_refs']['names'][$pid])) {
            $params[0] = strtolower($params[0]);
            $pid = $tree['child_refs']['names'][$pid][$params[0]];
            array_shift($params);
        }

        // Unshift the module alias back onto the params.
        // Anything left to match can be passed on to (custom) helper short URL decoders.
        array_unshift($params, $args['module_alias']);

        // We have the page ID.
        $args['pid'] = $pid;

        // Run any further URL decode functions, and merge in the result.
        // The custom decode URL functions are coded the same as normal
        // decode functions, but placed into the 'xardecodeapi' API
        $decode_url = $tree['pages'][$pid]['decode_url'];
        if (!empty($decode_url)) {
            // Attempt to invoke the custom decode URL function, suppressing errors.
            try {
                $args2 = xarMod::apiFunc('xarpages', 'decode', $decode_url, $params);
            } catch (Exception $e) {
                $args2 = array();
            }

            // If any decoding was done, merge in the results.
            if (!empty($args2) && is_array($args2)) {
                foreach($args2 as $key => $value) {
                    $args[$key] = $value;
                }
            }
        }

        return array($func, $args);
    }

    // default: return nothing -> no short URL decoded
    return;
}

?>