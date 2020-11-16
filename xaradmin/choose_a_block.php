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

function translations_admin_choose_a_block()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!$blocklist = xarMod::apiFunc('blocks', 'types', 'getitems', array('module_id' => 0, 'type_state' => xarBlock::TYPE_STATE_ACTIVE))) {
        return;
    }

    $data = translations_create_druidbar(CHOOSE, xarMLS::DNTYPE_BLOCK, '', 0);
    $data['blocklist'] = $blocklist;
    $data['dnType'] = xarMLS::DNTYPE_BLOCK;
    return $data;
}
