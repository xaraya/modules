<?php
/**
 * Search handler for autodoc module
 *
 * A few words on what's going on here:
 * This is the item:search:api hook function for the autodoc 
 * module. Each kind of this function is supposed to do its
 * search thing specific for it's containing module and return
 * an array of search-results with keys: result, link, itemtype and optionally 
 * description. The search module then renders that array in it's search
 * results template.
 *
 * Each search api function gets two parameters from the search module: terms
 * and itemtypes. The latter is an array which contains the itemtypes the
 * user wants to search in. The module itself should know what to do with these
 *
 * So:
 * $object_id -> when not 0 a specific item was specified (optional: only when an item is a 'collection' (see bkview))
 * $terms -> string with entered search terms
 * $itemtypes = array('itemtypename' => itemtypeid, ... ,)
 * 
 * $results = array(
 *                  array('result' => string describing the result,
 *                        'link'   => link to the result,
 *                        'itemtype' => in which itemtype was this found (text, not id)
 *                        'description' => longer result text
 *                        'context'     => some context if applicable, this enables highlighting of the results found
 *                       )
 *                  ...
 *                 )  
 *
 * This way of searching is the double whammy way, you have to hook search into autodoc (for the gui)
 * and autodoc into search (for delivering the results back to the search module)
 *               
 * @package modules
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage autodoc
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function autodoc_userapi_search($args) 
{
    extract($args);
    $itemtype_results = array(); $searchresults = array();
    if(!empty($itemtypes)) {
        foreach($itemtypes as $itemtype_name => $itemtype_id) {
            $itemtype_results = _ad_search($terms,$itemtype_id,$itemtype_name);
            $searchresults = array_merge($searchresults, $itemtype_results);
        }
    }
    return $searchresults;
}

function _ad_search($terms,$itemtype_id,$itemtype_name) {

    // Get the items, this delivers an array with the itemnames in the value
    // This stinks, cos we are in a different scope here now.
    $items = xarModApiFunc('autodoc','user','get',array('itemtype' => $itemtype_id));
    // For now, if the letters in terms are in the names, it's a hit
    $results = array();
    foreach($items as $id => $value) {
        if(strpos(strtolower($value), strtolower($terms)) !== false) {
            $results[] = array(
                               'result' => $value,
                               'itemtype' => $itemtype_name,
                               'link' => xarModUrl('autodoc','user','view',
                                                   array('scope' => 0,'itemtype'=>$itemtype_id,'itemid'  =>$id)),
                               'description' => '',
                               'context'     => ''                                                     
                               );
        }
    }
    //debug($results);
    return $results;
}
?>