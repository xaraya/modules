<?php
/**
 * File: navigator/xarinit.php
 *
 * navigator initialization functions
 *
 * @copyright (C) 2003 Charles and Helen Schwab Foundation.
 * @license unreleased
 * @link http://xavier.schwabfoundation.org
 * @subpackage navigator
 * @author CHSF Dev Team <xavier@schwabfoundation.org>
 */

/**
 * initialise the navigator module
 */
function navigator_init()
{
    /**
     * Register Privilege masks
     */

    /*
       make sure there isn't any crap left
       over from a previous install...
    */

    navigator_delete();

    /* Privileg Masks and Instances */
    xarRegisterMask('ViewNavigatorMenuItem', 'All', 'navigator', 'Item', 'All:All', 'ACCESS_READ', 'Ability to view menu item.');
    xarRegisterMask('ViewNavigatorMenu',     'All', 'navigator', 'Menu', 'All', 'ACCESS_READ', 'Ability to view navigation menu');
    xarRegisterMask('ViewNavigatorBlock',    'All', 'navigator', 'Block','All', 'ACCESS_READ', 'Ability to view navigation block based menu');
    xarRegisterMask('AdminNavigatorBlock',   'All', 'navigator', 'Block','All', 'ACCESS_ADMIN','Ability to administrate navigation block based menu');
    xarRegisterMask('AdminNavigator',        'All', 'navigator', 'All',  'All', 'ACCESS_ADMIN','Ability to administrate navigator module');

    $xartable =& xarDBGetTables();
    $instances[0]['header'] = 'external';
    $instances[0]['query']  = xarModURL('navigator', 'admin', 'privileges', array('privtype' => 'menu'));
    $instances[0]['limit']  = 0;
    xarDefineInstance('navigator', 'Menu', $instances);

    $xartable =& xarDBGetTables();
    $instances[0]['header'] = 'external';
    $instances[0]['query']  = xarModURL('navigator', 'admin', 'privileges', array('privtype' => 'item'));
    $instances[0]['limit']  = 0;
    xarDefineInstance('navigator', 'Menu Item', $instances);

    $query = "SELECT DISTINCT instances.xar_title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.xar_id = instances.xar_type_id WHERE xar_module = 'navigator'";
    $instances[0]['header'] = 'Navigator Menu Block Title:';
    $instances[0]['query']  = $query;
    $instances[0]['limit']  = 20;
    xarDefineInstance('navigator','Block',$instances);

    
    /* Custom Tags */
    $optional_string  = XAR_TPL_OPTIONAL | XAR_TPL_STRING;
    $optional_integer = XAR_TPL_OPTIONAL | XAR_TPL_INTEGER;

    $id           = new xarTemplateAttribute('id',          $optional_string);
    $base         = new xarTemplateAttribute('base',        $optional_string);
    $type         = new xarTemplateAttribute('type',        $optional_string);
    $rename       = new xarTemplateAttribute('rename',      $optional_string);
    $exclude      = new xarTemplateAttribute('exclude',     $optional_string);
    $maxdepth     = new xarTemplateAttribute('maxdepth',    $optional_integer);
    $intersects   = new xarTemplateAttribute('intersects',  $optional_string);
    $emptygroups  = new xarTemplateAttribute('emptygroups', $optional_string);

    $attr['image']         = array($id);
    $attr['location']      = array($id, $type);
    $attr['menu']          = array($id, $base, $type, $exclude, $rename,
                                   $maxdepth, $intersects, $emptygroups);

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => 'navigator',
                              'blockType' => 'jsnav'))) return;

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => 'navigator',
                              'blockType' => 'caption'))) return;

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => 'navigator',
                              'blockType' => 'newsletter_sub'))) return;

    xarTplRegisterTag('navigator', 'navigator-menu',
                      $attr['menu'],
                      'navigator_userapi_handle_menu_tag');

    xarTplRegisterTag('navigator', 'navigator-image',
                      $attr['image'],
                      'navigator_userapi_handle_image_tag');

    xarTplRegisterTag('navigator', 'navigator-location',
                      $attr['location'],
                      'navigator_userapi_handle_location_tag');

    // Initialisation successful
    return true;
}

/**
 * upgrade the navigator module from an old version
 */
function navigator_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                                array('modName' => 'navigator',
                                      'blockType' => 'jsnav'))) return;

            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                                array('modName' => 'navigator',
                                      'blockType' => 'newsletter_sub'))) return;
            break;
        case '1.0.1':
            xarRemoveMasks('navigator');
            xarRemoveInstances('navigator');
            xarRemovePrivileges('navigator');
            
            xarRegisterMask('ViewNavigatorMenuItem', 'All', 'navigator', 'Item', 'All:All', 'ACCESS_READ', 'Ability to view menu item.');
            xarRegisterMask('ViewNavigatorMenu',     'All', 'navigator', 'Menu', 'All', 'ACCESS_READ', 'Ability to view navigation menu');
            xarRegisterMask('ViewNavigatorBlock',    'All', 'navigator', 'Block','All', 'ACCESS_READ', 'Ability to view navigation block based menu');
            xarRegisterMask('AdminNavigatorBlock',   'All', 'navigator', 'Block','All', 'ACCESS_ADMIN','Ability to administrate navigation block based menu');
            xarRegisterMask('AdminNavigator',        'All', 'navigator', 'All',  'All', 'ACCESS_ADMIN','Ability to administrate navigator module');

            $xartable =& xarDBGetTables();
            $instances[0]['header'] = 'external';
            $instances[0]['query']  = xarModURL('navigator', 'admin', 'privileges', array('privtype' => 'menu'));
            $instances[0]['limit']  = 0;
            xarDefineInstance('navigator', 'Menu', $instances);

            $xartable =& xarDBGetTables();
            $instances[0]['header'] = 'external';
            $instances[0]['query']  = xarModURL('navigator', 'admin', 'privileges', array('privtype' => 'item'));
            $instances[0]['limit']  = 0;
            xarDefineInstance('navigator', 'Menu Item', $instances);

            $query = "SELECT DISTINCT instances.xar_title FROM $xartable[block_instances] as instances LEFT JOIN $xartable[block_types] as btypes ON btypes.xar_id = instances.xar_type_id WHERE xar_module = 'navigator'";
            $instances[0]['header'] = 'Navigator Menu Block Title:';
            $instances[0]['query']  = $query;
            $instances[0]['limit']  = 20;
            xarDefineInstance('navigator','Block',$instances);

        default:
            // Couldn't find a previous version to upgrade
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the navigator module
 */
function navigator_delete()
{



    // Remove privileges, security masks and instances
    xarRemoveMasks('navigator');
    xarRemoveInstances('navigator');
    xarRemovePrivileges('navigator');

    xarTplUnregisterTag('navigator-menu');
    xarTplUnregisterTag('navigator-image');
    xarTplUnregisterTag('navigator-location');

    xarModDelAllVars('navigator');

    if (!xarModAPIFunc('blocks', 'admin', 'delete_type',
                       array('modName' => 'navigator',
                             'blockType' => 'jsnav'))) return;

    if (!xarModAPIFunc('blocks', 'admin', 'delete_type',
                       array('modName' => 'navigator',
                             'blockType' => 'newsletter_sub'))) return;

    if (!xarModAPIFunc('blocks', 'admin', 'delete_type',
                       array('modName' => 'navigator',
                             'blockType' => 'caption'))) return;

    // Deletion successful
    return true;
}

?>
