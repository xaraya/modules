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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function translations_admin_choose_a_property()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!($propertylist = xarMod::apiFunc('themes','admin','getthemelist',array('filter' => array('State' => XARTHEME_STATE_ANY))))) return;

    $tplData = translations_create_druidbar(CHOOSE, XARMLS_DNTYPE_PROPERTY, '', 0);
    $tplData['propertylist'] = $propertylist;
    $tplData['dnType'] = XARMLS_DNTYPE_PROPERTY;
    return $tplData;
}

?>