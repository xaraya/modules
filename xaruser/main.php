<?php

/*/
 * main user function
 * called when user clicks the module link
 *
 * @redirects user to main user page
/*/
function shopping_user_main()
{
    $params = array();
    if (xarModIsHooked('categories', 'shopping') && xarModGetVar('shopping', 'mastercids') != "") {
      $params = array('cids' => array(xarModGetVar('shopping', 'featurecat')));
    } 
    return xarModFunc('shopping', 'user', 'showitems',$params);
}
?>
