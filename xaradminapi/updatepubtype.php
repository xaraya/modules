<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Update a publication type
 *
 * @param id $args['ptid'] ID of the publication type
 * @param string $args['name'] name of the publication type (not allowed here)
 * @param string $args['description'] description of the publication type
 * @param array $args['config'] configuration of the publication type
 * @return bool true on success, false on failure
 */
function publications_adminapi_updatepubtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($ptid) || !is_numeric($ptid) || $ptid < 1) {
        $invalid[] = 'publication type ID';
    }
/*
    if (!isset($name) || !is_string($name) || empty($name)) {
        $invalid[] = 'name';
    }
*/
    if (!isset($descr) || !is_string($descr) || empty($descr)) {
        $invalid[] = 'description';
    }
    if (!isset($config) || !is_array($config) || count($config) == 0) {
        $invalid[] = 'configuration';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'updatepubtype','Publications');
        throw new BadParameterException(null,$msg);
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurity::check('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;

    // Load user API to obtain item information function
    if (!xarMod::apiLoad('publications', 'user')) return;

    // Get current publication types
    $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'updatepubtype',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    // Make sure we have all the configuration fields we need
    $pubfields = xarMod::apiFunc('publications','user','getpubfields');
    foreach ($pubfields as $field => $value) {
        if (!isset($config[$field])) {
            $config[$field] = '';
        }
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $pubtypestable = $xartable['publication_types'];

    // Update the publication type (don't allow updates on name)
    $query = "UPDATE $pubtypestable
            SET pubtypedescr = ?,
                pubtypeconfig = ?
            WHERE pubtype_id = ?";
    $bindvars = array($descr, serialize($config), $ptid);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

?>