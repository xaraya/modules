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
function keywords_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();
    $data['settings'] = array();

    $keywords = xarModGetVar('keywords','default');
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'keywords' => $keywords);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'keywords'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    //$keywords = xarModGetVar('keywords', "$modname.$itemtype");
                    if (empty($keywords)) {
                        $keywords = '';
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'keywords'   => $keywords);
                }
            } else {
                //$keywords = xarModGetVar('keywords', $modname);
                if (empty($keywords)) {
                    $keywords = '';
                }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'keywords'   => $keywords);
            }
        }
    }
    $data['isalias'] = xarModGetVar('keywords','SupportShortURLs');
    $data['restricted'] = xarModGetVar('keywords','restricted');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
