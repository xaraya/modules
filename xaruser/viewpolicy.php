<?php
/*/
 * viewpolicy function
 * displays shipping and return policy
 *
 * @returns template variables
/*/
function shopping_user_viewpolicy()
{
    // security check
    if (!xarSecurityCheck('ViewShopping')) return;

    $data = array();
    // FIXME: See if we can  use this in templates, so it is translateable
    $data['spolicy'] = xarModGetVar('shopping', 'spolicy');
    $data['rpolicy'] = xarModGetVar('shopping', 'rpolicy');

    return $data;
}
?>