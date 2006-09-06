<?php /**
 * flickring module - wraps phpFlickr
 */
 
/**
 * Initialize the module
 */
function flickring_init() {

	$module ='flickring';

    // --------------------------------------------------------
    // Set up masks
    xarRegisterMask('ReadFlickring','All',$module,'All','All','ACCESS_READ');
    xarRegisterMask('AdminFlickring','All',$module,'All','All','ACCESS_ADMIN');

    // --------------------------------------------------------
    // Set up modvars
    xarModSetVar($module, 'key', '');
    xarModSetVar($module, 'secret', '');

    // --------------------------------------------------------
    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => $module,
                              'blockType' => 'flickr'))) return;

    // Initialisation successful
    return true;
}

/**
 * Upgrade the module
 *
 * @param oldversion
 */
function flickring_upgrade($oldversion)
{
    
}

/**
 * Delete the module
 * @return bool
 */
function flickring_delete() {

	$module ='flickring';

    // --------------------------------------------------------
    // Remove modvars, masks and privilege instances
    xarRemoveMasks($module);
    xarRemoveInstances($module);
    xarModDelAllVars($module);

    // --------------------------------------------------------
    // Remove block types
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                        array('modName' => $module,
                              'blockType' => 'flickr'))) return;

    // Deletion successful
    return true;
}
?>
