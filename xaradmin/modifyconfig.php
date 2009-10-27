<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
function headlines_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminHeadlines')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['shorturlslabel']     = xarML('Enable short URLs?');
    $data['shorturlschecked']   = xarModVars::get('headlines', 'SupportShortURLs') ? true : false;
    $data['modulealias'] = xarModVars::get('headlines', 'useModuleAlias');
    $data['aliasname'] = xarModVars::get('headlines', 'aliasname');
    $data['showcomments'] = xarModVars::get('headlines', 'showcomments');
    if (!xarMod::isAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
        $data['showcomments'] = 0;
    }
    $data['showratings'] = xarModVars::get('headlines', 'showratings');
    if (!xarMod::isAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
        $data['showratings'] = 0;
    }
    $data['showhitcount'] = xarModVars::get('headlines', 'showhitcount');
    if (!xarMod::isAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
        $data['showhitcount'] = 0;
    }
    $data['showkeywords'] = xarModVars::get('headlines', 'showkeywords');
    if (!xarMod::isAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
        $data['showkeywords'] = 0;
    }
    $data['maxdescription'] = xarModVars::get('headlines', 'maxdescription');
    // Magpie modvar deprecated
    $data['magpiechecked']      = xarModVars::get('headlines', 'magpie') ? true : false;

    $data['parser']             = xarModVars::get('headlines', 'parser');
    if (empty($data['parser'])) $data['parser'] = ($data['magpiechecked'] ? 'magpie' : 'default');
    // build array of available parsers
    $data['parsers'] = array();
    $data['parsers']['default'] = 'Default';
    if (xarMod::isAvailable('magpie')) { // only add parser if available
        $data['parsers']['magpie'] = 'Magpie';
    } else { // if not available, check parser isn't selected (ie module removed)
        if ($data['parser'] == 'magpie') { 
            $data['parser'] = 'default';
        }
    }
    if (xarMod::isAvailable('simplepie')) { // ony show parser if available
        $data['parsers']['simplepie'] = 'SimplePie';
    } else { // if not available, check parser isn't selected (ie module removed)
        if ($data['parser'] == 'simplepie') { 
            $data['parser'] = 'default';
        }
    }

    $data['authid']             = xarSecGenAuthKey();
    $data['pubtypes']           = xarMod::apiFunc('articles', 'user', 'getpubtypes');
    $data['importpubtype']      = xarModVars::get('headlines', 'importpubtype');
    return $data;
}
?>
