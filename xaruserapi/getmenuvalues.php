<?php
/**
 * AddressBook userapi getMenuValues
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * getMenuValues - performs the URL retrieval of data
 *
 * @param GET / POST params from viewall from
 * @return array $menuValues
 */
function addressbook_userapi_getMenuValues()
{
    $menuValues = array();

    if (!xarVarFetch ('total','int::',          $menuValues['total'],FALSE)) return;
    if (!xarVarFetch ('sortview','int::',       $menuValues['sortview'], 0)) return;
    if (!xarVarFetch ('formSearch','str::60',   $menuValues['formSearch'], '')) return;
    if (!xarVarFetch ('all','int::',            $menuValues['all'], 1)) return;
    if (!xarVarFetch ('page','int::',           $menuValues['page'], 1)) return;
    if (!xarVarFetch ('char','str:1:1',         $menuValues['char'], FALSE)) return;
    if (!xarVarFetch ('catview','int::',        $menuValues['catview'], 0)) return;
    if (!xarVarFetch ('menuprivate','int::',    $menuValues['menuprivate'], 0)) return;

    // used in the form to signal a state change of the private menu
    $menuValues['menuprivate_fl'] = $menuValues['menuprivate'];

    return $menuValues;

} // END getMenuValues

?>
