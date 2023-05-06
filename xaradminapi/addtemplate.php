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
 * create a new pubsub template
 * @param $args['name'] name of the template you want to create
 * @param $args['template'] the template text
 * @return mixed template ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_addtemplate($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    join(', ',$invalid), 'addtemplate', 'Pubsub');
        throw new Exception($msg);
    }

    // Security check
    if (!xarSecurity::check('AddPubSub')) return;

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // check this template isn't already in the DB
    $query = "SELECT templateid
              FROM $pubsubtemplatestable
              WHERE name = ?";

    $result = $dbconn->Execute($query, array($name));
    if (!$result) return;

    if (!$result->EOF) {
        $msg = xarML('Item already exists in function #(1)() in module #(2)',
                    'addtemplate', 'Pubsub');
        throw new Exception($msg);
    }

    // compile the template now
    $compiled = xarTpl::compileString($template);

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubtemplatestable);

    // Add item
    $query = "INSERT INTO $pubsubtemplatestable (
              templateid,
              name,
              template,
              compiled)
            VALUES (?,?,?,?)";
    $bindvars = array($nextId, $name, $template, $compiled);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $nextId = $dbconn->PO_Insert_ID($pubsubtemplatestable, 'templateid');

    // return eventID
    return $nextId;
}

?>
