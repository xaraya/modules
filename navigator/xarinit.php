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

    xarRegisterMask('ReadNavigator', 'All', 'navigator',
                    'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminNavigator', 'All', 'navigator',
                    'Item', 'All:All:All', 'ACCESS_ADMIN');

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

    $attr['inline-styles'] = array($id);
    $attr['image']         = array($id);
    $attr['location']      = array($id, $type);
    $attr['menu']          = array($id, $base, $type,
                                   $exclude, $rename,
                                   $maxdepth, $intersects,
                                   $emptygroups);

    xarTplRegisterTag('navigator', 'navigator-menu',
                      $attr['menu'],
                      'navigator_userapi_handle_menu_tag');

    xarTplRegisterTag('navigator', 'navigator-image',
                      $attr['image'],
                      'navigator_userapi_handle_image_tag');

    xarTplRegisterTag('navigator', 'navigator-location',
                      $attr['location'],
                      'navigator_userapi_handle_location_tag');

    xarTplRegisterTag('navigator', 'navigator-inline-styles',
                      $attr['inline-styles'],
                      'navigator_userapi_handle_inline_styles_tag');

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
            break;

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
    xarTplUnregisterTag('navigator-inline-styles');

    xarModDelAllVars('navigator');

    // Deletion successful
    return true;
}

?>
