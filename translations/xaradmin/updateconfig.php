<?php

/**
 * File: $Id$
 *
 * Update configuration for translations module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Update configuration
 *
 * @param string
 * @return void?
 * @todo move in timezone var when we support them
 * @todo decide whether a site admin can set allowed locales for users
 * @todo add decent validation
 */
function translations_admin_updateconfig()
{
    if (!xarVarFetch('mlsmode','str:1:',$MLSMode,'SINGLE',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('translationsbackend','str:1:',$translationsBackend)) return;
    if (!xarVarFetch('releasebackend','str:1:',$releaseBackend)) return;
    if (!xarVarFetch('showcontext','checkbox',$showContext,false,XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if (!isset($cacheTemplates)) {
        $cacheTemplates = true;
    }

    // MLS variables
    xarLogVariable('mls mode',$MLSMode);
//    xarConfigSetVar('Site.MLS.MLSMode', $MLSMode);
    xarConfigSetVar('Site.MLS.MLSMode', 'SINGLE');
    xarConfigSetVar('Site.MLS.TranslationsBackend', $translationsBackend);
//    xarModSetVar('translations', 'release_backend_type', $releaseBackend);
    xarModSetVar('translations', 'showcontext',$showContext);

    xarResponseRedirect(xarModURL('translations', 'admin', 'modifyconfig'));

    return true;
}

?>