<?php
/**
 * File: $Id$
 *
 * Decode the short URLs for Julian
 *
 * @package julian
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link  link to information for the subpackage
 * @author Julian development Team
 */

/**
 * Decode the short URLs in Julian
 *
 * The parameters are taken from the URL and coupled to functions
 *
 * @author  Julian Development Team
 * @deprec  date since deprecated <insert this if function is deprecated>
 * @access  public
 * @param   the URL
 * @return  array
 * @todo    MichelV. <#> Replace by Xaraya function
 */

function julian_userapi_getMonthNameShort($args)
{
    extract($args); unset($args);
    if(!isset($month)) $month = date('m');

    // make sure we have a valid month value
    if(!xarVarValidate('int:1:12',$month)) {
        return;
    }
    $c = xarModAPIFunc('julian','user','factory','julian');
    return $c->MonthShort($month);
}

?>