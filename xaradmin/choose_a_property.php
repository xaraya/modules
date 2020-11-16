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
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }
    
    xarMod::apiLoad('dynamicdata');
    $tables =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query('SELECT', $tables['dynamic_properties_def']);
    $q->eq('modid', 0);
    $q->run();
    $propertylist = $q->output();
    
    $data = translations_create_druidbar(CHOOSE, xarMLS::DNTYPE_PROPERTY, '', 0);
    $data['propertylist'] = $propertylist;
    $data['dnType'] = xarMLS::DNTYPE_PROPERTY;
    return $data;
}
