<?php

/**
 * the main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.  As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 */
function contact_admin_main()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  For the
    // main function we want to check that the user has at least edit privilege
    // for some item within this component, or else they won't be able to do
    // anything and so we refuse access altogether.  The lowest level of access
    // for administration depends on the particular module, but it is generally
    // either 'edit' or 'delete'
    if (!xarSecurityCheck('ContactAdd')) return;


    // If you want to go directly to some default function, instead of
    // having a separate main function, you can simply call it here, and
    // use the same template for admin-main.xard as for admin-view.xard
    // return xarModFunc('contact','admin','view');

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('contact','admin','menu');

    // Specify some other variables used in the blocklayout template
    $data['welcome'] = xarML('Contact Module Administrative Menu');
    $data['companyinfo'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=new">Add Company</a>');
    $data['addcity'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_city">Add Cities</a>');
    $data['addlocation'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_location">Add Location Types</a>');
    $data['addtitles'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_titles">Titles</a>');
    $data['adddepartments'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_departments">Add Departments</a>');
    $data['persons'] = xarML('<strong><u>Contacts</u></strong>');
    $data['addpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=add_contact">Add Contact Persons</a>');
    $data['contactmenu'] = xarML('<a href="index.php?module=contact&amp;type=admin">Contacts Menu</a>');
    $data['listpersons'] = xarML('<a href="index.php?module=contact&amp;type=admin&amp;func=list_contact">Edit Contact Persons</a>');
    // Return the template variables defined in this function
    return $data;

    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    //
    // return array('menutitle' => ...,
    //              'welcome' => ...,
    //              ... => ...);
}

?>