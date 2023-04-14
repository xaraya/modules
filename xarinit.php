<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Initialise or remove the cacher module
 *
 */

sys::import('xaraya.structures.query');

function cacher_init()
{

# --------------------------------------------------------
#
# Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_cacher_caches";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_cacher_caches (
        id                integer unsigned NOT NULL auto_increment,
        name              varchar(254) NOT NULL default '', 
        directory         varchar(254) NOT NULL default '', 
        file_extension    varchar(10) NOT NULL default 'xc', 
        expiration_time   integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        state             tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id), 
        KEY i_tag_name (name)
    )";
    if (!$q->run($query)) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarMasks::register('ViewCacher',    'All','cacher','All','All','ACCESS_OVERVIEW');
    xarMasks::register('ReadCacher',    'All','cacher','All','All','ACCESS_READ');
    xarMasks::register('CommentCacher', 'All','cacher','All','All','ACCESS_COMMENT');
    xarMasks::register('ModerateCacher','All','cacher','All','All','ACCESS_MODERATE');
    xarMasks::register('EditCacher',    'All','cacher','All','All','ACCESS_EDIT');
    xarMasks::register('AddCacher',     'All','cacher','All','All','ACCESS_ADD');
    xarMasks::register('ManageCacher',  'All','cacher','All','All','ACCESS_DELETE');
    xarMasks::register('AdminCacher',   'All','cacher','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarPrivileges::register('ViewCacher',    'All','cacher','All','All','ACCESS_OVERVIEW');
    xarPrivileges::register('ReadCacher',    'All','cacher','All','All','ACCESS_READ');
    xarPrivileges::register('CommentCacher', 'All','cacher','All','All','ACCESS_COMMENT');
    xarPrivileges::register('ModerateCacher','All','cacher','All','All','ACCESS_MODERATE');
    xarPrivileges::register('EditCacher',    'All','cacher','All','All','ACCESS_EDIT');
    xarPrivileges::register('AddCacher',     'All','cacher','All','All','ACCESS_ADD');
    xarPrivileges::register('ManageCacher',  'All','cacher','All','All','ACCESS_DELETE');
    xarPrivileges::register('AdminCacher',   'All','cacher','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'cacher';
    $objects = array(
                    'cacher_caches'
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Set up modvars
#
    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'cacher'));
    $module_settings->initialize();

    // Add variables like this next one when creating utility modules
    // This variable is referenced in the xaradmin/modifyconfig-utility.php file
    // This variable is referenced in the xartemplates/includes/defaults.xd file
    xarModVars::set('cacher', 'debugmode', false);

# --------------------------------------------------------
#
# Set up hooks
#

    return true;
}

function cacher_upgrade()
{
    return true;
}

function cacher_delete()
{
    $this_module = 'cacher';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $this_module));
}

?>