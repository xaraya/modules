<?php /**
 * flickring module - wraps phpFlickr
 */
function flickring_init() {

	$module ='flickring';

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ReadFlickring','All',$module,'All','All','ACCESS_READ');

# --------------------------------------------------------
#
# Set up modvars
#
    xarModSetVar($module, 'key', '');

# --------------------------------------------------------
#
# Register block types
#
    // Register the flickr blocktype
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => $module,
                              'blockType' => 'flickr'))) return;

    // Initialisation successful
    return true;
}

/**
 * delete the flickring module
 * @return bool
 */
function flickring_delete() {

	$module ='flickring';

# --------------------------------------------------------
#
# Remove modvars, masks and privilege instances
#
    xarRemoveMasks($module);
    xarRemoveInstances($module);
    xarModDelAllVars($module);

# --------------------------------------------------------
#
# Remove block types
#
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                        array('modName' => $module,
                              'blockType' => 'flickr'))) return;

    // Deletion successful
    return true;
}
?>
