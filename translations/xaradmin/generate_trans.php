<?php

/**
 * File: $Id$
 *
 * Generate translations
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_generate_trans()
{
    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','generate_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','generate_module_trans',$args);
        break;
        case XARMLS_DNTYPE_THEME:
        $args['themeid'] = xarSessionGetVar('translations_themeid');
        $res = xarModAPIFunc('translations','admin','generate_theme_trans',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_result', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_result'));
}

?>