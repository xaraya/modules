<?php
/**
 * Shopping Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shopping
 * @author Shopping module development team 
 */

/**
 * initialise block
 */
function shopping_shoppingcartblock_init()
{
    return true;
} 

/**
 * get information on block
 */
function shopping_shoppingcartblock_info()
{ 
    // Values
    return array('text_type' => 'shoppingcart',
        'module' => 'shopping',
        'text_type_long' => 'View shopping cart state',
        'allow_multiple' => false,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
} 

/**
 * display block
 */
function shopping_shoppingcartblock_display($blockinfo)
{ 
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']); 
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    $data = array();
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    // check to see if a user is logged in
    if (xarUserIsLoggedIn()) {
        // get the current user
        $uid = xarUserGetVar('uid');
        // get the items
        $data['cartitems'] = xarModAPIFunc('shopping', 'user', 'getallcart',
                                            array('uid' => $uid,
                                            'status' => 'cart'));
        if (!isset($data['cartitems']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

         
        // if there are items in the cart calulate the subtotal
        if ($data['cartitems'] != false) {
            $subtotal = 0;
            for ($i = 0; $i < count($data['cartitems']); $i++){
                $subtotal += $data['cartitems'][$i]['price'] * $data['cartitems'][$i]['quantity'];
                // format the price for display
                $data['cartitems'][$i]['price'] = round($data['cartitems'][$i]['price'], 2);
                $data['cartitems'][$i]['price'] = '$' . number_format($data['cartitems'][$i]['price'], 2, '.', '');
                
            }
            
            // format the subtotal for display
            $subtotal = round($subtotal, 2);
            $subtotal = '$' . number_format($subtotal, 2, '.', '');
            $data['subtotal'] = $subtotal;
        }
        
        // get images for delete and move
        $data['delimg'] = xarTplGetImage('delete.gif');
        $data['wishimg'] = xarTplGetImage('wish.gif');
        $data['cartimg'] = xarTplGetImage('wish.gif');
        
        // get urls for delete, move, and update
        $data['delurl'] = xarModURL('shopping', 'user', 'deletecart');
        $data['movewishurl'] = xarModURL('shopping', 'user', 'movecart', array('kind' => 1));
        $data['movecarturl'] = xarModURL('shopping', 'user', 'movecart', array('kind' => 0));
        $data['updateurl'] = xarModURL('shopping', 'user', 'modifycart');
    }
    else {
      // set var to displat login message
      $data['notlogged'] = true;
      
      // get total number of item added while anonymous
      $totalSessionItems = xarSessionGetVar("NumItems");
      if ($totalSessionItems > 0) {
          $subtotal = 0;
          for($i = 1; $i <= $totalSessionItems; $i++) {
              // get session vars into other vars to aviod having to call the function more than once
              $sessID = xarSessionGetVar("Item.$i.ID");
              $sessQuan = xarSessionGetVar("Item.$i.Quantity");
              $sessKind = xarSessionGetVar("Item.$i.Kind");
              
              // get the name and price of the item
              $sessItem = xarModAPIFunc('shopping', 'user', 'getallitems',
              array('where' => array('xar_iid' => array('=' => $sessID))));
              if (!is_array($sessItem)) return false;
              $sessName = $sessItem[0]['name'];
              $sessPrice = $sessItem[0]['price'];
              
              // if the kind is 0 it is a cart item, 1 is a wishlist item
              if ($sessKind == 0) {
                  $data['cartitems'][] = array(
                      'iid' => $sessID,
                      'quantity' => $sessQuan,
                      'name' => $sessName,
                      'price' => $sessPrice);
                  // unformat the price and calculate the subtotal
                  $ufprice = eregi_replace('\$', '', $sessPrice);
                  $subtotal += $ufprice * $sessQuan;
              }
          }
          $data['subtotal'] = '$' . number_format($subtotal, 2, '.', '');
          
          // if there were no cart or wishlist item, the respective var must be set to false
          if (!isset($data['cartitems'])) {
              $data['cartitems'] = false;
          }
      } 
      else {
          $data['cartitems'] = false;
      }
        
    }
    
    $data['blockid'] = $blockinfo['bid'];
    
    // Lets find out the template that we are sending the data to.
    if (empty($blockinfo['template'])) {
        $template = 'shoppingcart';
    } else {
        $template = $blockinfo['template'];
    }
    // Now we need to send our output to the template.
    $blockinfo['content'] = xarTplBlock('shopping', $template, $data);
    return $blockinfo;
}

?>