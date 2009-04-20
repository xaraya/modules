<?php
/**
 * Delete fuzzy file result
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_delete_fuzzy_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $locale = translations_working_locale(); 
    $backend = xarModAPIFunc('translations', 'admin', 'create_backend_instance',
                             array('interface' => 'ReferencesBackend', 'locale' => $locale)); 
    if (!isset($backend)) return;

    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
            $ctxType = 'core:';
            break;
        case XARMLS_DNTYPE_MODULE:
            $ctxType = 'modules:';
            break;
        case XARMLS_DNTYPE_THEME:
            $ctxType = 'themes:';
            break;
    }
    $ctxName = 'fuzzy';

    if ($backend->bindDomain($dnType,$dnName)) {
        $fileName = $backend->findContext($ctxType, $ctxName);
        unlink($fileName);
    }

    xarResponse::Redirect(xarModURL('translations', 'admin', 'translate', 
        array('dnType' => $dnType,
              'dnName' => $dnName,
              'extid' => $extid)));

    return true;
}

?>