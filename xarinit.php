<?php
/**
 * Scraper Module
 *
 * @package modules
 * @subpackage scraper
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Initialise or remove the scraper module
 *
 */

sys::import('xaraya.structures.query');

function scraper_init()
{

# --------------------------------------------------------
#
    # Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_scraper_urls";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_scraper_urls (
        id                integer unsigned NOT NULL auto_increment,
        name              varchar(254) NOT NULL default '', 
        url               varchar(254) NOT NULL default '', 
        code              text default '', 
        last_run          integer unsigned NOT NULL default 0, 
        last_results      integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        role_id           integer unsigned NOT NULL default 0, 
        state             tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id), 
        KEY i_tag_name (name)
    )";
    if (!$q->run($query)) {
        return;
    }

    # --------------------------------------------------------
#
    # Set up masks
#
    xarMasks::register('ViewScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_READ');
    xarMasks::register('CommentScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_COMMENT');
    xarMasks::register('ModerateScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_MODERATE');
    xarMasks::register('EditScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Set up privileges
#
    xarPrivileges::register('ViewScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_OVERVIEW');
    xarPrivileges::register('ReadScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_READ');
    xarPrivileges::register('CommentScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_COMMENT');
    xarPrivileges::register('ModerateScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_MODERATE');
    xarPrivileges::register('EditScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_EDIT');
    xarPrivileges::register('AddScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_ADD');
    xarPrivileges::register('ManageScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminScraper', 'All', 'scraper', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Create DD objects
#
    $module = 'scraper';
    $objects = [
                    'scraper_urls',
                     ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    # --------------------------------------------------------
#
    # Set up modvars
#
    $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'scraper']);
    $module_settings->initialize();

    // Add variables like this next one when creating utility modules
    // This variable is referenced in the xaradmin/modifyconfig-utility.php file
    // This variable is referenced in the xartemplates/includes/defaults.xt file
    xarModVars::set('scraper', 'defaultmastertable', 'scraper_urls');
    xarModVars::set('scraper', 'debugmode', false);

    # --------------------------------------------------------
#
    # Set up hooks
#

    return true;
}

function scraper_upgrade()
{
    return true;
}

function scraper_delete()
{
    $this_module = 'scraper';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $this_module]);
}
