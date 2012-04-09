<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the wurfl module
 *
 */

    sys::import('xaraya.structures.query');

    function wurfl_init()
    {

        xarRegisterMask('ViewWurfl','All','wurfl','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadWurfl','All','wurfl','All','All','ACCESS_READ');
        xarRegisterMask('CommentWurfl','All','wurfl','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateWurfl','All','wurfl','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditWurfl','All','wurfl','All','All','ACCESS_EDIT');
        xarRegisterMask('AddWurfl','All','wurfl','All','All','ACCESS_ADD');
        xarRegisterMask('ManageWurfl','All','wurfl','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminWurfl','All','wurfl','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewWurfl','All','wurfl','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadWurfl','All','wurfl','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentWurfl','All','wurfl','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateWurfl','All','wurfl','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditWurfl','All','wurfl','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddWurfl','All','wurfl','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageWurfl','All','wurfl','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminWurfl','All','wurfl','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'wurfl'));
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('wurfl', 'defaultmastertable','wurfl_wurfl');

    # --------------------------------------------------------
    #
    # Set up hooks
    #

        return true;
    }

    function wurfl_upgrade()
    {
        return true;
    }

    function wurfl_delete()
    {
        $this_module = 'wurfl';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
