<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * modify and update configuration
 * @param string phase
 * @return mixed true on success of update, or array with template to show
 */
function categories_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminCategories')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:
            $catsperpage = xarModGetVar('categories','catsperpage');
            if (!$catsperpage) {
                $catsperpage = 10;
            }

            $useJSdisplay = xarModGetVar('categories','useJSdisplay');
            if (!$useJSdisplay) {
                $useJSdisplay = false;
            }

            $extrainfo = array();
            $extrainfo['module'] = 'categories';
            $hooks = xarModCallHooks('module', 'modifyconfig', 'categories', $extrainfo);

            if (empty($hooks)) {
                $hooks = '';
            }

            $data = array ('catsperpage'   => $catsperpage,
                           'useJSdisplay'  => $useJSdisplay,
                           'hooks'         => $hooks);
            $data['submitlabel'] = xarML('Submit');

            $data['numstats'] = xarModGetVar('categories','numstats');
            if (empty($data['numstats'])) {
                $data['numstats'] = 100;
            }
            $data['showtitle'] = xarModGetVar('categories','showtitle');
            if (!empty($data['showtitle'])) {
                $data['showtitle'] = 1;
            }
            $data['usenameinstead'] = xarModGetVar('categories','usename')== true ? 1 : 0;

            return xarTplModule('categories','admin','config',$data);
            break;

        case 'update':
            if (!xarVarFetch('catsperpage', 'int:1:1000', $catsperpage, 10, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useJSdisplay', 'bool', $useJSdisplay)) return;
            if (!xarVarFetch('usenameinstead', 'bool', $usenameinstead)) return;
            if (!xarSecConfirmAuthKey()) return;
            xarModSetVar('categories','catsperpage', $catsperpage);
            xarModSetVar('categories','useJSdisplay', $useJSdisplay);
            xarModSetVar('categories','usename', $usenameinstead);
            if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
            xarModSetVar('categories', 'numstats', $numstats);
            xarModSetVar('categories', 'showtitle', $showtitle);

            // Call update config hooks
            xarModCallHooks('module','updateconfig','categories', array('module' => 'categories'));
            xarResponseRedirect(xarModUrl('categories','admin','modifyconfig',array()));

            break;
    }

    return true;
}

?>
