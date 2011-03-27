<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Add new forum
 *
 * This is a standard function that is called whenever a user
 * wishes to create a new forum.
 * The user needs at least Add privileges.
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_getsettings($args)
{
    extract($args);

    if (empty($setting) || !is_string($setting)) return;

    $settings = array();

    switch ($setting) {
        case 'fsettings':
            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'fsettings'));
            $string = xarModVars::get('crispbb', 'forumsettings');
            if (!empty($string) && is_string($string)) {
                $settings = unserialize($string);
            }
            if (empty($settings)) {
                $settings = $presets['fsettings'];
            }  else {
                // add in any new settings from defaults
                foreach ($presets['fsettings'] as $k => $v) {
                    if (!isset($settings[$k])) {
                        $settings[$k] = $v;
                    }
                }
                // remove any settings not in defaults
                foreach ($settings as $k => $v) {
                    if (!isset($presets['fsettings'][$k])) {
                        unset($settings[$k]);
                    }
                }
            }

        break;
        case 'fprivileges':
            $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                array('preset' => 'fprivileges'));
            $string = xarModVars::get('crispbb', 'privilegesettings');
            if (!empty($string) && is_string($string)) {
                $settings = unserialize($string);
            }
            if (empty($settings)) {
                $settings = $presets['fprivileges'];
            } else {
                // add in any new settings from defaults
                foreach ($presets['fprivileges'] as $k => $v) {
                    if (!isset($settings[$k])) {
                        $settings[$k] = $v;
                    }
                }
                // remove any settings not in defaults
                foreach ($settings as $k => $v) {
                    if (!isset($presets['fprivileges'][$k])) {
                        unset($settings[$k]);
                    }
                }
            }
        break;
        case 'usettings':
            if (!empty($uid) && is_numeric($uid)) {
                $string = xarModUserVars::get('crispbb', 'usettings', $uid);
            } else {
                $string = xarModVars::get('crispbb', 'usettings');
            }
            if (!empty($string) && is_string($string)) {
                $settings = unserialize($string);
            }
            if (empty($settings)) {
                $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
                    array('preset' => 'usettings'));
                $settings = $presets['usettings'];
            }
        break;
        default:
            if (!empty($uid) && is_numeric($uid)) {
                $value = xarModUserVars::get('crispbb', $setting, $uid);
            } else {
                $value = xarModVars::get('crispbb', $setting);
            }
            $settings[$setting] = $value;
        break;
    }

    return $settings;

}
?>