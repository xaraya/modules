<?php

/**
 * Update the configuration parameters of the module based on data from the modification form
 * 
 * @author mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function xlink_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXLink')) return;

    $data = array();
    $data['settings'] = array();

    $basenames = array();
    $xlink = xarModGetVar('xlink','default');
    if (!empty($xlink)) {
        $list = explode(',',$xlink);
        foreach ($list as $base) {
            $basenames[$base] = 1;
        }
    }
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'xlink' => $xlink);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'xlink'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $xlink = xarModGetVar('xlink', "$modname.$itemtype");
                    if (empty($xlink)) {
                        $xlink = '';
                    } else {
                        $list = explode(',',$xlink);
                        foreach ($list as $base) {
                            $basenames[$base] = 1;
                        }
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'xlink'   => $xlink);
                }
            } else {
                $xlink = xarModGetVar('xlink', $modname);
                if (empty($xlink)) {
                    $xlink = '';
                } else {
                    $list = explode(',',$xlink);
                    foreach ($list as $base) {
                        $basenames[$base] = 1;
                    }
                }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'xlink'   => $xlink);
            }
        }
    }
    $data['isalias'] = array();
    foreach (array_keys($basenames) as $base) {
        if (empty($base)) continue;
        $alias = xarModGetAlias($base);
        if ($alias == 'xlink') {
            $data['isalias'][$base] = 1;
        } else {
            $data['isalias'][$base] = 0;
        }
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
