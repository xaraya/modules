<?php
/**
 * Dynamic Data Example Module  Table Creation
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function labAccounting_xartables()
{
    // Initialise table array
    $xartable = array();
    
    $journals = xarDBGetSiteTablePrefix() . '_labaccounting_journals';
    $xartable['labaccounting_journals'] = $journals;
    
    $journaltransactions = xarDBGetSiteTablePrefix() . '_labaccounting_journaltransactions';
    $xartable['labaccounting_journaltransactions'] = $journaltransactions;
    
    $ledgers = xarDBGetSiteTablePrefix() . '_labaccounting_ledgers';
    $xartable['labaccounting_ledgers'] = $ledgers;
    
    $ledgertransactions = xarDBGetSiteTablePrefix() . '_labaccounting_ledgertransactions';
    $xartable['labaccounting_ledgertransactions'] = $ledgertransactions;
    
    $journalXledger = xarDBGetSiteTablePrefix() . '_labaccounting_journalXledger';
    $xartable['labaccounting_journalXledger'] = $journalXledger;
    
    return $xartable;
}

?>