<?php

function security_admin_ajax_server($args)
{
    ini_set('max_execution_time', 5);
    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }

    if( !xarVarFetch('action', 'str:1:20', $action, null) ){ return false; }

    ob_end_clean();

    $data = '';
    switch( $action )
    {
        case 'getitemtypes':
            if( !xarVarFetch('param_modid', 'int', $modid, null) ){ return false; }
            $info = xarModGetInfo($modid);
            $itemtypes = xarModApiFunc($info['name'], 'user', 'getitemtypes', array(), false);

            if( is_null($itemtypes) ){ $itemtypes = array(); }
            $itemtypes[0] = array('label' => 'None/All');
            ksort($itemtypes);

            $data['itemtypes'] = $itemtypes;
            $data['template']  = 'form-itemtypes';
            $data = xarTplModule('security', 'admin', 'ajax_server', $data);
            break;

        case 'loadsecurity':
            if( !xarVarFetch('param_modid', 'int', $modid, 0) ){ return false; }
            if( !xarVarFetch('param_itemtype', 'int', $itemtype, 0) ){ return false; }
            if( !xarVarFetch('param_itemid', 'int', $itemid, 0) ){ return false; }
            ini_set('include_path',ini_get('include_path').':modules/security/xarclass:modules/security/xarclass/Zend');
            include_once('Json.php');
            $data['security'] = xarModAPIFunc('security', 'user', 'get',
                array(
                    'modid'      => $modid
                    , 'itemtype' => $itemtype
                    , 'itemid'   => $itemid
                )
            );
            $data = Zend_Json::encode($data['security']);

            break;

        case 'savesecurity':
            if( !xarVarFetch('param_security', 'str', $security) ){ return false; }

            ini_set('include_path',ini_get('include_path').':modules/security/xarclass:modules/security/xarclass/Zend');
            include_once('Json.php');
            $security = Zend_Json::decode($security, Zend_Json::TYPE_OBJECT);
            Security::update($security->levels, $security->modid, $security->itemtype, $security->itemid);
            $data = xarML('Save was successful!');

            break;

        case 'getitemids':
            if( !xarVarFetch('param_modid', 'int', $modid, null) ){ return false; }
            if( !xarVarFetch('param_itemtype', 'int', $itemtype, null) ){ return false; }
            $info = xarModGetInfo($modid);
            $itemids = xarModApiFunc('security', 'user', 'getallitemids'
                , array(
                    'modid'      => $modid
                    , 'itemtype' => $itemtype
                )
            );
            //array_unshift($itemids, 'None/All');
            $itemids[0] = 'None/All';
            ksort($itemids);
            $data['itemids']   = $itemids;
            $data['template']  = 'form-itemids';
            $data = xarTplModule('security', 'admin', 'ajax_server', $data);
            break;

        default:
            return '';
    }

    echo $data;
    exit();
    //return $data;
}
?>