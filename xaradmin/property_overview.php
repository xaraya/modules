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

function translations_admin_property_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!xarVarFetch('extid', 'id', $id)) {
        return;
    }

    xarMod::apiLoad('dynamicdata');
    $tables =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query('SELECT', $tables['dynamic_properties_def']);
    $q->eq('id', $id);
    $q->run();
    $data = $q->row();
    
    $data['dnType'] = xarMLS::DNTYPE_PROPERTY;
    $data['dnName'] = $data['name'];
    $data['propertyid'] = $id;

    $druidbar = translations_create_druidbar(INFO, xarMLS::DNTYPE_PROPERTY, $data['name'], $id);
    $opbar = translations_create_opbar(OVERVIEW, xarMLS::DNTYPE_PROPERTY, $data['name'], $id);
    $data = array_merge($data, $druidbar, $opbar);

    return $data;
}
