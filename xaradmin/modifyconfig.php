<?php

/**
 * modify configuration
 */
function categories_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminCategories')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:
            $catsperpage = xarModVars::get('categories','catsperpage');
            if (!$catsperpage) {
                $catsperpage = 10;
            }

            $useJSdisplay = xarModVars::get('categories','useJSdisplay');
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

            $data['numstats'] = xarModVars::get('categories','numstats');
            if (empty($data['numstats'])) {
                $data['numstats'] = 100;
            }
            $data['showtitle'] = xarModVars::get('categories','showtitle');
            if (!empty($data['showtitle'])) {
                $data['showtitle'] = 1;
            }

            return xarTplModule('categories','admin','modifyconfig',$data);
            break;

        case 'update':
            if (!xarVarFetch('catsperpage', 'int:1:1000', $catsperpage, 10, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useJSdisplay', 'bool', $useJSdisplay)) return;
            if (!xarSecConfirmAuthKey()) return;
            xarModVars::set('categories','catsperpage', $catsperpage);
            xarModVars::set('categories','useJSdisplay', $useJSdisplay);
            if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
            xarModVars::set('categories', 'numstats', $numstats);
            xarModVars::set('categories', 'showtitle', $showtitle);

            // Call update config hooks
            xarModCallHooks('module','updateconfig','categories', array('module' => 'categories'));
            xarResponseRedirect(xarModUrl('categories','admin','modifyconfig',array()));

            break;
    }

    return true;
}

?>
