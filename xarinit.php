<?php
/**
 *
 * Initialise or remove the ckeditor module
 *
 */

    function ckeditor_init()
    {

    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewCKEditor','All','ckeditor','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadCKEditor','All','ckeditor','All','All','ACCESS_READ');
        xarRegisterMask('CommentCKEditor','All','ckeditor','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateCKEditor','All','ckeditor','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditCKEditor','All','ckeditor','All','All','ACCESS_EDIT');
        xarRegisterMask('AddCKEditor','All','ckeditor','All','All','ACCESS_ADD');
        xarRegisterMask('ManageCKEditor','All','ckeditor','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminCKEditor','All','ckeditor','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewCKEditor','All','ckeditor','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadCKEditor','All','ckeditor','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentCKEditor','All','ckeditor','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateCKEditor','All','ckeditor','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditCKEditor','All','ckeditor','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddCKEditor','All','ckeditor','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageCKEditor','All','ckeditor','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminCKEditor','All','ckeditor','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('ckeditor', 'itemsperpage', 20);
        xarModVars::set('ckeditor', 'useModuleAlias',0);
        xarModVars::set('ckeditor', 'aliasname','CKEditor');
        xarModVars::set('ckeditor', 'defaultmastertable','ckeditor_ckeditor');
        xarModVars::set('ckeditor', 'editorversion', 'ckeditor');

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
    //    xarModVars::set('ckeditor', 'bar', 'Bar');

        return true;
    }

    function ckeditor_upgrade()
    {
        return true;
    }

    function ckeditor_delete()
    {
        $this_module = 'ckeditor';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
