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
 * get an existing pubsub template
 * @param $args['id'] the ID of the item
 * @returns array
 * @return array of template information
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_userapi_gettemplate($args)
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
            'gettemplate',
            'Pubsub'
        );
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurity::check('EditPubSub', 1, 'item', "All:All:All:$id")) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // Update the item
    $query = "SELECT id,
                     name,
                     template,
                     compiled
              FROM $pubsubtemplatestable
              WHERE id = ?";
    $result = $dbconn->Execute($query, [(int)$id]);
    if (!$result) {
        return;
    }

    $info = [];
    if ($result->EOF) {
        return $info;
    }

    [$info['id'], $info['name'], $info['template'], $info['compiled']] = $result->fields;
    $result->Close();

    return $info;
}
