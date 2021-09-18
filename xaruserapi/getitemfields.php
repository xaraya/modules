<?php

/**
 * utility function to pass item field definitions to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @returns array
 * @return array containing the item field definitions
 */

function xarpages_userapi_getitemfields($args)
{
    extract($args);

    $itemfields = [];

    /*
        $itemfields['pid']  = array(
            'name'  => 'pid',
            'label' => xarML('Page ID'),
            'type'  => 'itemid'
        );
    */

    $itemfields['name'] = [
        'name'  => 'name',
        'label' => xarML('Name'),
        'type'  => 'textbox',
    ];

    $itemfields['desc'] = [
        'name'  => 'desc',
        'label' => xarML('Description'),
        'type'  => 'textarea_small',
    ];

    // TODO: add other static xarpages fields here if relevant
    // JJ: I'd like to see how this works in practice before adding the further
    // columns.
    // What I don't understand is: why this import/export stuff is in the user API,
    // since there is a strong likelyhood of name clashes with third-party modules.
    // Moving the 'reserved' core inter-module API functions to separate APIs is
    // something we were going to do anyway - it was discussed and strongly agreed
    // on the committer lists.
    // Why not put them all in an import/export API? To that end, why APIs at all?
    // Can this not be defined as meta-data? It may take a little longer to design
    // the import/export functions at first, but it would start us off down the right
    // track. IMO - just a mini-rant :-)

    // Note: DD fields (if any) will be added automatically by the DD Migrate Items function

    return $itemfields;
}
