<?php

/**
 * File: $Id$
 *
 * Release page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_release()
{
    $dnType = xarSessionGetVar('translations_dnType');
    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','release_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = xarSessionGetVar('translations_modid');
        $res = xarModAPIFunc('translations','admin','release_module_trans',$args);
        break;
    }
    if (!isset($res)) return;

    xarSessionSetVar('translations_filename', $res);
    xarResponseRedirect(xarModURL('translations', 'admin', 'release_result'));
}

?>