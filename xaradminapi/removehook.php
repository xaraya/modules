<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * delete all entries for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_adminapi_removehook($args)
{
    extract($args);

    if (!isset($extrainfo)) throw new EmptyParameterException('extrainfo');
    if (!isset($objectid)) throw new EmptyParameterException('objectid');
    if (!is_numeric($objectid)) throw new VariableValidationException(array('objectid',$objectid,'numeric'));

    if (!is_array($extrainfo)) {
        $extrainfo = array();
    }

    $modid = xarModGetIDFromName($objectid);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module id', 'admin', 'newhook', 'polls');
        throw new BadParameterException($vars,$msg);
    }

    $polls = xarModAPIFunc('polls','user','getall',
                           array('modid' => $modid));

    if (empty($polls) || count($polls) < 1) {
        return $extrainfo;
    }

    foreach ($polls as $poll) {
        xarModAPIFunc('polls','admin','delete',
                      array('pid' => $poll['pid']));
    }

    // Return the extra info
    return $extrainfo;
}

?>
