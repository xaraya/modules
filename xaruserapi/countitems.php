<?php
/**
 * Count entries in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

include_once 'modules/encyclopedia/xarclasses/encyclopediaquery.php';

function encyclopedia_userapi_countitems($args)
{
    extract($args);

    $letterget = isset($letterget) ? $letterget : "";
    $searchtype = isset($searchtype) ? $searchtype : "";

    $q = new EncyclopediaQuery();
    if ($letterget != "") {
        $q->eq('vid',$vid);
        if($letterget == "Other") $q->regex('term', "^[1-9]");
        elseif($letterget == "All") {}
        else $q->like('term', '$letterget%');
    }
    else if ($searchtype != "") {
        $q->like($searchtype, '%$term%');
        if( $vid != "allvols" ) $q->eq('vid',$vid);
    }
    else {
        if (!$q->run()) return;
    }

// Future improvement needed
    return count($q->output());
}

?>