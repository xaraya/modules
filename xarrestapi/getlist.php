<?php
/**
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Get the list of REST API calls supported by this module (if any)
 *
 * Parameters and requestBody fields can be specified as follows:
 * => ['itemtype', 'itemids']  // list of field names, each defaults to type 'string'
 * => ['itemtype' => 'string', 'itemids' => 'array']  // specify the field type, 'array' defaults to array of 'string'
 * => ['itemtype' => 'string', 'itemids' => ['integer']]  // specify the array items type as 'integer' here
 * => ['itemtype' => ['type' => 'string'], 'itemids' => ['type' => 'array', 'items' => ['type' => 'integer']]]  // rest
 *
 * @return array of info
 */
function xarcachemanager_restapi_getlist($args = [])
{
    $apilist = [];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getcachetypes'] = [
        'type' => 'admin',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'cachetypes',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'AdminXarCache',  // default = false REST APIs are public, if true check for authenticated user
        'description' => 'Call admin api function getcachetypes() in module xarcachemanager',
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getcacheinfo'] = [
        'type' => 'admin',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'cacheinfo/{type}',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'AdminXarCache',  // default = false REST APIs are public, if true check for authenticated user
        'description' => 'Call xarcachemanager api function getcacheinfo() in module blocks',
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getcachekeys'] = [
        'type' => 'admin',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'cachekeys/{type}',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'AdminXarCache',  // default = false REST APIs are public, if true check for authenticated user
        'description' => 'Call xarcachemanager api function getcachekeys() in module blocks',
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getcachelist'] = [
        'type' => 'admin',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'cachelist/{type}',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'AdminXarCache',  // default = false REST APIs are public, if true check for authenticated user
        'description' => 'Call xarcachemanager api function getcachelist() in module blocks',
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getcacheitem'] = [
        'type' => 'admin',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'cacheitem/{type}/{key}/{code}',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'AdminXarCache',  // default = false REST APIs are public, if true check for authenticated user
        'description' => 'Call xarcachemanager api function getcacheitem() in module blocks',
    ];
    return $apilist;
}
