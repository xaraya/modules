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
function changelog_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    $data = array();
    $data['settings'] = array();

    $changelog = xarModGetVar('changelog','default');
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'changelog' => $changelog);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'changelog'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            $changelog = xarModGetVar('changelog', $modname);
            if (empty($changelog)) {
                $changelog = '';
            }
            $data['settings'][$modname] = array('label' => xarML('Configuration for #(1) module', $modname),
                                                'changelog'   => $changelog);
        }
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
