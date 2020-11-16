<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    // This is the cached realm ids of Roles.User.123
    $cacheKey ='Roles.User.' . xarSession::getVar('role_id');
    $infoid = 'realm_ids';
    
    // We already have the realm ids, bail
    if (!xarCoreCache::isCached($cacheKey, $infoid)) {

        // Get all the realms we've registered
        sys::import('modules.realms.xartables');
        xarDB::importTables(realms_xartables());
        $xartable =& xarDB::getTables();
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT');
        $q->addtable($xartable['realms_members'], 'rm');
        $q->addtable($xartable['roles'], 'r');
        $q->join('r.id', 'rm.role_id');
        $q->eq('r.id', xarUser::getVar('id'));
        $q->addfield('rm.realm_id AS realm_id');
        
        // CHECKME: is returning false on no success a good idea?
        if (!$q->run()) {
            return false;
        }
        $result = $q->output();
        
        // Check whether one of the parent roles is a realm
        xarCoreCache::setCached($cacheKey, $infoid, $result);
    }
