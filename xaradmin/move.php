<?php

/**
 * Move a link
 *
 * @param id 'lid' the id for the link to move
 * @param id 'tid' the id for the type of link
 * @param string 'confirm' do the move?
 * @param string 'cancel' cancel the move
 * @return mixed array or void
 */
function autolinks_admin_move()
{
    // Takes a lid and allows it to be moved from one type to another.
    // Pass 1: takes a lid and displays options.
    // Pass 2: takes a lid and tid and displays confirm.
    // Pass 3: does the move.

    // Security Check
    if (!xarSecurityCheck('EditAutolinks')) {return;}

    // A link ID is mandatory. The rest depend on the stage.
    if (!xarVarFetch('lid', 'id', $lid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('tid', 'id', $tid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'str', $confirm, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('cancel', 'str', $cancel, NULL, XARVAR_NOT_REQUIRED)) {return;}

    $data = array();

    // Provide current link details.
    if (!empty($lid)) {
        // Get the link details.
        $link = xarModAPIfunc(
            'autolinks', 'user', 'get',
            array('lid' => $lid)
        );
        if (!$link) {return;}
    }

    // Get choice of links to move to.
    if (!empty($lid) && empty($tid)) {
        // Get the list of link types.
        $types = xarModAPIfunc('autolinks', 'user', 'getalltypes');

        if (!$types) {return;}

        // There must be at least two types if there is to be
        // somewhere to move to.
        if (count($types) < 2) {
            // TODO: raise user error.
            return;
        }

        // Remove the current link type.
        unset ($types[$link['tid']]);
    }

    // User cancelled at the last stage.
    if (!empty($lid) && !empty($cancel)) {
        // Jump back a stage.
        xarResponseRedirect(xarModURL('autolinks', 'admin', 'move', array('lid' => $lid)));
        return true;
    }

    // A type has been selected - get the details.
    if (!empty($lid) && !empty($tid)) {
        // Supply type details.
        $type = xarModAPIfunc('autolinks', 'user', 'gettype', array('tid' => $tid));
        if (!$type) {return;}
    }

    // Do the final move and show the user the result.
    if (!empty($lid) && !empty($tid) && !empty($confirm)) {
        // To move the link, we need to delete the old one and create a new one.

        // If DD is hooked, then we will try to move some DD property values too.

        if (xarModIsHooked('dynamicdata', 'autolinks', $link['itemtype'])) {
            $dd_data_old = xarModAPIfunc(
                'dynamicdata', 'user', 'getitem',
                array('module' => 'autolinks', 'itemtype' => $link['itemtype'], 'itemid' => $lid)
            );
        }

        // Delete the old one.
        $result = xarModAPIfunc('autolinks', 'admin', 'delete', array('lid' => $lid));
        if (!$result) {return;}

        // Change the type and disable it until the user has inspected it.
        $link['tid'] = $tid;
        $link['enabled'] = 0;

        // Create the new link.
        $lid = xarModAPIfunc(
            'autolinks', 'admin', 'create', $link
        );

        if (!$lid) {return;}

        // Now try and replace some of the DD data, where the field names match.
        // Get the link back first, since it has changed.
        $link = xarModAPIfunc('autolinks', 'user', 'get', array('lid'=>$lid));

        if (xarModIsHooked('dynamicdata', 'autolinks', $link['itemtype'])) {
            $dd_data_new = xarModAPIfunc(
                'dynamicdata', 'user', 'getitem',
                array('module' => 'autolinks', 'itemtype' => $link['itemtype'], 'itemid' => $link['lid'])
            );
        }

        // If the new and old object contain DD properties, then see if
        // any data should be moved across.
        if (is_array($dd_data_old) && is_array($dd_data_new)) {
            // Loop for the old object and move it to the new object.
            $dd_updates = false;
            foreach($dd_data_old as $name => $value) {
                // If a property in the new object shares a name, then copy the
                // data across. We are assuming here that the types are the same
                // or at least won't cause any problems when copied.
                if (isset($dd_data_new[$name])) {
                    $dd_data_new[$name] = $value;
                    $dd_updates = true;
                }
            }
            if ($dd_updates) {
                // Some data was copied - update the properties.
                // We won't test the result here yet, until we know how we could handle it.

                $result = xarModAPIfunc(
                    'dynamicdata', 'admin', 'update',
                    array(
                        'itemid' => $lid,
                        'itemtype' => $link['itemtype'],
                        'modid' => xarModGetIDFromName('autolinks'),
                        'values' => $dd_data_new
                    )
                );
            }
        }

        // Take the user to the results. The lid will have changed.
        xarResponseRedirect(xarModURL('autolinks', 'admin', 'modify', array('lid' => $lid)));
        return true;
    }

    // Set the various data details for the template.

    $data['authid'] = xarSecGenAuthKey();

    if (isset($types)) {
        $data['types'] = $types;
    }

    if (isset($tid)) {
        $data['tid'] = $tid;
        $data['type'] = $type;
    }

    if (isset($lid)) {
        $data['lid'] = $lid;
        $data['link'] = $link;
    }

    return $data;
}

?>
