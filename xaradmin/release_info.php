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
 * @author Volodymyr Metenchuk <voll@xaraya.com>
 */

function translations_admin_release_info()
{
    // Security Check
    if(!xarSecurity::check('AdminTranslations')) return;

    if (!xarVar::fetch('dnType','int',$dnType)) return;
    if (!xarVar::fetch('dnName','str:1:',$dnName)) return;
    if (!xarVar::fetch('extid','int',$extid)) return;

    $druidbar = translations_create_druidbar(REL, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(RELEASE, $dnType, $dnName, $extid);
    $tplData = array_merge($druidbar, $opbar);

    $tplData['dnType'] = $dnType;
    $tplData['dnTypeText'] = xarMLSContext::getContextTypeText($dnType);
    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}

?>