<?php

/**
 * File: $Id$
 *
 * Release result page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_release_result()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    $filename = xarSessionGetVar('translations_filename');
    if ($filename == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'release_info'));
    }
    xarSessionDelVar('translations_filename');

    $tplData['url'] = xarServerGetBaseURL().xarCoreGetVarDirPath().'/cache/'.$filename;

    $tran_type = xarSessionGetVar('translations_dnType');
    $druidbar = translations_create_generate_trans_druidbar(REL, $tran_type);
    $opbar = translations_create_opbar(GEN_TRANS);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>