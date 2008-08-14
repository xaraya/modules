<?php
/*
 *
 * Mime Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
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
function mime_userapi_mime_to_extension( $args )
{

    extract($args);

    if (!isset($mime_type) || empty($mime_type)) {
        $msg = xarML('Missing \'mime_type\' parameter!');
        throw new Exception($msg);
    }

    $typeparts = explode('/',$mime_type);
    if (count($typeparts) < 2) {
        $msg = xarML('Missing mime type or subtype parameter!');
        throw new Exception($msg);
    }

    $xartable = xarDB::getTables();
    sys::import('modules.query.class.query');
    $q = new Query();
    $q->addtable($xartable['mime_type'], 'mt');
    $q->addtable($xartable['mime_subtype'], 'mst');
    $q->addtable($xartable['mime_extension'], 'me');
    $q->join('mt.xar_mime_type_id','mst.xar_mime_type_id');
    $q->join('mst.xar_mime_subtype_id','me.xar_mime_subtype_id');
    $q->eq('mt.xar_mime_type_name',$typeparts[0]);
    $q->eq('mst.xar_mime_subtype_name',$typeparts[1]);
    
    $q->addfield('xar_mime_type_name AS type_name');
    $q->addfield('xar_mime_subtype_name AS subtype_name');
    $q->addfield('xar_mime_extension_name AS extension');
    if (!$q->run()) return;

    return $q->output();
}

?>
