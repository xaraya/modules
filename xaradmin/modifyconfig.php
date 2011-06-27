<?php
/**
 * Images module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
function images_admin_modifyconfig()
{

    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    if (!xarVarFetch('phase',        'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'images'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons, enable_short_urls');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:
            // get the current module variables for display
            // *********************************************
            // Global
            $data['gdextension'] = extension_loaded ('gd'); // True or false
            $data['libtype']['graphics-library']    = xarModVars::get('images', 'type.graphics-library'); // return gd
            $data['path']['derivative-store']       = xarModVars::get('images', 'path.derivative-store');
            $data['file']['cache-expire']           = xarModVars::get('images', 'file.cache-expire');
            if (!isset($data['file']['cache-expire'])) {
                xarModVars::set('images', 'file.cache-expire', 60);
            }
            $data['file']['imagemagick']            = xarModVars::get('images', 'file.imagemagick');
            if (!isset($data['file']['imagemagick'])) {
                xarModVars::set('images', 'file.imagemagick', '');
            }
            // Get the constant definitions
            xarModAPILoad('images');
            $data['library']   = array('GD'          => _IMAGES_LIBRARY_GD,
                                       'ImageMagick' => _IMAGES_LIBRARY_IMAGEMAGICK,
                                       'NetPBM'      => _IMAGES_LIBRARY_NETPBM);
        
            $shortURLs = xarModVars::get('images', 'SupportShortURLs');
        
            $data['shortURLs'] = empty($shortURLs) ? 0 : 1;
        
            $data['basedirs'] = xarModAPIFunc('images','user','getbasedirs');
            $data['basedirs'][] = array('basedir' => '',
                                        'baseurl' => '',
                                        'filetypes' => '',
                                        'recursive' => false);
        
            $hooks = xarModCallHooks('module', 'modifyconfig', 'images', array());
        
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
        break;

        case 'update':
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
        
            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                return xarTplModule('images','admin','modifyconfig', $data);
            } else {
                $itemid = $data['module_settings']->updateItem();
            }

            // Get parameters
            if (!xarVarFetch('libtype', 'list:int:1:3', $libtype,   '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('file',    'list:str:1:',  $file,   '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('path',    'list:str:1:',  $path,   '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('view',    'list:str:1:',  $view,   '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('shortURLs', 'checkbox', $shortURLs, TRUE)) return;
        
            if (isset($shortURLs) && $shortURLs) {
                xarModVars::set('images', 'SupportShortURLs', TRUE);
            } else {
                xarModVars::set('images', 'SupportShortURLs', FALSE);
            }
        
            if (isset($libtype) && is_array($libtype)) {
                foreach ($libtype as $varname => $value) {
                    // check to make sure that the value passed in is
                    // a real images module variable
                    if (NULL !== xarModVars::get('images', 'type.'.$varname)) {
                        xarModVars::set('images', 'type.' . $varname, $value);
                    }
                }
            }
            if (isset($file) && is_array($file)) {
                foreach ($file as $varname => $value) {
                    // check to make sure that the value passed in is
                    // a real images module variable
                    if (NULL !== xarModVars::get('images', 'file.'.$varname)) {
                        xarModVars::set('images', 'file.' . $varname, $value);
                    }
                }
            }
            if (isset($path) && is_array($path)) {
                     foreach ($path as $varname => $value) {
                    // check to make sure that the value passed in is
                    // a real images module variable
                    $value = trim(mb_ereg_replace('\/$', '', $value));
                    if (NULL !== xarModVars::get('images', 'path.' . $varname)) {
                        $thispath = sys::root() . $value;
                        if (!file_exists($thispath) || !is_dir($thispath)) {
                            return xarTplModule('images','user','errors',array('layout' => 'no_directory', 'location' => $varname));
                        } elseif (!is_writable($thispath)) {
                            return xarTplModule('images','user','errors',array('layout' => 'cannot_write', 'location' => $varname));
                        } else {
                            xarModVars::set('images', 'path.' . $varname, $value);
                        }
                    }
                }
            }
            if (isset($view) && is_array($view)) {
                foreach ($view as $varname => $value) {
                    // check to make sure that the value passed in is
                    // a real images module variable
        // TODO: add other view.* variables later ?
                    if ($varname != 'itemsperpage') continue;
                    xarModVars::set('images', 'view.' . $varname, $value);
                }
            }
        
            if (!xarVarFetch('basedirs', 'isset', $basedirs, '', XARVAR_NOT_REQUIRED)) return;
            if (!empty($basedirs) && is_array($basedirs)) {
                $newdirs = array();
                $idx = 0;
                foreach ($basedirs as $id => $info) {
                    if (empty($info['basedir']) && empty($info['baseurl']) && empty($info['filetypes'])) {
                        continue;
                    }
                    $newdirs[$idx] = array('basedir' => $info['basedir'],
                                           'baseurl' => $info['baseurl'],
                                           'filetypes' => $info['filetypes'],
                                           'recursive' => (!empty($info['recursive']) ? true : false));
                    $idx++;
                }
                xarModVars::set('images','basedirs',serialize($newdirs));
            }
        
            xarModCallHooks('module', 'updateconfig', 'images', array('module' => 'images'));
            xarResponse::redirect(xarModURL('images','admin','modifyconfig'));
        break;
    }
    return $data;
}
?>
