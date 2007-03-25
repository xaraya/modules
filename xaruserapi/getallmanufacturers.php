<?php
/**
 * Get all manufacturers
 *
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @subpackage Vendors module
 */
/**
 * get all manufacturers
 * @author Marc Lutolf <mfl@netspan.ch>
 * @returns array
 * @return array of manufacturers, or empty array on failure
 */

function vendors_userapi_getallmanufacturers($args)
{
    extract($args);

    $xartable =& xarDBGetTables();
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
    $parent = xarFindRole('Manufacturers');
    $q->eq('rm.parentid',$parent->getID());
    if (!$q->run()) return;
    return $q->output();
}

?>
