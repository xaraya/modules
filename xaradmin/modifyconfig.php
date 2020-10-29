<?php
/**
 * Modify configuration
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_admin_modifyconfig()
{

    // Security Check
    if (!xarSecurity::check('AdminCKEditor')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) {
        return;
    }

    switch (strtolower($phase)) {
        case 'modify':
            break;

        case 'update':
            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            /*if (!xarVar::fetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('ckeditor', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVar::fetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVar::fetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('ckeditor', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVar::fetch('aliasname', 'str', $aliasname,  xarModVars::get('ckeditor', 'aliasname'), XARVAR_NOT_REQUIRED)) return;*/
      
            $pgrconfig = array(
                'rootPath' => 'str',
                'urlPath' => 'str',
                'allowedExtensions' => 'str',
                //'imagesExtensions' => 'str',
                'fileMaxSize' => 'int',
                'imageMaxHeight' => 'int',
                'imageMaxWidth' => 'int',
                'allowEdit' => 'str'
            );

            foreach ($pgrconfig as $key => $type) {
                $setting = 'PGRFileManager_'.$key;
                if (!xarVar::fetch($setting, $type, ${$setting}, xarModVars::get('ckeditor', $setting), XARVAR_NOT_REQUIRED)) {
                    return;
                }
                
                if ($key == 'imagesExtensions' || $key == 'allowedExtensions') {
                    ${$setting} = str_replace(' ', '', ${$setting});
                    $arr = explode(',', ${$setting});
                    $end = end($arr);
                    if (empty($end)) {
                        array_pop($arr);
                    }
                    ${$setting} = implode(', ', $arr);
                }

                xarModVars::set('ckeditor', $setting, ${$setting});
                xarMod::apiFunc('ckeditor', 'admin', 'modifypluginsconfig', array(
                'name' => 'PGRFileManager.' . $key,
                'value' => ${$setting}
                ));
            }
    
            xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'modifyconfig'));
            // Return
            return true;
            break;

    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
