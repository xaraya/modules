<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Delete a publication type
 *
 * @param $args['ptid'] ID of the publication type
 * @return bool true on success, false on failure
 */
function publications_adminapi_deletepubtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    if (!isset($ptid) || !is_numeric($ptid) || $ptid < 1) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deletepubtype',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('publications', 'user')) return;

    // Get current publication types
    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deletepubtype',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $pubtypestable = $xartable['publication_types'];

    // Delete the publication type
    $query = "DELETE FROM $pubtypestable
            WHERE pubtype_id = ?";
    $result =& $dbconn->Execute($query,array($ptid));
    if (!$result) return;

    $publicationstable = $xartable['publications'];

    // Delete all publications for this publication type
    $query = "DELETE FROM $publicationstable
            WHERE pubtype_id = ?";
    $result =& $dbconn->Execute($query,array($ptid));
    if (!$result) return;

// TODO: call some kind of itemtype delete hooks here, once we have those
    //xarModCallHooks('itemtype', 'delete', $ptid,
    //                array('module' => 'publications',
    //                      'itemtype' =>'ptid'));

    return true;
}

?>
