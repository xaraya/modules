<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Get all tags
 *
 */
    sys::import('xaraya.structures.query');
    sys::import('modules.dynamicdata.class.objects.master');

    function karma_userapi_getall_tags($args)
    {
        extract($args);

        $xartable =& xarDB::getTables();
        $q = new Query('SELECT', $xartable['karma_tags']);
        $q->setorder('name','ASC');
//        $q->qecho();
        if (!$q->run()) return;

        $items = $q->output();
        return $items;
    }

?>