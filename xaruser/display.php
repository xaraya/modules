<?php

/**
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param $args an array of arguments (if called by other modules)
 * @param $args['objectid'] a generic object id (if called by other modules)
 * @param $args['exid'] the item id used for this events module
 */
function events_user_display($args)
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya.
    // Note that for retrieving several parameters, we use list($var1,$var2) =
    list($exid,
         $objectid) = xarVarCleanFromInput('exid',
                                          'objectid');

    // User functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *after* we have obtained any form-based input through
    // xarVarCleanFromInput().
    extract($args);

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $exid
    //
    // Note that this module could just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $exid = $objectid;
    }

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModFunc('events', 'user', 'menu');

    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';

    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the get() function will fail if the user does not
    // have at least READ access to this item (also see below).
    $item = xarModAPIFunc('events',
                          'user',
                          'get',
                          array('exid' => $exid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check 2 - if your API function does *not* check for the
    // appropriate access rights, or if for some reason you require higher
    // access than READ for this function, you *must* check this here !
    // if (!xarSecAuthAction(0, 'Eventss::', "$item[name]::$item[exid]",
    //        ACCESS_COMMENT)) {
    //    // Fill in the status variable with the status to be shown
    //    $data['status'] = _EXAMPLENOAUTH;
    //    // Return the template variables defined in this function
    //    return $data;
    //}

    // Let any transformation hooks know that we want to transform some text.
    // You'll need to specify the item id, and an array containing all the
    // pieces of text that you want to transform (e.g. for autolinks, wiki,
    // smilies, bbcode, ...).
    list($item['name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['exid'],
                                         array($item['name']));

    // Fill in the details of the item.  Note that a module variable is used here to determine
    // whether or not parts of the item information should be displayed in
    // bold type or not
    $data['name_label'] = xarML('EXAMPLENAME');
    $data['name_value'] = $item['name'];
    $data['number_label'] = xarML('EXAMPLENUMBER');
    $data['number_value'] = $item['number'];

    $data['is_bold'] = xarModGetVar('events', 'bold');
    // Note : module variables can also be specified directly in the
    // blocklayout template by using &xar-mod-<modname>-<varname>;

    // Note that you could also pass on the $item variable, and specify
    // the labels directly in the blocklayout template. But make sure you
    // use the <xar:ml>, <xar:mlstring> or <xar:mlkey> tags then, so that
    // labels can be translated for other languages...


    // Save the currently displayed item ID in a temporary variable cache
    // for any blocks that might be interested (e.g. the Others block)
    // You should use this -instead of globals- if you want to make
    // information available elsewhere in the processing of this page request
    xarVarSetCached('Blocks.events', 'exid', $exid);

    // Let any hooks know that we are displaying an item.  As this is a display
    // hook we're passing a URL as the extra info, which is the URL that any
    // hooks will show after they have finished their own work.  It is normal
    // for that URL to bring the user back to this function
    $hooks = xarModCallHooks('item',
                             'display',
                             $exid,
                             xarModURL('events',
                                       'user',
                                       'display',
                                       array('exid' => $exid)));
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } elseif (is_array($hooks)) {
    // You can also use the output from individual hooks in your template,
    // e.g. with $hooks['comments'], $hooks['hitcount'], $hooks['ratings'] etc.
        $data['hookoutput'] = join('',$hooks);
    } else {
        $data['hookoutput'] = $hooks;
    }

    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarModGetVar('themes', 'SiteName').' :: '.
                       xarVarPrepForDisplay(xarML('Events'))
               .' :: '.xarVarPrepForDisplay($item['name']));

    // Return the template variables defined in this function
    return $data;

    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :
    //
    // return array('menu' => ...,
    //              'item' => ...,
    //              'hookoutput' => ...,
    //              ... => ...);
}

?>