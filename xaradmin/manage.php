<?php

function xorba_admin_manage($args)
{
    $data = array();
    xarVarFetch('connect','str:7:7',$connect,null, XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_server','id:',$data['xorba_server'],null, XARVAR_NOT_REQUIRED);
    
    // Connect request received?
    if(isset($connect))
        // Check if its different from what we may have remembered earlier
        if(xarSessionGetVar('xorba.lastserver') != $data['xorba_server'])
            xarSessionSetVar('xorba.lastserver',$data['xorba_server']);
    $data['xorba_server'] = xarSessionGetVar('xorba.lastserver');
    
    // Retrieve info about this server
    $objectInfo = xarModApiFunc('dynamicdata','user','getobjectinfo',array(
        'name' => 'xorba_servers'
    ));
    $serverInfo = xarModApiFunc('dynamicdata','user','getitem', array(
        'itemid'   => $data['xorba_server'],
        'itemtype' => $objectInfo['itemtype'],
        'modid'    => $objectInfo['moduleid']
    ));
    
    // Get an authenticated client connection to that server
    $client = xarModApiFunc('xorba','admin','getclient',array(
        'xorba_server'  => $serverInfo['name'],
        'xorba_port'    => $serverInfo['port'],
        'xorba_user'    => $serverInfo['user'],
        'xorba_pass'    => $serverInfo['pass']
    ));
    
    // Start collecting the rules information
    $rules = $client->getObject('rules');
    $identities = $rules->get('identity');

    $data['hostrules'] = array(); $data['objectrules'] = array();
    foreach($identities as $ident) 
    {
        $data['identities'][] = array('id' => $ident['identity'], 'name' => $ident['identity']);
        $hostRules = $rules->get('host',$ident['identity']);
        foreach($hostRules as $index => $hostRule)
        {
            $data['hostrules'][] = $hostRule;
        }
        $objectRules = $rules->get('object',$ident['identity']);
        foreach($objectRules as $index => $objectRule)
        {
            $data['objectrules'][] = $objectRule;
        }
    }
    
    $server = $client->getObject('server');
    $data['objects'] = array('id' => '*', 'name' => '*');
    foreach($server->listObjects() as $object)
    {
        $data['objects'][] = array('id' => $object, 'name' => $object);
    }
    
    $client->disconnect();
    
    // Set remaining data for the template
    $data['filter_objects'] =& $data['objects'];
    $data['filter_identities'] = $data['identities'];
    array_unshift($data['filter_identities'],array('id' => '*', 'name' => '*'));
    $data['filter_hosts'] = array();//$data['hosts'];
    $data['objectinfo'] = $objectInfo;
    return $data;
}
?>