<?php

/**
 * File: $Id$
 *
 * Skels generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_generate_skels()
{

    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_working_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','generate_core_skels',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','generate_module_skels',$args);
        break;
        case XARMLS_DNTYPE_THEME:
        $args['themeid'] = xarSessionGetVar('translations_themeid');
        $res = xarModAPIFunc('translations','admin','generate_theme_skels',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_result', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'generate_skels_result'));
}

?>