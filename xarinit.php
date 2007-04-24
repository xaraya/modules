<?php
/**
 * MP3 Jukebox Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php/
 * @author MP3 Jukebox Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author MP3 Jukebox Module Development Team
 * @param none
 * @return bool true on success of installation
 */
function mp3jukebox_init()
{
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you
     * are using - $table doesn't cut it in more complex modules
     */
    $mp3jukebox_songs_table = $xartable['mp3jukebox_songs'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Define the table structure in a string, each field and it's description
     * separated by a comma. The key for the element is the physical field name.
     * Each field descripton contains other data specifying the
     * data type and associated parameters
     */

    $fields = "xar_songid           I       AUTO        PRIMARY,
                xar_song_filesize   I       NotNull     DEFAULT 0,
                xar_sequence        I       NotNull     DEFAULT 0,
                xar_createdate      I       NotNull     DEFAULT 0,
                xar_status          C(32)   NotNull     DEFAULT '',
                xar_song_name       C(128)  NotNull     DEFAULT '',
                xar_song_filename   C(128)  NotNull     DEFAULT '',
                xar_artist_name     C(128)  NotNull     DEFAULT '',
                xar_album_name      C(128)  NotNull     DEFAULT '',
                xar_song_filetype   C(32)   NotNull     DEFAULT 'audio/mpeg3'
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($mp3jukebox_songs_table, $fields);
    if (!$result) {return;}

    /* If and as necessary create indexes for your tables */
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_mp3jukebox_name',
        $mp3jukebox_songs_table,
        'xar_song_name'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_mp3jukebox_artist',
        $mp3jukebox_songs_table,
        'xar_artist_name'
    );
    if (!$result) {return;}
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_mp3jukebox_album',
        $mp3jukebox_songs_table,
        'xar_album_name'
    );
    if (!$result) {return;}

    /*  PLAYLISTS TABLE */
    $mp3jukebox_playlists_table = $xartable['mp3jukebox_playlists'];

    $fields = "xar_playlistid   I       AUTO        PRIMARY,
                xar_uid         I       NotNull     DEFAULT 0,
                xar_private     I1      NotNull     DEFAULT 0,
                xar_featured    I1      NotNull     DEFAULT 0,
                xar_createdate  I       NotNull     DEFAULT 0,
                xar_status      C(32)   NotNull     DEFAULT 0,
                xar_title       C(128)  NotNull     DEFAULT ''
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($mp3jukebox_playlists_table, $fields);
    if (!$result) {return;}


    /* PLAYLIST SONGS TABLE */
    $mp3jukebox_playlist_songs_table = $xartable['mp3jukebox_playlist_songs'];

    $fields = "xar_playlistid   I       NotNull     DEFAULT 0,
                xar_songid      I       NotNull     DEFAULT 0,
                xar_sequence    I       NotNull     DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($mp3jukebox_playlist_songs_table, $fields);
    if (!$result) {return;}

    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_mp3jukebox_playlistid',
        $mp3jukebox_playlist_songs_table,
        'xar_playlistid'
    );


    /* If Categories API loaded and available, generate proprietary
     * module master category if (cid) and child category ids (subcids)
     */
    if (xarModIsAvailable('categories')) {
        $mp3jukeboxcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'mp3jukebox',
                'description' => 'MP3 Jukebox Categories',
                'parent_id' => 0));
        /* Store the generated master category id and the number of possible categories
         * Note: you can have more than 1 mastercid (cfr. articles module)
         */
        xarModSetVar('mp3jukebox', 'number_of_categories', 1);
        xarModSetVar('mp3jukebox', 'mastercids', $mp3jukeboxcid);

        $mp3jukeboxcategories = array();

        $mp3jukeboxcategories[] = array('name' => "Big Band",
            'description' => "Big Band");
        $mp3jukeboxcategories[] = array('name' => "Blues",
            'description' => "Blues");
        $mp3jukeboxcategories[] = array('name' => "Classical",
            'description' => "Classical");
        $mp3jukeboxcategories[] = array('name' => "Country",
            'description' => "Country");
        $mp3jukeboxcategories[] = array('name' => "Electronic",
            'description' => "Electronic");
        $mp3jukeboxcategories[] = array('name' => "Funk",
            'description' => "Funk");
        $mp3jukeboxcategories[] = array('name' => "Gospel",
            'description' => "Gospel");
        $mp3jukeboxcategories[] = array('name' => "Hip Hop",
            'description' => "Hip Hop");
        $mp3jukeboxcategories[] = array('name' => "Jazz",
            'description' => "Jazz");
        $mp3jukeboxcategories[] = array('name' => "Latin",
            'description' => "Latin");
        $mp3jukeboxcategories[] = array('name' => "Metal",
            'description' => "Metal");
        $mp3jukeboxcategories[] = array('name' => "Pop",
            'description' => "Pop");
        $mp3jukeboxcategories[] = array('name' => "Punk",
            'description' => "Punk");
        $mp3jukeboxcategories[] = array('name' => "R & B",
            'description' => "R & B");
        $mp3jukeboxcategories[] = array('name' => "Rap",
            'description' => "Rap");
        $mp3jukeboxcategories[] = array('name' => "Reggae",
            'description' => "Reggae");
        $mp3jukeboxcategories[] = array('name' => "Rock",
            'description' => "Rock");
        $mp3jukeboxcategories[] = array('name' => "Ska",
            'description' => "Ska");

        foreach($mp3jukeboxcategories as $subcat) {
            $mp3jukeboxsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $mp3jukeboxcid));
        }
    }
    /* Set up an initial value for a module variable. Note that all module
     * variables should be initialised with some value in this way rather
     * than just left blank, this helps the user-side code and means that
     * there doesn't need to be a check to see if the variable is set in
     * the rest of the code as it always will be
     */
    xarModSetVar('mp3jukebox', 'itemsperpage', 20);
    xarModSetVar('mp3jukebox', 'playlistsperuser', 5);
    xarModSetVar('mp3jukebox', 'songsperplaylist', 100);

    /* If your module supports short URLs, the website administrator should
     * be able to turn it on or off in your module administration.
     * Use the standard module var name for short url support.
     */
    xarModSetVar('mp3jukebox', 'SupportShortURLs', 0);

    /* Register Block types (this *should* happen at activation/deactivation) */
//    if (!xarModAPIFunc('blocks',
//            'admin',
//            'register_block_type',
//            array('modName' => 'mp3jukebox',
//                'blockType' => 'others'))) return;
    /* Register blocks */
//    if (!xarModAPIFunc('blocks',
//            'admin',
//            'register_block_type',
//            array('modName' => 'mp3jukebox',
//                'blockType' => 'first'))) return;
    /* Register our hooks that we are providing to other modules. The example
     * module shows an example hook in the form of the user menu.
     */
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'mp3jukebox', 'user', 'usermenu')) {
        return false;
    }

    /*
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     *
     * Instance definitions serve two purposes:
     * 1. The define "filters" that are added to masks at runtime, allowing us to set
     *    security checks over single objects or groups of objects
     * 2. They generate dropdowns the UI uses to present the user with choices when
     *    definng or modifying privileges.
     * For each component we need to tell the system how to generate
     * a list (dropdown) of all the component's instances.
     * In addition, we add a header which will be displayed for greater clarity, and a number
     * (limit) which defines the maximum number of rows a dropdown can have. If the number of
     * instances is greater than the limit (e.g. registered users), the UI instead presents an
     * input field for manual input, which is then checked for validity.
     */
    $query1 = "SELECT DISTINCT xar_songid FROM " . $mp3jukebox_songs_table;
    $query2 = "SELECT DISTINCT xar_song_name FROM " . $mp3jukebox_songs_table;
    $query3 = "SELECT DISTINCT xar_artist_name FROM " . $mp3jukebox_songs_table;
    $query4 = "SELECT DISTINCT xar_album_name FROM " . $mp3jukebox_songs_table;
    $instances = array(
        array('header' => 'Song ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Song Name:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Artist:',
            'query' => $query3,
            'limit' => 20
            ),
        array('header' => 'Album:',
            'query' => $query4,
            'limit' => 20
            ),
        );
    xarDefineInstance('mp3jukebox', 'Song', $instances);

    $query1 = "SELECT DISTINCT xar_playlistid FROM " . $mp3jukebox_playlists_table;
    $query2 = "SELECT DISTINCT xar_title FROM " . $mp3jukebox_playlists_table;
    $query3 = "SELECT DISTINCT xar_uid FROM " . $mp3jukebox_playlists_table;
    $instances = array(
        array('header' => 'Playlist ID:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Title:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'User ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('mp3jukebox', 'Playlist', $instances);

    /* You can also use some external "wizard" function to specify instances
     * You will need to provide the wizard function in admin_privileges :

      $instances = array(
          array('header' => 'external', // this keyword indicates an external "wizard"
                'query'  => xarModURL('mp3jukebox','admin','privileges',array('foo' =>'bar')),
                'limit'  => 0
          )
      );
      xarDefineInstance('mp3jukebox', 'Item', $instances);

    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_name FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'mp3jukebox'";
    $instances = array(
        array('header' => 'MP3 Jukebox Block Name:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('mp3jukebox', 'Block', $instances);
     */

    /*
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     * These masks are used in the module for the security checks
     */
    /* First for the blocks */
//    xarRegisterMask('ReadMP3JukeboxBlock', 'All', 'mp3jukebox', 'Block', 'All', 'ACCESS_OVERVIEW');
    /* Then for all operations */
    xarRegisterMask('ViewMP3Jukebox',   'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadMP3Jukebox',   'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditMP3Jukebox',   'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddMP3Jukebox',    'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteMP3Jukebox', 'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminMP3Jukebox',  'All', 'mp3jukebox', 'Song', 'All:All:All', 'ACCESS_ADMIN');

    xarRegisterMask('ViewMP3Jukebox',   'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadMP3Jukebox',   'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditMP3Jukebox',   'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddMP3Jukebox',    'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteMP3Jukebox', 'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminMP3Jukebox',  'All', 'mp3jukebox', 'Playlist', 'All:All:All', 'ACCESS_ADMIN');

    /* This init function brings our module to version 1.0, run the upgrades for the rest of the initialisation */
    return mp3jukebox_upgrade('1.0');
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times. It holds all the routines for each version
 * of the module that are necessary to upgrade to a new version. It is very important to keep the
 * initialisation and the upgrade compatible with eachother.
 *
 * @author MP3 Jukebox Module Development Team
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function mp3jukebox_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
        case '1.0.0':
            /* Code to upgrade from version 1.0.0 goes here */
            /* Register search hook */
            if (!xarModRegisterHook('item', 'search', 'GUI', 'mp3jukebox', 'user', 'search')) {
               return false;
            }
            /* If you provide short URL encoding functions you might want to also
             * provide module aliases and have them set in the module's administration.
             * Use the standard module var names for useModuleAlias and aliasname.
             */
            xarModSetVar('mp3jukebox', 'useModuleAlias',false);
            xarModSetVar('mp3jukebox','aliasname','');
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author MP3 Jukebox Module Development Team
 * @param none
 * @return bool true on succes of deletion
 */
function mp3jukebox_delete()
{
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $mp3jukebox_songs = $xartable['mp3jukebox_songs'];
    $mp3jukebox_playlists = $xartable['mp3jukebox_playlists'];
    $mp3jukebox_playlist_songs = $xartable['mp3jukebox_playlist_songs'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the mp3jukebox tables */
    $result = $datadict->dropTable($mp3jukebox_songs);
    $result = $datadict->dropTable($mp3jukebox_playlists);
    $result = $datadict->dropTable($mp3jukebox_playlist_songs);

    /* Remove any module aliases before deleting module vars
     * This Assumes one module alias in this case
     */
    $aliasname = xarModGetVar('mp3jukebox','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias == 'mp3jukebox')){
        xarModDelAlias($aliasname,'mp3jukebox');
    }

    /* Delete any module variables */
    xarModDelAllVars('mp3jukebox');

    /* UnRegister all blocks that the module uses*/
/*    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'mp3jukebox',
                'blockType' => 'first'))) return;

*/
    /* Unregister each of the hooks that have been created */
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'mp3jukebox', 'user', 'usermenu')) {
        return false;
    }
    /* Remove Masks and Instances
     * These functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('mp3jukebox');
    xarRemoveInstances('mp3jukebox');

    /* Category deletion?
     *
     * Categories can be used in more than one module.
     * The categories originally created for this module could also have been used
     * for other modules. If we delete the categories then we must be sure that
     * no other modules are currently using them.
     */

    /* Deletion successful*/
    return true;
}
?>
