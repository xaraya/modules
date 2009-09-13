<?php
/**
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
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    switch ($data['tab']) {
        case 'locales':
            if (!xarVarFetch('defaultlocale','str:1:',$defaultLocale)) return;
            if (!xarVarFetch('active','isset',$active)) return;
            if (!xarVarFetch('mlsmode','str:1:',$MLSMode,'SINGLE',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('translationsbackend','str:1:',$translationsBackend)) return;

            $localesList = array();
            foreach($active as $activelocale) $localesList[] = $activelocale;
            if (!in_array($defaultLocale,$localesList)) $localesList[] = $defaultLocale;
            sort($localesList);

            if (($MLSMode == 'UNBOXED') && (xarMLSGetCharsetFromLocale($defaultLocale) != 'utf-8')) {
                $msg = xarML('You should select utf-8 locale as default before selecting UNBOXED mode');
                throw new Exception($msg);
            }

            // Locales
            xarConfigVars::set(null,'Site.MLS.MLSMode', $MLSMode);
            xarConfigVars::set(null,'Site.MLS.DefaultLocale', $defaultLocale);
            xarConfigVars::set(null,'Site.MLS.AllowedLocales', $localesList);
            xarConfigVars::set(null,'Site.MLS.TranslationsBackend', $translationsBackend);
            break;
        case 'display':
            if (!xarVarFetch('showcontext','checkbox',$showContext,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('maxreferences','int',$maxReferences,5,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('maxcodelines','int',$maxCodeLines,5,XARVAR_NOT_REQUIRED)) return;

            xarModVars::set('translations', 'showcontext',$showContext);
            xarModVars::set('translations', 'maxreferences',$maxReferences);
            xarModVars::set('translations', 'maxcodelines',$maxCodeLines);

            break;
        case 'release':
            if (!xarVarFetch('releasebackend','str:1:',$releaseBackend)) return;

            // xarModVars::set('translations', 'release_backend_type', $releaseBackend);

            break;
    }

    //FIXME: what is this?
    if (!isset($cacheTemplates)) {
        $cacheTemplates = true;

        // Call updateconfig hooks
        xarModCallHooks('module','updateconfig','translations', array('module' => 'translations'));
    }

    xarResponse::Redirect(xarModURL('translations', 'admin', 'modifyconfig',array('tab' => $data['tab'])));

    return true;
}

?>