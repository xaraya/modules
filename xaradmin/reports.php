<?php

/**
 * view reports
 */
function opentracker_admin_reports($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminOpentracker')) return;

    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($method)) {
        // get a dynamic object interface (cfr. object module)
        $interface = xarModAPIFunc('dynamicdata','user','interface',
                                   array(// the main templates for the GUI are in this module
                                         'urlmodule' => 'opentracker',
                                         // specify some number of items if you like
                                         //'numitems' => xarModGetVar('opentracker','itemsperpage'),
                                         // specify some default object here
                                         'object' => 'pot_reports',

                                         // TODO: the following GUI functions are available in code
                                         //'functions' => array(),
                                         // the object templates are in some other module
                                         //'tplmodule' => 'dynamicdata',
                                         // use a different sub-class for the dynamic object [interface]
                                         //'classname' => 'My_Object_Interface', // or 'My_Object'
                                        ));

        // let the interface handle the rest
        return $interface->handle($args);
    }

    // find the dynamic object containing the report definitions
    $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                          array('name' => 'pot_reports'));
    if (empty($info) || empty($info['objectid'])) {
        // try to import the report definitions
        $objectid = xarModAPIFunc('dynamicdata','util','import',
                                  array('file' => 'modules/opentracker/pot_reports.xml'));
        if (empty($objectid)) return;
        xarResponseRedirect(xarModURL('opentracker', 'admin', 'reports'));
        return true;
    }

    $data = array();
    $data['reports'] = xarModAPIFunc('dynamicdata','user','getitems',
                                     array('moduleid' => $info['moduleid'],
                                           'itemtype' => $info['itemtype']));

    if (!xarVarFetch('itemid', 'id', $itemid, 0, XARVAR_NOT_REQUIRED)) return;

    if (!empty($itemid) && !empty($data['reports']) && !empty($data['reports'][$itemid])) {
        $data['itemid'] = $itemid;
        $params = $data['reports'][$itemid];
        switch ($params['api_call']) {
            case 'all_paths':
            case 'top_paths':
            case 'longest_paths':
            case 'shortest_paths':
            case 'individual_clickpath':
            case 'num_visitors_online':
            case 'page_impressions':
            case 'returning_visitors':
            case 'search_engines':
            case 'top':
            case 'visitors':
            case 'visits':
            case 'visitors_online':
            case 'xarmod_top':
                $apitype = 'get';
                break;
            case 'plot_access_statistics':
            case 'plot_top':
                $apitype = 'plot';
                $params['api_call'] = preg_replace('/^plot_/','',$params['api_call']);
                break;
        }
        $args['api_call'] = $params['api_call'];
        $args['result_format'] = $params['result_format'];
        $extra = unserialize($params['extra']);
        foreach ($extra as $key => $value) {
            if (empty($value)) continue;
            $args[$key] = $value;
        }
        require_once('modules/opentracker/xarOpenTracker.php');
        $result = xarOpenTracker::$apitype($args);
        if (!isset($result)) {
            // plot output
            exit();
        } elseif (is_array($result)) {
            // table output
            $data['result'] = $result;
        } elseif (is_object($result)) {
            switch (get_class($result)) {
                case 'image_graphviz':
                    // graphviz output
                    $result->image('png');
                    exit();
                    break;
                default:
                    break;
            }
        }
    }

    return $data;
} 

?>
