<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
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
    if (!xarSec::confirmAuthKey()) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) return;
    switch ($data['tab']) {
        case 'locales':
            if (!xarVar::fetch('defaultlocale','str:1:',$defaultLocale)) return;
            if (!xarVar::fetch('active','isset',$active)) return;
            if (!xarVar::fetch('mlsmode','str:1:',$MLSMode,'SINGLE',xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('translationsbackend','str:1:',$translationsBackend)) return;

            $localesList = array();
            foreach($active as $activelocale) $localesList[] = $activelocale;
            if (!in_array($defaultLocale,$localesList)) $localesList[] = $defaultLocale;
            sort($localesList);

            if (($MLSMode == 'UNBOXED') && (xarMLS::getCharsetFromLocale($defaultLocale) != 'utf-8')) {
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
            if (!xarVar::fetch('showcontext','checkbox',$showContext,false,xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('maxreferences','int',$maxReferences,5,xarVar::NOT_REQUIRED)) return;
            if (!xarVar::fetch('maxcodelines','int',$maxCodeLines,5,xarVar::NOT_REQUIRED)) return;

            xarModVars::set('translations', 'showcontext',$showContext);
            xarModVars::set('translations', 'maxreferences',$maxReferences);
            xarModVars::set('translations', 'maxcodelines',$maxCodeLines);

            break;
        case 'release':
            if (!xarVar::fetch('releasebackend','str:1:',$releaseBackend)) return;

            // xarModVars::set('translations', 'release_backend_type', $releaseBackend);

            break;
    }

    //FIXME: what is this?
    if (!isset($cacheTemplates)) {
        $cacheTemplates = true;

        // Call updateconfig hooks
        xarModHooks::call('module','updateconfig','translations', array('module' => 'translations'));
    }

    xarController::redirect(xarController::URL('translations', 'admin', 'modifyconfig',array('tab' => $data['tab'])));

    return true;
}

?>