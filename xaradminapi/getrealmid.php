<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Param Software Services <paramsoft@eth.net>
 */

/**
 * Function to get required realmsid
 *
 */
sys::import('xaraya.structures.query');
function realms_adminapi_getrealmid($args)
{
    extract($args);

    $xartable =& xarDB::getTables();

    $q = new Query('SELECT', $xartable[$tablename]);
    $q->eq('id', $itemid);
    $q->addfield('realm_id');
    if (!$q->run()) {
        return;
    }
    $result = $q->row();

    if (empty($result)) {
        return 0;
    }
    return $result['realm_id'];
}
