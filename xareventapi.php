<?php
/**
 * File: $Id: $
 *
 * Navigator Style Setup
 *
 * @package Navigator
 * @copyright (C) 2004 by the Charles and helen Schab Foundation
 * @link http://www.xaraya.com
 * @subpackage Navigator
 * @author Carl P. Corliss
 */

/**
 * Function to set up styles specific to this module
 *
 * @return Boolean
 */
function navigator_eventapi_OnServerRequest($arg)
{
    $styleList = @unserialize(xarModGetVar('navigator', 'style.list.inline'));
    $styleSheets = @unserialize(xarModGetVar('navigator', 'style.list.files'));
    if (isset($styleSheets) && !empty($styleSheets) && is_array($styleSheets)) {
        foreach ($styleSheets as $key => $styleSheet) {
            if (!($return = xarTplAddStyleLink('navigator', $styleSheet))) {
                // if we're here, it couldn't find the css file so remove it
                // from the list post-haste ;-)
                unset($styleSheets[$key]);
            } else {
                if (strstr($styleSheet, 'navigator-branchmenu')) {
                    xarModAPIFunc('base', 'javascript', 'modulefile',
                                   array('module'=>'navigator',
                                         'filename'=>'menu.js',
                                         'position'=>'head'));
                }
            }
        }
        xarModSetVar('navigator', 'style.list.files', serialize($styleSheets));
    }

    if (!isset($styleList) || !is_array($styleList) || !count($styleList)) {
        return FALSE;
    } else {
        $data['styles'] = $styleList;

        $style = xarTpl_includeModuleTemplate('navigator', 'inline-styles', $data);
        if (!isset($style) || empty($style)) {
            return FALSE;
        } else {
            if (!isset($GLOBALS['xarTpl_additionalStyles'])) {
                $GLOBALS['xarTpl_additionalStyles'] = $style;
            } else {
                $GLOBALS['xarTpl_additionalStyles'] .= $style;
            }

            return TRUE;
        }
    }
}

?>