<?php

/**
 * File: $Id$
 *
 * Update the session info with how we are going to translate
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function translations_admin_update_info()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('ctxtype', 'regexp:/^(core|module|theme)$/', $type)) return;

    switch ($type) {
        case 'core':
        $url = xarModURL('translations', 'admin', 'core_overview');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_CORE);
        break;
        case 'module':
        $url = xarModURL('translations', 'admin', 'choose_a_module');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_MODULE);
        break;
        case 'theme':
        $url = xarModURL('translations', 'admin', 'choose_a_theme');
        xarSessionSetVar('translations_dnType', XARMLS_DNTYPE_THEME);
        break;
    }
    xarResponseRedirect($url);
}