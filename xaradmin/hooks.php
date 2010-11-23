<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
function twitter_admin_hooks($args)
{
    if (!xarSecurityCheck('AdminTwitter')) return;
    
    if (!xarVarFetch('hookmodule', 'pre:trim:lower:str:1:', $hookmodule, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hookitemtype', 'int', $hookitemtype, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    
    if ($phase == 'update') {
        if (!xarSecConfirmAuthKey())
            return xarTplModule('privileges', 'user', 'error', array('layout' => 'bad_author')); 
        if (!xarMod::apiFunc('twitter', 'hooks', 'moduleupdateconfig',
            array('extrainfo' => array('module' => $hookmodule, 'itemtype' => $hookitemtype)))) return;
        $return_url = xarModURL('twitter', 'admin', 'hooks', 
            array('hookmodule' => $hookmodule, 'hookitemtype' => $hookitemtype));
        return xarResponse::redirect($return_url);  
    }

    $subjects = xarMod::apiFunc('modules', 'admin', 'gethookedmodules',
        array(
            'hookModName' => 'twitter',
        ));
    if (!empty($subjects)) {
        foreach ($subjects as $modname => $hooks) {
            $modinfo = xarMod::getInfo(xarMod::getRegID($modname));
            try {
                $itemtypes = xarMod::apiFunc($modname, 'user', 'getitemtypes');
            } catch (Exception $e) {
                $itemtypes = array();
            }
            $modinfo['itemtypes'] = array();
            foreach ($itemtypes as $typeid => $typeinfo) {
                if (!isset($hooks[0]) && !isset($hooks[$typeid])) continue; // not hooked
                $modinfo['itemtypes'][$typeid] = $typeinfo;
            }
            $subjects[$modname] += $modinfo;
        }
    }
        
    $data = array();
    $data['subjects'] = $subjects;
    $data['hookmodule'] = $hookmodule;
    $data['hookitemtype'] = $hookitemtype;
    $data['hookconfig'] = xarMod::guiFunc('twitter', 'hooks', 'modulemodifyconfig',
        array('extrainfo' => array('module' => $hookmodule, 'itemtype' => $hookitemtype)));
    return $data;
}
?>