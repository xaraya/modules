<?php

/**
 * get a the sub-items of an article
 *
 * @param  $args ['aid'] id of article
 * @param  $args ['ptid'] id of article
 * @returns array
 * @return item array, or false on failure
 */
function subitems_userapi_getarticlesubitems($args)
{
    extract($args);
	
	// Get Sub-Items' Object ID of the Pubtype
    $subitemArgs =  array();   
    $subitemArgs['module'] =  'articles';   
    $subitemArgs['itemtype'] =  $ptid;   
    $ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$subitemArgs);

	// Get the Object from DD
    $ddobjectArgs =  array();   
    $ddobjectArgs['objectid'] =  $ddobjectlink['objectid'];   
    $ddobjectArgs['status'] =  1;   
    $ddobject =  xarModAPIFunc('dynamicdata','user','getobject',$ddobjectArgs);


	// Get SubItem IDs
    $subitemIDArgs =  array();   
    $subitemIDArgs['objectid'] =  $ddobjectlink['objectid'];   
    $subitemIDArgs['itemid'] =  $aid;   
    $itemIDs =  xarModAPIFunc('subitems','user','dditems_getids',$subitemIDArgs);

	// Get Actual Subitems
    $subitemArgs =  array();   
    $subitemArgs['modid'] =  $ddobject->moduleid;   
    $subitemArgs['itemtype'] =  $ddobject->itemtype;   
    $subitemArgs['itemids'] =  $itemIDs;   
    $subitemInfo =  xarModAPIFunc('dynamicdata','user','getitems',$subitemArgs);

	return $subitemInfo;
}

/*
<!-- Get DD Object ID -->
<xar:set name="$subitemArgs">array()</xar:set>   
<xar:set name="$subitemArgs['module']">'articles'</xar:set>   
<xar:set name="$subitemArgs['itemtype']">13</xar:set>   
<xar:set name="$subitemArgs['itemid']">$product</xar:set>   
<xar:set name="$ddobjectlink">#xarModAPIFunc('subitems','user','ddobjectlink_get',$subitemArgs)#</xar:set>

<!-- Get DD Object Definition -->
<xar:set name="$ddobjectArgs">array()</xar:set>   
<xar:set name="$ddobjectArgs['objectid']">$ddobjectlink['objectid']</xar:set>   
<xar:set name="$ddobjectArgs['status']">1</xar:set>   
<xar:set name="$ddobject">#xarModAPIFunc('dynamicdata','user','getobject',$ddobjectArgs)#</xar:set>


<!-- Get ID of the OGCInfo Article -->
<xar:set name="$ogcinfoArgs['module']">'articles'</xar:set> 
<xar:set name="$ogcinfoArgs['itemtype']">13</xar:set> 
<xar:set name="$ogcinfoArgs['where']">'title eq '.$product</xar:set>
<!---    <xar:set name="$ddlistArgs['fieldlist']">array('publisher')</xar:set>--->
<xar:set name="$ogcinfo">#xarModAPIFunc('articles','user','getall',$ogcinfoArgs)#</xar:set>


<!-- Get SubItem IDs -->
<xar:set name="$subitemIDArgs">array()</xar:set>   
<xar:set name="$subitemIDArgs['objectid']">$ddobjectlink['objectid']</xar:set>   
<xar:set name="$subitemIDArgs['itemid']">$ogcinfo[0]['aid']</xar:set>   
<xar:set name="$itemIDs">#xarModAPIFunc('subitems','user','dditems_getids',$subitemIDArgs)#</xar:set>

<!-- Get Actual Subitems -->

<xar:set name="$subitemArgs">array()</xar:set>   
<xar:set name="$subitemArgs['modid']">$ddobject->moduleid</xar:set>   
<xar:set name="$subitemArgs['itemtype']">$ddobject->itemtype</xar:set>   
<xar:set name="$subitemArgs['itemids']">$itemIDs</xar:set>   
<xar:set name="$subitemInfo">#xarModAPIFunc('dynamicdata','user','getitems',$subitemArgs)#</xar:set>
*/


?>	