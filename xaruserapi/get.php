<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author andrea moro
 */
/**
 * Get subscribed sharecontent sites
 * @param @args['active'] if true get active websites
 * @return array of subscribed sites, or void
 */
function sharecontent_userapi_get($args)
{

    // Security Check
    if(!xarSecurityCheck('ReadSharecontentWeb',0)) {
	    return;
	}

	extract($args);
	if (!isset($active)) $active = 0;
	if ($active) {
	    $where='WHERE active= TRUE ';
    } else {
	    $where='';
    }

    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $sharecontenttable = $xartable['sharecontent'];
    // Get items
    $query = "SELECT id,title,homeurl,submiturl,image,active
              FROM $sharecontenttable
			  $where
			  ORDER BY title
	          ";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $websites= array();
    while (!$result->EOF) {
	    list($id,$title,$homeurl,$submiturl,$image,$xar_active) = $result->fields;
        $websites[$id] = array('title' => $title
		                       ,'homeurl' => $homeurl
		                       ,'submiturl' => $submiturl
		                       ,'image' => $image
							   ,'active' => $xar_active
							   );
        $result->MoveNext();
	}
	$result->close();

    return $websites;
}
?>
