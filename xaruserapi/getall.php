<?php
/**
 * Get all customers
 *
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @subpackage Customers module
 */
/**
 * get all customers
 * @author Marc Lutolf <mfl@netspan.ch>
 * @returns array
 * @return array of customers, or empty array on failure
 */

function customers_userapi_getall($args)
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
    $parent = xarFindRole('Customers');
    $q->eq('rm.parentid',$parent->getID());
    if (!$q->run()) return;
    return $q->output();
}

?>
