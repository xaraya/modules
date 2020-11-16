<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

/**
 * Attempt to convert a MIME type to a file extension.
 * If we cannot map the type to a file extension, we return false.
 *
 * Code originally based on hordes Magic class (www.horde.org)
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   string      $mime_type MIME type to be mapped to a file extension.
 * @return  string      The file extension of the MIME type.
 */
function mime_userapi_mime_to_extension($args)
{
    extract($args);

    if (!isset($mime_type) || empty($mime_type)) {
        $msg = xarML('Missing \'mime_type\' parameter!');
        throw new Exception($msg);
    }

    $typeparts = explode('/', $mime_type);
    if (count($typeparts) < 2) {
        $msg = xarML('Missing mime type or subtype parameter!');
        throw new Exception($msg);
    }

    $xartable =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query();
    $q->addtable($xartable['mime_type'], 'mt');
    $q->addtable($xartable['mime_subtype'], 'mst');
    $q->addtable($xartable['mime_extension'], 'me');
    $q->join('mt.id', 'mst.type_id');
    $q->join('mst.id', 'me.subtype_id');
    $q->eq('mt.name', $typeparts[0]);
    $q->eq('mst.name', $typeparts[1]);
    
    $q->addfield('mt.name AS type_name');
    $q->addfield('mst.name AS subtype_name');
    $q->addfield('me.name AS extension');
    if (!$q->run()) {
        return;
    }

    return $q->output();
}
