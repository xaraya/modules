<?php
/**
 * File: $Id:
 * 
 * Extract function and arguments from short URLs for this module
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * 
 * @author the Example module development team 
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function xarcpshop_userapi_decode_shorturl($params)
{ 
    // Initialise the argument list we will return
    $args = array(); 

    $thisurl=parse_url($_SERVER['REQUEST_URI']);
    if (isset($thisurl['query'])) {
        $zoomquery =$thisurl['query'];
    }else{
        $zoomquery='';
    }

    if (isset($thisurl['fragment'])){
      $thisfragment=$thisurl['fragment'];
    }else{
        $thisfragment='';
    }

    if (isset($thisurl['path'])){
       $thispath=$thisurl['path'];
    }else{
        $thispath='';
    }

  //  var_dump($_SERVER['REQUEST_URI']);
    $module = 'xarcpshop';
    if ($params[0] != $module) { //it's possibly some type of alias
        $alias = xarModGetAlias($params[0]);
         if ($module == $alias) {
            // yup, looks like it
            $shops = xarModAPIFunc('xarcpshop','user','getall');
            foreach ($shops as $id => $shop) {
                if ($params[0] == $shop['name']) {
                     $args['storeid'] = (int)$shop['storeid'];
                    break;
                } //ok - it's an alias but might be using 'publication'!
            }
        }
    }
    $shops = xarModAPIFunc('xarcpshop','user','getall');

    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);
     }elseif (!empty($params[1]) && (preg_match('/^(\w+)/',$params[1],$matches))) {
         // something that starts with this is a shop name
        $shopandid=explode(".",$params[1]);
        $storename = $shopandid[0];
        foreach ($shops as $id => $shop) {
            if ($storename == $shop['name']) {
                $id = $shop['storeid'];
            } //ok - it's an alias but might be using 'publication'!
        }

        if (isset($shopandid[1])) {
            $args['item'] = $shopandid[1];
        }else{
            $args['item'] = '';
        }

        if (isset($thisfragment) && ($thisfragment =='top')) {
           $args['id'] = $params[1].'#'.$thisfragment;
        // can go straight to the top of the store
        }elseif ($zoomquery<>''){
         $args['zoom'] = 'yes';
         $args['zoomquery']=$zoomquery;
         $args['id'] = $params[1].'?'.$zoomquery;
        }else{
         $args['id'] = $params[1];
        }
        return array('main', $args);
    } else {

    }
    // default : return nothing -> no short URL decoded
}

?>
