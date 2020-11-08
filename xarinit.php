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
        xarMasks::register('ViewCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_OVERVIEW');
        xarMasks::register('ReadCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_READ');
        xarMasks::register('CommentCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_COMMENT');
        xarMasks::register('ModerateCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_MODERATE');
        xarMasks::register('EditCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_EDIT');
        xarMasks::register('AddCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_ADD');
        xarMasks::register('ManageCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_DELETE');
        xarMasks::register('AdminCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Set up privileges
        #
        xarPrivileges::register('ViewCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_OVERVIEW');
        xarPrivileges::register('ReadCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_READ');
        xarPrivileges::register('CommentCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_COMMENT');
        xarPrivileges::register('ModerateCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_MODERATE');
        xarPrivileges::register('EditCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_EDIT');
        xarPrivileges::register('AddCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_ADD');
        xarPrivileges::register('ManageCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_DELETE');
        xarPrivileges::register('AdminCKEditor', 'All', 'ckeditor', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Set up modvars
        #
        //xarModVars::set('ckeditor', 'itemsperpage', 20);
        //xarModVars::set('ckeditor', 'useModuleAlias',0);
        //xarModVars::set('ckeditor', 'aliasname','CKEditor');
        //xarModVars::set('ckeditor', 'defaultmastertable','ckeditor_ckeditor');

        if (strstr(realpath(sys::varpath()), '/')) {
            $str = '/uploads';
        } else {
            $str = '\uploads';
        }
        $PGRFileManager_rootPath = realpath(sys::varpath()) . $str;
        $PGRFileManager_urlPath = xarServer::getBaseURL() . 'var/uploads';
 
        xarModVars::set('ckeditor', 'PGRFileManager_rootPath', $PGRFileManager_rootPath);
        xarModVars::set('ckeditor', 'PGRFileManager_urlPath', $PGRFileManager_urlPath);
        xarModVars::set('ckeditor', 'PGRFileManager_allowedExtensions', 'pdf, txt, rtf, jpg, gif, jpeg, png');
        xarModVars::set('ckeditor', 'PGRFileManager_imagesExtensions', 'jpg, gif, jpeg, png, bmp');
        xarModVars::set('ckeditor', 'PGRFileManager_fileMaxSize', 1024 * 1024 * 10);
        xarModVars::set('ckeditor', 'PGRFileManager_imageMaxHeight', 724);
        xarModVars::set('ckeditor', 'PGRFileManager_imageMaxWidth', 1280);
        xarModVars::set('ckeditor', 'PGRFileManager_allowEdit', 'true');

        xarMod::apiFunc('ckeditor', 'admin', 'modifypluginsconfig', array(
            'name' => 'PGRFileManager.rootPath',
            'value' => $PGRFileManager_rootPath
            ));
        xarMod::apiFunc('ckeditor', 'admin', 'modifypluginsconfig', array(
            'name' => 'PGRFileManager.urlPath',
            'value' => $PGRFileManager_urlPath
            ));

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
        return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', array('module' => $this_module));
    }
