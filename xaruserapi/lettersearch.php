<?php
/**
 * Search for specific entries
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_userapi_lettersearch($args)
{
    $items = array(array());
    if (!xarSecurityCheck('ReadEncyclopedia')) {return $items;}

    extract($args);

    // Bail if there is no current query
    if (!isset($cs)) return $items;

// Apparently there is a current query. So lets set the filtering stuff
// By design we allow the letter filter and search criterium to be independant.
// That is, each is applied regardless of whether the other is present

    // First, set the letter filter if it exists
    if (isset($letterget) && $letterget != "" && $letterget != "All")
    {
        if ($letterget == "Other") $cs->regexp('term','\"^[^a-z]\'');
        else $cs->like('term',$letterget . '%');
    }


    // Now set the search criterium.
    if(isset($searchtype)) {
        // Are we searching term and/or definition?
        if ($searchtype == "both") {
            $c[1] = $cs->like('term','%' . $search . '%');
            $c[2] = $cs->like('definition','%' . $search . '%');
            $cs->qor($c);
        } else {
            $cs->like($searchtype,'%' . $search . '%');
        }
    }

    // We finished assembling the query. Now run it.
    if(!$cs->run()) return;

    if (xarModGetVar('Encyclopedia', 'longdisplay') == 1) {
        return $cs->output();
    } else {
        foreach($cs->output() as $item) {
//            if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',$term . "::" . $id))
                $items[] = $item;
        }
        return $items;
    }
}
?>