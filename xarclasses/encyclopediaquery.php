<?php
/**
 * Query object for the encyclopedia module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

include_once 'DynamicDataQuery.php';

class EncyclopediaQuery extends DynamicDataQuery
{

    var $object;
//---------------------------------------------------------
// Constructor
//---------------------------------------------------------
    function EncyclopediaQuery()
    {
        parent::DynamicDataQuery(xarModGetVar('encyclopedia','encyclopediaid'));
    }
}
?>