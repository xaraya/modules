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

    $defaultcreate    = xarModGetVar('userpoints', 'defaultcreate');
    $defaultdelete    = xarModGetVar('userpoints', 'defaultdelete');
    $defaultdisplay   = xarModGetVar('userpoints', 'defaultdisplay');
    $defaultupdate    = xarModGetVar('userpoints', 'defaultupdate');
    $defaultfrontpage = xarModGetVar('userpoints', 'defaultfrontpage');

    $data['ranksperlabel'] = xarVarPrepForDisplay(xarML('Ranks Per Page?'));
    $data['rankspervalue'] = xarModGetVar('userpoints', 'ranksperpage');
    $data['showadminlabel'] = xarML('Show Admin Score?');
    $data['showadminchecked'] = xarModGetVar('userpoints', 'showadminscore') ?
    'checked' : '';
    $data['showanonlabel'] = xarML('Show Anonymous Score?');
    $data['showanonchecked'] = xarModGetVar('userpoints', 'showanonscore') ?
    'checked' : '';
	$data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('userpoints', 'SupportShortURLs') ?
    'checked' : '';


    $data['settings'] = array();
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
		                                 'create_score'    => $defaultcreate,
		                                 'delete_score'    => $defaultdelete,
		                                 'display_score'   => $defaultdisplay,
		                                 'update_score'    => $defaultupdate,
		                                 'frontpage_score' => $defaultfrontpage);

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
                    $create_score = xarModGetVar('userpoints', "createpoints.$modname.$itemtype");
                    if (empty($create_score)) {
                        $create_score = $defaultcreate;
                    }
					$delete_score = xarModGetVar('userpoints', "deletepoints.$modname.$itemtype");
                    if (empty($delete_score)) {
                        $delete_score = $defaultdelete;
                    }
					$display_score = xarModGetVar('userpoints', "displaypoints.$modname.$itemtype");
                    if (empty($display_score)) {
                        $display_score = $defaultdisplay;
                    }
					$update_score = xarModGetVar('userpoints', "updatepoints.$modname.$itemtype");
                    if (empty($update_score)) {
                        $update_score = $defaultupdate;
                    }
					$frontpage_score = xarModGetVar('userpoints', "frontpagepoints.$modname.$itemtype");
                    if (empty($frontpage_score)) {
                        $frontpage_score = $defaultfrontpage;
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'create_score'    => $create_score,
						                                            'delete_score'    => $delete_score,
						                                            'display_score'   => $display_score,
						                                            'update_score'    => $update_score,
						                                            'frontpage_score' => $frontpage_score);
                }
            } else {
                    $create_score = xarModGetVar('userpoints', "createpoints.$modname");
                    if (empty($create_score)) {
                        $create_score = $defaultcreate;
                    }
					$delete_score = xarModGetVar('userpoints', "deletepoints.$modname");
                    if (empty($delete_score)) {
                        $delete_score = $defaultdelete;
                    }
					$display_score = xarModGetVar('userpoints', "displaypoints.$modname");
                    if (empty($display_score)) {
                        $display_score = $defaultdisplay;
                    }
					$update_score = xarModGetVar('userpoints', "updatepoints.$modname");
                    if (empty($update_score)) {
                        $update_score = $defaultupdate;
                    }
					$frontpage_score = xarModGetVar('userpoints', "frontpagepoints.$modname");
                    if (empty($frontpage_score)) {
                        $frontpage_score = $defaultfrontpage;
                    }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'create_score'    => $create_score,
						                            'delete_score'    => $delete_score,
						                            'display_score'   => $display_score,
						                            'update_score'    => $update_score,
						                            'frontpage_score' => $frontpage_score);
            }
        }
    }


    $data['authid'] = xarSecGenAuthKey();
    return $data;
} 

?>
