<?php

/**
 * File: $Id$
 *
 * Generate skels result
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_generate_skels_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $tplData = xarSessionGetVar('translations_result');
    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_skels_info'));
    }
    xarSessionDelVar('translations_result');

    $tran_type = xarSessionGetVar('translations_dnType');
    $druidbar = translations_create_generate_skels_druidbar(GEN,$tran_type);
    $opbar = translations_create_opbar(GEN_SKELS);
    $tplData['dnType'] = translations__dnType2Name($tran_type);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>