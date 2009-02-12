<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_xartables()
{
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the labcontest item table. This is not necessary
     * but helps in the following statements and keeps them readable
     */
    $labaffiliate_programs_table = xarDBGetSiteTablePrefix() . '_labaffiliate_programs';
    $labaffiliate_affiliates_table = xarDBGetSiteTablePrefix() . '_labaffiliate_affiliates';
    $labaffiliate_membership_table = xarDBGetSiteTablePrefix() . '_labaffiliate_membership';

    /* Set the table name */
    $xarTables['labaffiliate_programs'] = $labaffiliate_programs_table;
    $xarTables['labaffiliate_affiliates'] = $labaffiliate_affiliates_table;
    $xarTables['labaffiliate_membership'] = $labaffiliate_membership_table;

    /* Return the table information */
    return $xarTables;
}
?>