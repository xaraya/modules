<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 */
function pubsub_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'pubsub'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    $data['templates'] = array();
    $data['templates'][0] = xarML('not supported');

    // get the list of available templates
    $templates = xarMod::apiFunc('pubsub','user','getalltemplates');
    foreach ($templates as $id => $name) {
        $data['templates'][$id] = $name;
    }

    $data['settings'] = array();

    if (!xarVarFetch('phase', 'pre:trim:lower:str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab',   'pre:trim:lower:str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                default:
                	$data['regoptions'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state'=> 3, 'module'=> "pubsub"));
                    // get the list of hooked modules
                    $hookedmodules = xarMod::apiFunc('modules', 'admin', 'gethookedmodules',
                                                   array('hookModName' => 'pubsub'));
                    if (isset($hookedmodules) && is_array($hookedmodules)) {
                        foreach ($hookedmodules as $modname => $value) {
                            // Get the list of all item types for this module (if any)
                            /*
                            $mytypes = xarMod::apiFunc($modname,'user','getitemtypes',
                                                     // don't throw an exception if this function doesn't exist
                                                     array(), 0);
                            // we have hooks for individual item types here
                            if (!isset($value[0])) {
                                foreach ($value as $itemtype => $val) {
                                    $createwithstatus = xarModVars::get('pubsub', "$modname.$itemtype.createwithstatus");
                                    if (empty($createwithstatus)) {
                                        $createwithstatus = 0;
                                    }
                                    $create = xarModVars::get('pubsub', "$modname.$itemtype.create");
                                    if (empty($create)) {
                                        $create = 0;
                                    }
                                    $update = xarModVars::get('pubsub', "$modname.$itemtype.update");
                                    if (empty($update)) {
                                        $update = 0;
                                    }
                                    $delete = xarModVars::get('pubsub', "$modname.$itemtype.delete");
                                    if (empty($delete)) {
                                        $delete = 0;
                                    }
                                    if (isset($mytypes[$itemtype])) {
                                        $type = $mytypes[$itemtype]['label'];
                                        $link = $mytypes[$itemtype]['url'];
                                    } else {
                                        $type = xarML('type #(1)',$itemtype);
                                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                                    }
                                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                                    'createwithstatus' => $createwithstatus,
                                                                                    'create' => $create,
                                                                                    'update' => $update,
                                                                                    'delete' => $delete);
                                }
                            } else {
                                $createwithstatus = xarModVars::get('pubsub', "$modname.createwithstatus");
                                                if (empty($createwithstatus)) {
                                                    $createwithstatus = 0;
                                }
                                $create = xarModVars::get('pubsub', "$modname.create");
                                if (empty($create)) {
                                    $create = 0;
                                }
                                $update = xarModVars::get('pubsub', "$modname.update");
                                if (empty($update)) {
                                    $update = 0;
                                }
                                $delete = xarModVars::get('pubsub', "$modname.delete");
                                if (empty($delete)) {
                                    $delete = 0;
                                }
                                $link = xarModURL($modname,'user','main');
                                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                                    'createwithstatus' => $createwithstatus,
                                                                    'create' => $create,
                                                                    'update' => $update,
                                                                    'delete' => $delete);
                                if (!empty($mytypes) && count($mytypes) > 0) {
                                    foreach ($mytypes as $itemtype => $mytype) {
                                        $createwithstatus = xarModVars::get('pubsub', "$modname.$itemtype.createwithstatus");
                                        if (empty($createwithstatus)) {
                                            $createwithstatus = 0;
                                        }
                                        $create = xarModVars::get('pubsub', "$modname.$itemtype.create");
                                        if (empty($create)) {
                                            $create = 0;
                                        }
                                        $update = xarModVars::get('pubsub', "$modname.$itemtype.update");
                                        if (empty($update)) {
                                            $update = 0;
                                        }
                                        $delete = xarModVars::get('pubsub', "$modname.$itemtype.delete");
                                        if (empty($delete)) {
                                            $delete = 0;
                                        }
                                        $type = $mytypes[$itemtype]['label'];
                                        $link = $mytypes[$itemtype]['url'];
                                        $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                                        'createwithstatus' => $createwithstatus,
                                                                                        'create' => $create,
                                                                                        'update' => $update,
                                                                                        'delete' => $delete);
                                }
                            }
                        }
                            */
                    }
                break;
            }
            break;
        }
        break;
        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        return xarTpl::module('pubsub','admin','modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }
                    
                    // Get parameters
                    xarVarFetch('settings',       'isset',    $settings,      '', XARVAR_DONT_SET);
                    xarVarFetch('subjecttitle',   'checkbox', $subjecttitle,  false, XARVAR_DONT_SET);
                    xarVarFetch('includechildren','checkbox', $includechildren,false, XARVAR_DONT_SET);
                    xarVarFetch('allindigest',    'checkbox', $allindigest,   false, XARVAR_DONT_SET);
                    xarVarFetch('usetemplateids', 'checkbox',  $usetemplateids, false, XARVAR_DONT_SET);
					if (!xarVarFetch('sendnotice_subscription',        'checkbox', $sendnotice_subscription,        false, XARVAR_NOT_REQUIRED)) return;
					if (!xarVarFetch('sendnotice_queue',        'checkbox', $sendnotice_queue,        false, XARVAR_NOT_REQUIRED)) return;
					if (!xarVarFetch('enable_default_template',        'checkbox', $enable_default_template,        false, XARVAR_NOT_REQUIRED)) return;
					if (!xarVarFetch('recognized_events',        'str', $recognized_events,        '', XARVAR_NOT_REQUIRED)) return;
                    
                    if (isset($settings) && is_array($settings)) {
                        foreach ($settings as $name => $value) {
                            xarModVars::set('pubsub', $name, $value);
                        }
                    }
                    /* Bug 4777
                    if (empty($SupportShortURLs)) {
                        xarModVars::set('pubsub','SupportShortURLs',0);
                    } else {
                        xarModVars::set('pubsub','SupportShortURLs',1);
                    }*/
                    xarModVars::set('pubsub', 'subjecttitle',$subjecttitle);
                    xarModVars::set('pubsub', 'includechildren',$includechildren);
                    xarModVars::set('pubsub', 'usetemplateids',$usetemplateids);
                    xarModVars::set('pubsub', 'allindigest',$allindigest);
                    xarModVars::set('pubsub', 'sendnotice_subscription', $sendnotice_subscription);
                    xarModVars::set('pubsub', 'sendnotice_queue', $sendnotice_queue);
                    xarModVars::set('pubsub', 'enable_default_template', $enable_default_template);
                    xarModVars::set('pubsub', 'recognized_events', $recognized_events);

                    if (xarMod::isAvailable('scheduler')) {
                        if (!xarVarFetch('interval', 'str:1', $interval, '', XARVAR_NOT_REQUIRED)) return;
                        // see if we have a scheduler job running to process the pubsub queue
                        $job = xarMod::apiFunc('scheduler','user','get',
                                             array('module' => 'pubsub',
                                                   'type'   => 'admin',
                                                   'func'   => 'processq'));
                        if (empty($job) || empty($job['interval'])) {
                            if (!empty($interval)) {
                                // create a scheduler job
                                xarMod::apiFunc('scheduler','admin','create',
                                              array('module' => 'pubsub',
                                                    'type' => 'admin',
                                                    'func' => 'processq',
                                                    'interval' => $interval));
                            }
                        } elseif (empty($interval)) {
                            // delete the scheduler job
                            xarMod::apiFunc('scheduler','admin','delete',
                                          array('module' => 'pubsub',
                                                'type' => 'admin',
                                                'func' => 'processq'));
                        } elseif ($interval != $job['interval']) {
                            // update the scheduler job
                            xarMod::apiFunc('scheduler','admin','update',
                                          array('module' => 'pubsub',
                                                'type' => 'admin',
                                                'func' => 'processq',
                                                'interval' => $interval));
                        }
                    }

                    xarController::redirect(xarModURL('pubsub', 'admin', 'modifyconfig'));

                    return true;
                    break;
                }
            break;
    }

    // Bug 4777 $data['SupportShortURLs'] = xarModVars::get('pubsub','SupportShortURLs');

    if (xarMod::isAvailable('scheduler')) {
        $data['intervals'] = xarMod::apiFunc('scheduler','user','intervals');
        // see if we have a scheduler job running to process the pubsub queue
        $job = xarMod::apiFunc('scheduler','user','get',
                             array('module' => 'pubsub',
                                   'type' => 'admin',
                                   'func' => 'processq'));
        if (empty($job) || empty($job['interval'])) {
            $data['interval'] = '';
        } else {
            $data['interval'] = $job['interval'];
        }
    } else {
        $data['intervals'] = array();
        $data['interval'] = '';
    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>