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
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_choose_a_theme()
{
    // Security Check
    if (!xarSecurity::check('AdminTranslations')) {
        return;
    }

    if (!($themelist = xarMod::apiFunc('themes', 'admin', 'getthemelist', array('filter' => array('State' => xarTheme::STATE_ANY))))) {
        return;
    }

    $tplData = translations_create_druidbar(CHOOSE, xarMLS::DNTYPE_THEME, '', 0);
    $tplData['themelist'] = $themelist;
    $tplData['dnType'] = xarMLS::DNTYPE_THEME;
    return $tplData;
}
