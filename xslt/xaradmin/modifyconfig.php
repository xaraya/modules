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
function xslt_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXSLT')) return;

    $data = array();
    $data['settings'] = array();

    $xsl = xarModGetVar('xslt','default');
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'xsl'   => $xsl);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'xslt'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            $xsl = xarModGetVar('xslt', $modname);
            if (empty($xsl)) {
                $xsl = '';
            }
            $data['settings'][$modname] = array('label' => xarML('Configuration for #(1) module', $modname),
                                                'xsl'   => $xsl);
        }
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
