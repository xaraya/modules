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
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

function translations_admin_generate_skels_info()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!xarVarFetch('dnType', 'int', $dnType)) {
        return;
    }
    if (!xarVarFetch('dnName', 'str:1:', $dnName)) {
        return;
    }
    if (!xarVarFetch('extid', 'int', $extid)) {
        return;
    }

    $druidbar = translations_create_druidbar(GENSKELS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_SKELS, $dnType, $dnName, $extid);
    $data = array_merge($druidbar, $opbar);

    $data['dnType'] = $dnType;
    $data['dnTypeText'] = xarMLSContext::getContextTypeText($dnType);
    $data['dnName'] = $dnName;
    $data['extid'] = $extid;

    return $data;
}
