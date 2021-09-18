<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * delete a pubsub template
 * @param $args['id'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deltemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = [];
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (count($invalid) > 0) {
        $msg = xarML(
            'Invalid #(1) for function #(2)() in module #(3)',
            join(', ', $invalid),
            'deltemplate',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurity::check('DeletePubSub')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // Delete item
    $query = "DELETE FROM $pubsubtemplatestable
              WHERE id = ?";
    $result = $dbconn->Execute($query, [(int)$id]);
    if (!$result) {
        return;
    }

    return true;
}
