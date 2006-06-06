<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
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
                                         'changelog' => $changelog,
                                         'includedd' => 0);
    $withdd = xarModGetVar('changelog','withdd');
    if (empty($withdd)) {
        $withdd = '';
    }
    $withdd = explode(';',$withdd);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'changelog'));
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $changelog = xarModGetVar('changelog', "$modname.$itemtype");
                    if (empty($changelog)) {
                        $changelog = '';
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    if (xarModIsHooked('dynamicdata',$modname,$itemtype)) {
                        if (!empty($withdd) && in_array("$modname.$itemtype",$withdd)) {
                            $includedd = 2;
                        } else {
                            $includedd = 1;
                        }
                    } else {
                        $includedd = 0;
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'changelog' => $changelog,
                                                                    'includedd' => $includedd);
                }
            } else {
                $changelog = xarModGetVar('changelog', $modname);
                if (empty($changelog)) {
                    $changelog = '';
                }
                if (xarModIsHooked('dynamicdata',$modname)) {
                    if (!empty($withdd) && in_array($modname,$withdd)) {
                        $includedd = 2;
                    } else {
                        $includedd = 1;
                    }
                } else {
                    $includedd = 0;
                }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'changelog' => $changelog,
                                                    'includedd' => $includedd);
            }
        }
    }

    $data['numstats'] = xarModGetVar('changelog','numstats');
    if (empty($data['numstats'])) {
        $data['numstats'] = 100;
    }
    $data['showtitle'] = xarModGetVar('changelog','showtitle');
    if (!empty($data['showtitle'])) {
        $data['showtitle'] = 1;
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
