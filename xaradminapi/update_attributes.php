<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * update the atributes of an object
 */
function eav_adminapi_update_attributes(Array $args=array())
{
    extract($args);

    // Required arguments
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'property id';
    }
    if (!isset($label) || !is_string($label)) {
        $invalid[] = 'label';
    }
    if (!isset($type) || !is_numeric($type)) {
        $invalid[] = 'type';
    }
    if (count($invalid) > 0) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array(join(', ',$invalid), 'admin', 'updateprop', 'DynamicData');
        throw new BadParameterException($vars, $msg);
    }

    // TODO: security check on object level

    // Get database setup - note that xarDB::getConn()
    // returns an array but we handle it differently.
    // For xarDB::getConn() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $dynamicprop = $xartable['dynamic_properties'];

    $bindvars = array();
    $sql = "UPDATE $dynamicprop SET label = ?, type = ?";
    $bindvars[] = $label; $bindvars[] = $type;
    if (isset($defaultvalue) && is_string($defaultvalue)) {
        $sql .= ", defaultvalue = ?";
        $bindvars[] = $defaultvalue;
    }
    if (isset($seq) && is_numeric($seq)) {
        $sql .= ", seq = ?";
        $bindvars[] = $seq;
    }
    // TODO: verify that the data source exists
    if (isset($source) && is_string($source)) {
        $sql .= ", source = ?";
        $bindvars[] = $source;
    }
    if (isset($configuration) && is_string($configuration)) {
        $sql .= ", configuration = ?";
        $bindvars[] = $configuration;
    }
    if (isset($name) && is_string($name)) {
        $sql .= ", name = ?";
        $bindvars[] = $name;
    }
    if (isset($status) && is_numeric($status)) {
        $sql .= ", status = ?";
        $bindvars[] = $status;
    }

    $sql .= " WHERE id = ?";
    $bindvars[] = $id;
    $dbconn->Execute($sql,$bindvars);

    return true;
}
?>
