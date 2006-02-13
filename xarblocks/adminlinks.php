<?php
/**
 * legis Block  - standard Initialization function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * legis Block  - standard Initialization function
 *
 * @author legis Module development team
 */
function legis_adminlinksblock_init()
{
    return array(
        'numitems'    => 5,
        'nocache'     => 0, /* cache by default (if block caching is enabled) */
        'pageshared'  => 1, /* share across pages */
        'usershared'  => 1, /* share across group members */
        'cacheexpire' => null
    );
}

/**
 * Get information on block
 */
function legis_adminlinksblock_info()
{
    /* Values */
    return array(
        'text_type' => 'Latest Legislation',
        'module' => 'legis',
        'text_type_long' => 'Latest Legislation',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display block
 */
function legis_adminlinksblock_display($blockinfo)
{ 
    /* Security check */
    if (!xarSecurityCheck('EditLegis', 0)) {return;}

//    if (!xarSecurityCheck('ReadLegisBlock', 0, 'Block', $blockinfo['title'])) {return;}

    /* Get variables from content block.
     * Content is a serialized array for legacy support, but will be
     * an array (not serialized) once all blocks have been converted.
     */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
   $hallsparent=xarModGetVar('legis','mastercids');
   $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));

    $data['halls']=$halls;

   if (!isset($defaulthall) || empty($defaulthall)) {
            /* First check for user mod var that is set for logged in user */
           if (xarUserIsLoggedIn()) {
               $uid = xarUserGetVar('uid');
               $defaulthall = (int)xarModGetUserVar('legis','defaulthall');
               if (!isset($defaulthall) || empty($defaulthall))  {
                //   $defaulthall = (int)xarSessionGetVar('legishall');
                  $defaulthall=(int)xarModGetVar('legis','defaulthall');                
               }
           }

           if (!xarUserIsLoggedIn()){
               /* try now for session */
               $defaulthall = (int)xarSessionGetVar('legishall');
               if (!isset($defaulthall) || empty($defaulthall))  {
                  $defaulthall=(int)xarModGetVar('legis','defaulthall');
                }
           }
            $defaulthalldata=$halls[$defaulthall];
        }

       if (!isset($defaulthalldata)) {
           $usehall=xarModGetVar('legis','defaulthall');
           $defaulthalldata=$halls[$usehall];
        }
    /* The API function is called.  The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the getall() function only returns items for which the
     * the user has at least OVERVIEW access.
     */
    $items = xarModAPIFunc(
        'legis', 'user', 'getall',
        array('numitems' => $vars['numitems'],
              'docstatus'=> 2,
              'dochall' =>$defaulthall)
    );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back

    /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
     * Loop through each item and display it.
     */
    $data['items'] = array();
    if (is_array($items)) {
        foreach ($items as $item) {

            if (xarSecurityCheck('ReadLegis', 0, 'Item', "All:All:$item[cdid]")) {
                $item['link'] = xarModURL('legis', 'user', 'display',
                    array('cdid' => $item['cdid'])
                ); 
                /* Security check 2 - else only display the item name (or whatever is
                 * appropriate for your module)
                 */
            } else {
                $item['link'] = '';
            }

            /* Add this item to the list of items to be displayed */
            $data['items'][] = $item;
        }
    }
    
    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $data['cansethall']=true;
    } else {
      $data['cansethall']=false;
    }

    $data['defaulthall']=$defaulthall;
    $data['defaulthalldata']=$defaulthalldata;
    $data['blockid'] = $blockinfo['bid'];

    /* Now we need to send our output to the template.
     * Just return the template data.
     */
    $blockinfo['content'] = $data;

    return $blockinfo;
} 
?>
