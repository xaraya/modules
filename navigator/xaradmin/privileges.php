<?php

/**
 * Manage definition of instances for privileges (unfinished)
 */
function navigator_admin_privileges($args)
{
    if (!xarSecurityCheck('AdminNavigator')) { return; }
    
    extract($args);

    // fixed params
    if (!xarVarFetch('privtype',    'enum:item:menu', $pType,NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('secid',       'int:0:', $secid,        0,    XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('primaryid',   'int:0:', $primaryid,    0,    XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('menuname',    'str:1:', $menuname,     '',   XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('apply',       'isset',  $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',      'isset',  $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',     'isset',  $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',    'isset',  $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',   'isset',  $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent','isset',  $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance', 'isset',  $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',    'isset',  $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $field1 = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $field2 = $parts[1];
    } else {
        $field1 = $field2 = '';
    }
    
    $levels = array(
        array('level' => 0,   
              'selected' => ((   0 == $extlevel) ? TRUE : FALSE),
              'name' => xarML('Inaccessable')),
        array('level' => 200,   
              'selected' => (( 200 == $extlevel) ? TRUE : FALSE),
              'name' => xarML('View'))
    );

    $newinstance    = array();

    switch ($pType) {
        case 'item':
            $primary   = @unserialize(xarModGetVar('navigator', 'categories.list.primary'));
            $matrix    = xarModGetVar('navigator', 'style.matrix') ? TRUE : FALSE;
            
           if (!empty($field1)) {
                $primaryid = $field1;
            } else {
                $primaryid = $primaryid;
            }
            
            if (!empty($field2) && $matrix) {
                $secid = $field2;
            } else {
                $secid = $secid;
            }
            
            if (count($primary)) {
                xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$primary);
                foreach ($primary as $key => $node) {
                    if (0 == $node['indent']) {
                        $primary[$key]['name'] = '* ' . $node['name'];
                    } else {
                        if ($node['parent'] == 1) {
                            $primary[$key]['name'] = str_repeat('&nbsp;', $node['indent'] * 3) . '+ ' . $node['name'];
                        } else {
                            $primary[$key]['name'] = str_repeat('&nbsp;', $node['indent'] * 3) 
                                                   . str_repeat('&nbsp;', $node['indent'] * 3) . $node['name'];
                        }
                    }
                    unset($primary[$key]['npid']);
                    unset($primary[$key]['ncid']);
                    unset($primary[$key]['parent']);
                    unset($primary[$key]['indent']);
                    unset($primary[$key]['pid']);
                }
            } else {
                $primary = array();
            }
            
            if (TRUE == $matrix) {
                $secondary = @unserialize(xarModGetVar('navigator', 'categories.list.secondary'));
                if (count($secondary)) {
                    xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$secondary);
                    foreach ($secondary as $key => $node) {
                        if (0 == $node['indent']) {
                            $secondary[$key]['name'] = '+ ' . $node['name'];
                        } else {
                            if ($node['parent'] == 1) {
                                $secondary[$key]['name'] = str_repeat('&nbsp;', $node['indent'] * 3) . '+ ' . $node['name'];
                            } else {
                                $secondary[$key]['name'] = str_repeat('&nbsp;', $node['indent'] * 3) 
                                                         . str_repeat('&nbsp;', $node['indent'] * 3) . $node['name'];
                            }
                        }
                        unset($secondary[$key]['npid']);
                        unset($secondary[$key]['ncid']);
                        unset($secondary[$key]['parent']);
                        unset($secondary[$key]['indent']);
                        unset($secondary[$key]['pid']);
                    }
                } else {
                    $secondary = array();
                }
            } else {
                $secondary = array();
            }
            
            $data['primarylist']    = $primary;
            $data['primaryid']      = $primaryid;
            $data['secondarylist']  = $secondary;
            $data['secid']          = $secid;
            $data['matrix']         = $matrix;
            $newinstance[]          = empty($data['primaryid']) ? 'All' : $data['primaryid'];
            $newinstance[]          = empty($data['secid'])     ? 'All' : $data['secid'];
            break;
        case 'menu':
            if (!empty($field1)) {
                $menuname = $field1;
            } else {
                $menuname = $menuname;
            }
            $data['menuname']       = $menuname;
            $newinstance[]          = empty($data['menuname']) ? 'All' : $menuname;
            break;
    }

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    
    $data['privtype']       = $pType;
    $data['levels']         = $levels;
    $data['extpid']         = $extpid;
    $data['extname']        = $extname;
    $data['extrealm']       = $extrealm;
    $data['extmodule']      = $extmodule;
    $data['extcomponent']   = $extcomponent;
    $data['extlevel']       = $extlevel;
    $data['extinstance']    = xarVarPrepForDisplay(join(':',$newinstance));
    $data['refreshlabel']   = xarML('Refresh');
    $data['applylabel']     = xarML('Finish and Apply to Privilege');

    return $data;

}

?>