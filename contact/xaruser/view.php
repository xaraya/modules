<?php

function contact_user_view()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya.
    // Note that for retrieving 1 parameter, we can use $var1 = ... (see below)
    $startnum = xarVarCleanFromInput('startnum');

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('contact','user','menu');

    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';

    // Prepare the array variable that will hold all items for display
    $data['items'] = array();

    // Specify some other variables for use in the function template
    $data['someheader'] = xarML('CONTACTNAME');
    $data['pager'] = '';

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
       if (!xarSecurityCheck('ContactOverview')) return;

    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    $items = xarModAPIFunc('contact',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('contact',
                                                          'itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

// TODO: check for conflicts between transformation hook output and
//       xarVarCensor / xarVarPrepForDisplay
    // Loop through each item and display it.  Note the use of xarVarCensor() to
    // remove any words from the name that the administrator has deemed
    // unsuitable for the site
    foreach ($items as $item) {

        // Let any transformation hooks know that we want to transform some text
        // You'll need to specify the item id, and an array containing all the
        // pieces of text that you want to transform (e.g. for autolinks, wiki,
        // smilies, bbcode, ...).
        // Note : for your module, you might not want to call transformation
        // hooks in this overview list, but only in the display of the details
        // in the display() function.
        //list($item['name']) = xarModCallHooks('item',
        //                                     'transform',
        //                                     $item['exid'],
        //                                     array($item['name']));

        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
       if(xarSecurityCheck('ContactRead',0,'Item',"$item[firstname]:All:$item[id]")){
            $item['link'] = xarModURL('contact',
                                     'user',
                                     'display',
                                     array('id' => $item['id']));

            $item['email'] = xarModURL('contact',
                                      'user',
                                      'sendmail',
                                      array('id' => $item['id'],
                                            'firstname' => $item['firstname'],
                                            'lastname' => $item['lastname']));


        // Security check 2 - else only display the item name (or whatever is
        // appropriate for your module)
        } else {
            $item['link'] = '';
            $item['email'] = '';
        }

        // Clean up the item text before display
       // $item['firstname'] = xarVarPrepForDisplay(xarVarCensor($list['firstname']));

        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }

    // Specify some other variables for use in the function template
    $data['firstname_label'] = xarML('Firstname');
    $data['lastname_label'] = xarML('Lastname');
    $data['email_label'] = xarML('Email');
    $data['department_label'] = xarML('Department');
    $data['info_label'] = xarVarPrepForDisplay(xarML('Info'));
    $data['send_label'] = xarVarPrepForDisplay(xarML('Contact'));

    // Return the template variables defined in this function
    return $data;

    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    //
    // return array('menu' => ...,
    //              'items' => ...,
    //              'pager' => ...,
    //              ... => ...);
}

?>