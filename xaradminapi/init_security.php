<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

function gallery_adminapi_init_security($args)
{
    if( !xarModIsHooked('security', 'gallery', 1) )
    {
        $result = xarModAPIFunc('modules','admin','enablehooks',
            array(
                'callerModName' => 'gallery',
                'callerItemType' => 1, // Album
                'hookModName' => 'security'
            )
        );
    }

    if( !xarModIsHooked('security', 'gallery', 2) )
    {
        $result = xarModAPIFunc('modules','admin','enablehooks',
            array(
                'callerModName' => 'gallery',
                'callerItemType' => 2, // photo
                'hookModName' => 'security'
            )
        );
    }

    /**
     * Defaults
     */
    $settings = SecuritySettings::factory(xarModGetIDFromName('gallery'), 1);

    $settings->default_item_levels['user'] = new SecurityLevel(1,1,1,1,0,0);
    $settings->default_item_levels[0] = new SecurityLevel(1,1,0,0,0,0);
    $settings->default_group_level = new SecurityLevel(1,1,0,0,0,0);

    xarModAPILoad('gallery');
    $xartable =& xarDBGetTables();
    $album_table = $xartable['gallery_albums'];
    $files_table = $xartable['gallery_files'];

    $settings->owner_table = $album_table;
    $settings->owner_column = 'uid';
    $settings->owner_primary_key = 'album_id';
    $settings->save();


    $settings->itemtype = 2;
    $settings->owner_table = $files_table;
    $settings->owner_column = 'uid';
    $settings->owner_primary_key = 'file_id';
    $settings->save();

    return true;
}
?>