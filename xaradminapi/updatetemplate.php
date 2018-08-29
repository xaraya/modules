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
 * update an existing pubsub template
 * @param $args['id'] the ID of the item
 * @param $args['name'] the new name of the item
 * @param $args['template'] the new template text of the item
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatetemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatetemplate', 'Pubsub');
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:All:$id")) return;

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // compile the template now
    $compiled = xarTplCompileString($template);

    // Update the item
    $query = "UPDATE $pubsubtemplatestable
              SET template = ?,
                  compiled = ?,
                  name = ?
              WHERE id = ?";
    $bindvars = array($template, $compiled, $name, (int)$id);
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    return true;
}

?>
