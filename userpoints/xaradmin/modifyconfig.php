<?php

/**
 * Update the configuration parameters of the module based on data from the modification form
 * 
 * @author Vassilis Stratigakis 
 * @access public 
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function userpoints_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminUserpoints')) return;

    $defaultscore = xarModGetVar('userpoints', 'defaultscore');

    $data['settings'] = array();
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'score' => $defaultscore);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'userpoints'));

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $score = xarModGetVar('userpoints', "points.$modname.$itemtype");
                    if (empty($score)) {
                        $score = $defaultscore;
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'score' => $score);
                }
            } else {
                $score = xarModGetVar('userpoints', 'points.' . $modname);
                if (empty($score)) {
                    $score = $defaultscore;
                } 
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'score' => $score);
            }
        }
    }


    $data['authid'] = xarSecGenAuthKey();
    return $data;
} 

?>
