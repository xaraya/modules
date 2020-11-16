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

function translations_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'mailer_general', XARVAR_NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', array('module' => 'mailer'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    $localehome = sys::varpath().'/locales';
    if (!file_exists($localehome)) {
        throw new Exception('The locale directory was not found.');
    }
    $dd = opendir($localehome);
    $locales = array();
    while ($filename = readdir($dd)) {
        if (is_dir($localehome . "/" . $filename) && file_exists($localehome . "/" . $filename . "/locale.xml")) {
            $locales[] = $filename;
        }
    }
    closedir($dd);

    $allowedlocales = xarConfigVars::get(null, 'Site.MLS.AllowedLocales');
    foreach ($locales as $locale) {
        if (in_array($locale, $allowedlocales)) {
            $active = true;
        } else {
            $active = false;
        }
        $data['locales'][] = array('name' => $locale, 'active' => $active);
    }

    $data['translationsBackend'] = xarConfigVars::get(null, 'Site.MLS.TranslationsBackend');
    $data['releaseBackend'] = xarModVars::get('translations', 'release_backend_type');
    $data['showcontext'] = xarModVars::get('translations', 'showcontext');
    $data['maxreferences'] = xarModVars::get('translations', 'maxreferences');
    $data['maxcodelines'] = xarModVars::get('translations', 'maxcodelines');

    switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'general':
                        break;
                }

                break;

            case 'update':
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) {
                    return;
                }
                $isvalid = $data['module_settings']->checkInput();
                if (!$isvalid) {
                    return xarTplModule('mailer', 'admin', 'modifyconfig', $data);
                } else {
                    $itemid = $data['module_settings']->updateItem();
                }
                xarController::redirect(xarModURL('translations', 'admin', 'modifyconfig', array('tab' => $data['tab'])));
                // Return
                return true;
                break;

        }
    return $data;
}
