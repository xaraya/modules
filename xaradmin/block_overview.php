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

function translations_admin_block_overview()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('extid', 'id', $id)) return;

    $data = xarMod::apiFunc('blocks','types','getitem',array('type_id' => $id, 'type_state' => xarBlock::TYPE_STATE_ACTIVE));
    $data['dnType'] = XARMLS_DNTYPE_BLOCK;
    $data['dnName'] = $data['type'];
    $data['blockid'] = $id;

    $druidbar = translations_create_druidbar(INFO, XARMLS_DNTYPE_BLOCK, $data['type'], $id);
    $opbar = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_BLOCK, $data['type'], $id);
    $data = array_merge($data, $druidbar, $opbar);

    return $data;
}

?>