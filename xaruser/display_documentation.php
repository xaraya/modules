<?php
/**
 * Release Module
 *
 * @package modules
 * @subpackage release
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Display an item of the release documentation object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function release_user_display_documentation()
    {
        if (!xarSecurityCheck('ReadRelease')) {
            return;
        }

        if (!xarVarFetch('name', 'str', $name, 'release_docs', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'release';

        return $data;
    }
