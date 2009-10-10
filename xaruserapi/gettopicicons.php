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
 *//**
 * Get a specific item
 *
 * Standard function to return factory default settings
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  string type component to return defaults for (forums|privileges - default forums)
 * @return array
 * @throws none
 */
function crispbb_userapi_gettopicicons($args)
{
    extract ($args);
    $iconlist = array();
    if (!empty($iconfolder)) {
        if (!empty($shownone)) {
            $iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
        }
        $moduleicons = xarMod::apiFunc('crispbb', 'user', 'browse_files',
            array('module' => 'crispbb',
                'basedir' => 'xarimages/'.$iconfolder,
                'match_re' => '/(gif|png|jpg)$/'));
        $themeicons = xarMod::apiFunc('crispbb', 'user', 'browse_files',
            array('basedir' => xarTPLGetThemeDir() . '/modules/crispbb/images/'.$iconfolder,
                'match_re' => '/(gif|png|jpg)$/'));
        if (!empty($moduleicons)) {
            foreach ($moduleicons as $modicon) {
                $iconname =  preg_replace( "/\.\w+$/U", "", $modicon );
                $imagepath = $iconfolder . '/' . $modicon;
                $iconlist[$modicon] = array('id' => $modicon, 'name' => $iconname, 'imagepath' => $imagepath);
            }
        }
        unset($moduleicons);
        if (!empty($themeicons)) {
            foreach ($themeicons as $thmicon) {
                $iconname =  preg_replace( "/\.\w+$/U", "", $thmicon );
                $imagepath = $iconfolder . '/' . $thmicon;
                $iconlist[$thmicon] = array('id' => $thmicon, 'name' => $iconname, 'imagepath' => $imagepath);
            }
        }
        unset($themeicons);
    }

    return $iconlist;
}
?>