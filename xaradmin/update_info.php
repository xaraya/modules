<?php
/**
 * Update the session info with how we are going to translate
 *
 * @package modules
 * @copyright (C) copyright-placeholder
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

    if (!xarVarFetch('dntype', 'regexp:/^(core|module|theme)$/', $type)) return;

    switch ($type) {
        case 'core':
        $url = xarModURL('translations', 'admin', 'core_overview');
        break;
        case 'module':
        $url = xarModURL('translations', 'admin', 'choose_a_module');
        break;
        case 'theme':
        $url = xarModURL('translations', 'admin', 'choose_a_theme');
        break;
    }
    xarResponse::Redirect($url);
}

?>