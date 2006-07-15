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

    $xartable =& xarDBGetTables();
    if (isset($conditions)) {
    	$conditions->addtable($xartable['roles'],'r');
    	$q = $conditions;
    } else {
		$q = new xenQuery('SELECT',$xartable['roles'],'r');
	}
	$q->addfield('r.xar_uid');
	$q->addfield('r.xar_name');
	$q->setorder('r.xar_name');
	$q->addtable($xartable['rolemembers'],'rm');
	$q->join('r.xar_uid','rm.xar_uid');
	$parent = xarFindRole('Suppliers');
	$q->eq('rm.xar_parentid',$parent->getID());
    if (!$q->run()) return;
    return $q->output();
}

?>
