<?php

/**
 * File: $Id$
 *
 * Generate translations result
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_generate_trans_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = xarSessionGetVar('translations_result');
    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_info'));
    }
    xarSessionDelVar('translations_result');

    $druidbar = translations_create_generate_trans_druidbar(GEN);
    $opbar = translations_create_opbar(GEN_TRANS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>