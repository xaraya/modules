<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_adminapi_getpageaccessconstraints($args)
{
    if (!isset($args['property'])) {
        throw new Exception(xarML('Missing property param in publications_adminapi_getpageaccessconstraints'));
    }

    $constraints = [
        'display' => ['level' => 800, 'group' => 0, 'failure' => 1],
        'add'     => ['level' => 800, 'group' => 0, 'failure' => 1],
        'modify'  => ['level' => 800, 'group' => 0, 'failure' => 1],
        'delete'  => ['level' => 800, 'group' => 0, 'failure' => 1],
    ];

    $unpacked_constraints = $args['property']->getValue();
    if (empty($unpacked_constraints)) {
        return $constraints;
    }
    try {
        // Check the array structure
        if (isset($unpacked_constraints['display'])) {
            $constraints = $unpacked_constraints;
        }
    } catch (Exception $e) {
    }

    return $constraints;
}
