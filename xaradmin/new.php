<?php
/**
   Add new item
   This is a standard function that is called whenever an administrator
   wishes to create a new module item

   @param itemtype - number specifing the type of the object (required)
   @return template data
*/
function maxercalls_admin_new($args)
{
    // Security check
    if (!xarSecurityCheck('addmaxercalls')) return;

    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch().
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, $itemtype, XARVAR_GET_OR_POST)) return;

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'new', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    //$data['menu']      = xarModFunc('maxercalls','admin','menu');
    //$data['menutitle'] = xarModAPIFunc('maxercalls','admin','menu');

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module'   => 'maxercalls',
                                           'itemtype' => $itemtype )
                                    );
    if (!isset($data['object'])) return;  // throw back

    // Lets take care of hooks now
    $item = array();
    $item['module'] = 'maxercalls';
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';
    $data['itemtype'] = $itemtype;

    // Return the template variables defined in this function
    return $data;
}

?>
