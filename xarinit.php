<?php
/**
 * Module initilization
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */

/**
 * initialise the xarLinkMe module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarlinkme_init()
{
    /* Create tables - for later */
    /*
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xlmbannerstable = $xartable['xlm_banners'];

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "xar_bnid      I      AUTO       PRIMARY,
               xar_clid      I4     NotNull    DEFAULT 0,
               xar_btype     C(2)   NotNull    DEFAULT '',
               xar_imptotal  I4     NotNull    DEFAULT 0,
               xar_impmade   I4     NotNull    DEFAULT 0,
               xar_clicks    I4     NotNull    DEFAULT 0,
               xar_imageurl  C(255) NotNull    DEFAULT '',
               xar_clickurl  C(255) NotNull    DEFAULT '',
               xar_bdate     T      DEFTIMESTAMP
              ";

    $result = $datadict->changeTable($xlmbannerstable, $fields);
    if (!$result) {return;}


    $xlmbannersexpiredtable = $xartable['xlm_banners_expired'];

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "xar_bnid      I      AUTO       PRIMARY,
               xar_clid      I4     NotNull    DEFAULT 0,
               xar_impmade   I4     NotNull    DEFAULT 0,
               xar_clicks    I4     NotNull    DEFAULT 0,
               xar_datestart T      NotNull    DEFAULT 0,
               xar_dateend   T     NotNull    DEFAULT 0
              ";

    $result = $datadict->changeTable($xlmbannersexpiredtable, $fields);
    if (!$result) {return;}
    */

    // If Categories API loaded and available, generate proprietary
    // module master category cid and child subcids
    if (xarModIsAvailable('categories')) {
        $xarlinkmecid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'xarLinkMe',
                'description' => 'xarLinkMe Categories',
                'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('xarlinkme', 'number_of_categories', 1);
        xarModSetVar('xarlinkme', 'mastercids', $xarlinkmecid);
        $xarlinkmecategories = array();
        $xarlinkmecategories[] = array('name' => "Promotional",
            'description' => "General promotional banner ads");
        $xarlinkmecategories[] = array('name' => "Web Hosting",
            'description' => "Web hosting related");
        $xarlinkmecategories[] = array('name' => "Business",
            'description' => "Business related banner ads");
        foreach($xarlinkmecategories as $subcat) {
            $xarlinkmesubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $xarlinkmecid));
        }
    }

    /* Register blocks */
    /*
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'xarlinkme',
                'blockType' => 'banners'))) return;
    */
    /* Set up an initial value for a module variable.  Note that all module
     * variables should be initialised with some value in this way rather
     * than just left blank, this helps the user-side code and means that
     * there doesn't need to be a check to see if the variable is set in
     * the rest of the code as it always will be
     */

    xarModSetVar('xarlinkme', 'itemsperpage', 10);
    xarModSetVar('xarlinkme', 'imagedir', 'modules/xarlinkme/xarimages/linkads');
    xarModSetVar('xarlinkme', 'pagetitle', 'Banner and Link Codes');
    xarModSetVar('xarlinkme', 'instructions', 'The following are banners approved for external site linking. Copy the HTML code and paste it into your web page wherever you would like to add the banner link.');
    xarModSetVar('xarlinkme', 'instructions2', 'Choose from one of the banners below, or the Text Link at the end of the banner list.');
    xarModSetVar('xarlinkme', 'txtintro', 'If you would prefer a text link, we suggest the following:');
    xarModSetVar('xarlinkme', 'txtadlead', 'Go...');
    xarModSetVar('xarlinkme','allowlinking',true);
    xarModSetVar('xarlinkme', 'SupportShortURLs',false);
    xarModSetVar('xarlinkme', 'useModuleAlias',false);
    xarModSetVar('xarlinkme','aliasname','');
    xarModSetVar('xarlinkme', 'activebanners', false);
    xarModSetVar('xarlinkme','excludedips','');
     /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */
    xarRegisterMask('ReadxarLinkMeBlock', 'All', 'xarlinkme', 'Block', 'All', 'ACCESS_READ');
    xarRegisterMask('ViewxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeletexarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminxarLinkMe', 'All', 'xarlinkme', 'BannerItem', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the xarLinkMe module from an old version
 * This function can be called multiple times
 */
function xarlinkme_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.1':
            return xarlinkme_upgrade('0.1.0');
            break;
        case '0.1.0':
           /* Register new mask for block */
           xarRegisterMask('ReadxarLinkMeBlock', 'All', 'xarlinkme', 'Block', 'All', 'ACCESS_READ');

           /* setup the new mod vars */
            xarModSetVar('xarlinkme','allowlinking',true);
            xarModSetVar('xarlinkme', 'SupportShortURLs',false);
            xarModSetVar('xarlinkme', 'useModuleAlias',false);
            xarModSetVar('xarlinkme','aliasname','');
            xarModSetVar('xarlinkme', 'activebanners', false);
            xarModSetVar('xarlinkme','excludedips','');
            return xarlinkme_upgrade('0.2.0');

         case '0.2.0':
             /* Register the Banner block */
             /* later
             if (!xarModAPIFunc('blocks','admin','register_block_type',
            array('modName' => 'xarlinkme',
                'blockType' => 'banners'))) return;
            */
            /*create the tables */
            /* later
                $dbconn =& xarDBGetConn();
                $xartable =& xarDBGetTables();

                $xlmbannerstable = $xartable['xlm_banners'];

                $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

               $fields = "xar_bnid      I      AUTO       PRIMARY,
                          xar_clid      I4     NotNull    DEFAULT 0,
                          xar_btype     C(2)   NotNull    DEFAULT '',
                          xar_imptotal  I4     NotNull    DEFAULT 0,
                          xar_impmade   I4     NotNull    DEFAULT 0,
                          xar_clicks    I4     NotNull    DEFAULT 0,
                          xar_imageurl  C(255) NotNull    DEFAULT '',
                          xar_bdate     T      DEFTIMESTAMP
                          ";

               $result = $datadict->changeTable($xlmbannerstable, $fields);
               if (!$result) {return;}

               $xlmbannersexpiredtable = $xartable['xlm_banners_expired'];

               $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

               $fields = "xar_bnid      I      AUTO       PRIMARY,
                          xar_clid      I4     NotNull    DEFAULT 0,
                          xar_impmade   I4     NotNull    DEFAULT 0,
                          xar_clicks    I4     NotNull    DEFAULT 0,
                          xar_datestart T      NotNull    DEFAULT 0,
                          xar_dateend   T     NotNull    DEFAULT 0
                          ";

              $result = $datadict->changeTable($xlmbannersexpiredtable, $fields);
              if (!$result) {return;}
              */
        return xarlinkme_upgrade('0.3.0');
        case '0.3.0':
            // Code to upgrade from version 1.0 goes here
           break;
    }
    // Update successful
    return true;
}

/**
 * delete the xarLinkMe module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xarlinkme_delete()
{
   /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('xarlinkme','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='xarlinkme')){
        xarModDelAlias($aliasname,'xarlinkme');
    }
    /* delete all the other mod vars */
    xarModDelAllVars('xarlinkme');

    /* drop any tables */
    /* comment out until later
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xlmbannerstable = $xartable['xlm_banners'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $result = $datadict->dropTable($xlmbannerstable);
     */
     /*
     $xlmbannersexpiredtable = $xartable['xlm_banners_expired'];
     $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
     $result = $datadict->dropTable($xlmbannersexpiredtable);
     */
    // Remove Masks and Instances
    // these functions remove all the registered masks and instances of a module
    // from the database. This is not strictly necessary, but it's good housekeeping.
    xarRemoveMasks('xarlinkme');
    xarRemoveInstances('xarlinkme');

    // Deletion successful
    return true;
}

?>