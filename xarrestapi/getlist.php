<?php
/**
 * Workflow Module REST API for Galaxia Workflow Engine and Symfony Workflow Component (test)
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
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
function workflow_restapi_getlist($args = [])
{
    $apilist = [];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['findinstances'] = [
        'type' => 'user',  // default = rest, other options are user, admin, ... as usual
        'path' => 'instances',  // path to use in REST API operation /modules/{module}/{path}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Find instances with a certain status, activityId and/or max. started date',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['getinstance'] = [
        'type' => 'user',  // default = rest, other $type options are user, admin, ... as usual
        'path' => 'instances/{instanceId}',  // path to use in REST API operation /modules/{module}/{path} with path parameter
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Retrieve the actual instance rather than just an array of values',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform Instance to $item or not?
        'response' => ['type' => 'object'],  // optional response schema
        // @checkme Galaxia library has issues unserializing Instance without Creole being loaded - set_include_path
        //'caching' => false,
    ];
    // $func name as used in xarMod::apiFunc($module, $type, $func, $args)
    $apilist['test_configs'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'config',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get the list of defined workflows',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'config'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_config'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'config/{workflow}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'config'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_process'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'process/{workflow}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'process'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_processplace'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'process/{workflow}/{subjectId}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'process'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_transition'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'process/{workflow}/{subjectId}/{transition}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'post',  // method to use in REST API operation
        'security' => 'SubmitWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'process'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_tracker'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'tracker/{workflow}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'tracker'],  // optional default args
        'paging' => true,
        'caching' => false,
    ];
    $apilist['test_trackerlist'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'tracker/{workflow}/{subjectId}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'tracker'],  // optional default args
        'paging' => true,
        'caching' => false,
    ];
    $apilist['test_trackeritem'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'tracker/{workflow}/{subjectId}/{trackerId}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'tracker'],  // optional default args
        'caching' => false,
    ];
    $apilist['test_history'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'history/{workflow}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'history'],  // optional default args
        'parameters' => ['subjectId', 'trackerId'],  // optional parameter(s)
        'paging' => true,
        'caching' => false,
    ];
    $apilist['test_subjects'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'subject/{object}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'subject'],  // optional default args
        'caching' => false,
    ];
    // @todo this doesn't match if the previous one already matched! - sort by decreasing path length and count params?
    $apilist['test_subject'] = [
        //'type' => 'rest',  // default = rest, other $type options are user, admin, ... as usual
        'name' => 'test',  // default = $api array key
        'path' => 'subject/{object}/{item}',  // path to use in REST API operation /modules/{module}/{path} with {more}
        'method' => 'get',  // method to use in REST API operation
        'security' => 'ReadWorkflow',  // optional security mask depending on the api
        'description' => 'Get details of selected workflow',
        //'parameters' => ['moduleid'],  // optional parameter(s)
        // @todo transform assoc array("$itemid" => $item) to list of $item or not?
        //'response' => ['type' => 'array', 'items' => ['type' => 'object']],  // optional response schema
        'args' => ['what' => 'subject'],  // optional default args
        'caching' => false,
    ];
    return $apilist;
}
