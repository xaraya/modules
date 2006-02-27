<?php
/**
 * Moveable type module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
function moveabletype_userapi_supportedTextFilters($args)
{
    // NOT supported yet, return an empty response according to the spec
    $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                         array('module'  => 'moveabletype',
                               'command' => 'supportedtextfilters',
                               'params'  => array())
                         );
    return $out;

}

?>