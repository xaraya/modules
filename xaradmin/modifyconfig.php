<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * Update the configuration parameters of the module based on data from the modification form
 *
 * @author Jim McDonald
 * @access public
 * @param no $ parameters
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function ratings_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminRatings')) return;

    $defaultstyle = xarModGetVar('ratings', 'defaultstyle');
    $defaultseclevel = xarModGetVar('ratings', 'seclevel');

    $data['settings'] = array();
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'style' => $defaultstyle,
                                         'seclevel' => $defaultseclevel);

    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'ratings'));

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    $style = xarModGetVar('ratings', "style.$modname.$itemtype");
                    if (empty($style)) {
                        $style = $defaultstyle;
                    }
                    $seclevel = xarModGetVar('ratings', "seclevel.$modname.$itemtype");
                    if (empty($seclevel)) {
                        $seclevel = $defaultseclevel;
                    }
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                        $link = $mytypes[$itemtype]['url'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                        $link = xarModURL($modname,'user','view',array('itemtype' => $itemtype));
                    }
                    $data['settings']["$modname.$itemtype"] = array('label' => xarML('Configuration for #(1) module - <a href="#(2)">#(3)</a>', $modname, $link, $type),
                                                                    'style' => $style,
                                                                    'seclevel' => $seclevel);
                }
            } else {
                $style = xarModGetVar('ratings', 'style.' . $modname);
                if (empty($style)) {
                    $style = $defaultstyle;
                }
                $seclevel = xarModGetVar('ratings', 'seclevel.' . $modname);
                if (empty($seclevel)) {
                    $seclevel = $defaultseclevel;
                }
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'style' => $style,
                                                    'seclevel' => $seclevel);
            }
        }
    }

    $data['styleoptions'] = array(
        array('id' => 'percentage', 'name' => xarML('Percentage')),
        array('id' => 'outoffive', 'name' => xarML('Number out of five')),
        array('id' => 'outoffivestars', 'name' => xarML('Stars out of five')),
        array('id' => 'outoften', 'name' => xarML('Number out of ten')),
        array('id' => 'outoftenstars', 'name' => xarML('Stars out of ten')),
        array('id' => 'customised', 'name' => xarML('Customized : see the user-display template')),
        );

    $data['secleveloptions'] = array(
        array('id' => 'low', 'name' => xarML('Low : users can vote multiple times')),
        array('id' => 'medium', 'name' => xarML('Medium : users can vote once per day')),
        array('id' => 'high', 'name' => xarML('High : users must be logged in and can only vote once')),
        );

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
