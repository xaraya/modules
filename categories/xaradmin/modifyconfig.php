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
            } elseif (is_array($hooks)) {
                $hooks = join('',$hooks);
            }

            $data = array ('catsperpage'   => $catsperpage,
                           'useJSdisplay'  => $useJSdisplay,
                           'hooks'         => $hooks);
            $data['submitlabel'] = xarML('Submit');

            return xarTplModule('categories','admin','config',$data);
            break;

        case 'update':
            if (!xarVarFetch('catsperpage', 'int:1:1000', $catsperpage, 10, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('useJSdisplay', 'bool', $useJSdisplay)) return;
            if (!xarSecConfirmAuthKey()) return;
            xarModSetVar('categories','catsperpage', $catsperpage);
            xarModSetVar('categories','useJSdisplay', $useJSdisplay);

            // Call update config hooks
            xarModCallHooks('module','updateconfig','categories', array('module' => 'categories'));
            xarResponseRedirect(xarModUrl('categories','admin','modifyconfig',array()));

            break;
    } 

    return true;
} 

?>
