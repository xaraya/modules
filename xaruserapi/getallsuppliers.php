<?php
/**
 * Get all suppliers
 *
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @subpackage Vendors module
 */
/**
 * get all suppliers
 * @author Marc Lutolf <mfl@netspan.ch>
 * @returns array
 * @return array of suppliers, or empty array on failure
 */

function vendors_userapi_getallsuppliers($args)
{
    extract($args);

    $xartable = xarDB::getTables();
    if (isset($conditions)) {
        $conditions->addtable($xartable['roles'],'r');
        $q = $conditions;
    } else {
        $q = new xenQuery('SELECT',$xartable['roles'],'r');
    }
    $q->addfield('r.id');
    $q->addfield('r.name');
    $q->setorder('r.name');
    $q->addtable($xartable['rolemembers'],'rm');
    $q->join('r.id','rm.id');
    $q->eq('rm.parentid',xarModVars::get('vendors','defaultgroup'));
    if (!$q->run()) return;
    return $q->output();
}

?>
