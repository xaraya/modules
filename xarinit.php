<?php
/**
 * ITSP initialization functions
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Initialise the module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author ITSP Module Development Team
 */
function itsp_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

/*
Database considerations, taken from specification on project website
The main trick to this will be to have some way to configure a basis plan, with rules to meet.
Some parts of this database might be better handled by dynamic data; a mix might also be usefull.

Table: itsp_plans
This table holds the general plans

-planid
-name of plan
-description
-rules?: free, open, for what students?
-Total credits to meet
-minimum credits to meet?
-dates opening/closing?
*/
    $planstable = $xartable['itsp_plans'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_planid      I         AUTO       PRIMARY,
               xar_planname    C(100)    NotNull    DEFAULT '',
               xar_plandesc    X         NotNull    DEFAULT '',
               xar_planrules   C(255)    NotNull    DEFAULT '',
               xar_credits     I         NotNull    DEFAULT 0,
               xar_mincredit   I         NotNull    DEFAULT 0,
               xar_dateopen    I(11)     Null       DEFAULT NULL,
               xar_dateclose   I(11)     Null       DEFAULT NULL,
               xar_datemodi    I(11)     Null       DEFAULT NULL,
               xar_modiby      I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($planstable, $fields);
    if (!$result) {return;}

/*
Table: itsp_planitems
Table with planitems: the building blocks. These are the blocks a student can use to form his own ITSP. These blocks need to be filled in with courses or other items, depending on the type of planitem.

-ID
-Name item
-Minimum Credits to meet
-Rule(s) which courses can be involved, or which open items can be added. This dictates what table to get the courses from.
Ruleformat: (compare to privileges) coursetype:Level:Category:internal/external/open
"All" in the rule determines All
*/
    $planitemstable = $xartable['itsp_planitems'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_pitemid     I         AUTO       PRIMARY,
               xar_pitemname   C(100)    NotNull    DEFAULT '',
               xar_pitemdesc   X         NotNull    DEFAULT '',
               xar_pitemrules  C(255)    NotNull    DEFAULT '',
               xar_credits     I         NotNull    DEFAULT 0,
               xar_mincredit   I         NotNull    DEFAULT 0,
               xar_dateopen    I(11)     Null       DEFAULT NULL,
               xar_dateclose   I(11)     Null       DEFAULT NULL,
               xar_datemodi    I(11)     Null       DEFAULT NULL,
               xar_modiby      I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($planitemstable, $fields);
    if (!$result) {return;}


/*
Table: itsp_planlinks
Table with links between plan and planitems. Planitems can be reused.

-ID plan
-ID planitem
*/
    $planlinkstable = $xartable['itsp_planlinks'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_pitemid  I         NotNull    DEFAULT 0,
               xar_planid   I         NotNull    DEFAULT 0,
               xar_datemodi I(10)     Null       DEFAULT NULL,
               xar_modiby   I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($planlinkstable, $fields);
    if (!$result) {return;}
/*

Table: itsp_itsp
Table with the ITSP: the entry point for students. Will the supervision be included in here?

- ITSP id
- a userid, preferably the xar uid
- PlanID: which plan is this itsp about?
-date creation
-date submission
-itsp status
-date approval
-date certificate requested
-date certificate awarded
*/
    $itsptable = $xartable['itsp_itsp'];

    /* Get a data dictionary object with all the item create methods in it */
    //TODO: Status types?
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_itspid           I         AUTO       PRIMARY,
               xar_userid           I         NotNull    DEFAULT 0,
               xar_planid           I         NotNull    DEFAULT 0,
               xar_itspstatus       C(255)    NotNull    DEFAULT '',
               xar_datesubm         I(11)     Null       DEFAULT NULL,
               xar_dateappr         I(11)     Null       DEFAULT NULL,
               xar_datecertreq      I(11)     Null       DEFAULT NULL,
               xar_datecertaward    I(11)     Null       DEFAULT NULL,
               xar_datemodi         I(11)     Null       DEFAULT NULL,
               xar_modiby           I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($itsptable, $fields);
    if (!$result) {return;}
/*

Table: itsp_itsp_courselinks

There are two types: fixed courses and courses that are added in a free form (courses not taken from the courses module). This table only deals with courses from the courses module.

- LinkedCourseID: the rest should be coming from the hooked item.
- PlanitemID:
-approval: is this gonna be part of the ITSP?

*/
    $courselinkstable = $xartable['itsp_itsp_courselinks'];

    /* Get a data dictionary object with all the item create methods in it */
    // Table with courses added to the ITSP by students
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_courselinkid     I         AUTO       PRIMARY,
               xar_lcourseid        I         NotNull    DEFAULT 0,
               xar_itspid           I         NotNull    DEFAULT 0,
               xar_pitemid          I         NotNull    DEFAULT 0,
               xar_dateappr         I(11)     Null       DEFAULT NULL,
               xar_datemodi         I(11)     Null       DEFAULT NULL,
               xar_modiby           I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($courselinkstable, $fields);
    if (!$result) {return;}

/*
Table: itsp_itsp_courses
This table deals with the free courses. So: how to add the custom courses/items to the itsp of a student?

- itspcourseID Item ID
- PlanItemId
- Title of course item
- School/location of course
- credits involved
- startdate
- level
- contact (text. Like URL or person)
- result (standard 0=planned. Then a mark of status can be added).
- approval (is this item to be in the ITSP?)
*/
    $icoursestable = $xartable['itsp_itsp_courses'];

    /* Get a data dictionary object with all the item create methods in it */
    // Table with courses added to the ITSP by students
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $fields = "xar_icourseid        I         AUTO       PRIMARY,
               xar_pitemid          I         NotNull    DEFAULT 0,
               xar_itspid           I         NotNull    DEFAULT 0,
               xar_icoursetitle     C(255)    NotNull    DEFAULT '',
               xar_icourseloc       C(255)    NotNull    DEFAULT '',
               xar_icoursedesc      X         NotNull    DEFAULT '',
               xar_icoursecredits   I         NotNull    DEFAULT 0,
               xar_icourselevel     C(255)    NotNull    DEFAULT '',
               xar_icourseresult    C(255)    NotNull    DEFAULT '',
               xar_icoursedate      I(11)     Null       DEFAULT NULL,
               xar_dateappr         I(11)     Null       DEFAULT NULL,
               xar_datemodi         I(11)     Null       DEFAULT NULL,
               xar_modiby           I         NotNull    DEFAULT 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($icoursestable, $fields);
    if (!$result) {return;}

    /* If and as necessary create indexes for your tables */
    /*
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_itsp_number',
        $itsptable,
        'xar_number'
    );
    if (!$result) {return;}
    */

    /* If Categories API loaded and available, generate proprietary
     * module master category cid and child subcids
     */
    if (xarModIsAvailable('categories')) {
        $itspcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'itsps',
                'description' => 'ITSP Categories',
                'parent_id' => 0));
        /* Note: you can have more than 1 mastercid (cfr. articles module) */
        xarModSetVar('itsp', 'number_of_categories', 1);
        xarModSetVar('itsp', 'mastercids', $itspcid);
        $itspcategories = array();
        $itspcategories[] = array('name' => "Eduction plan one",
            'description' => "description one");
        $itspcategories[] = array('name' => "Education plan two",
            'description' => "description two");
        $itspcategories[] = array('name' => "Education plan three",
            'description' => "description three");
        foreach($itspcategories as $subcat) {
            $itspsubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $itspcid));
        }
    }
    /* Set up an initial value for a module variable.  Note that all module
     * variables should be initialised with some value in this way rather
     * than just left blank, this helps the user-side code and means that
     * there doesn't need to be a check to see if the variable is set in
     * the rest of the code as it always will be
     */
    xarModSetVar('itsp', 'bold', 0);
    xarModSetVar('itsp', 'itemsperpage', 10);
    /* If your module supports short URLs, the website administrator should
     * be able to turn it on or off in your module administration.
     * Use the standard module var name for short url support.
     */
    xarModSetVar('itsp', 'SupportShortURLs', 0);
    /* If you provide short URL encoding functions you might want to also
     * provide module aliases and have them set in the module's administration.
     * Use the standard module var names for useModuleAlias and aliasname.
     */
    xarModSetVar('itsp', 'useModuleAlias',false);
    xarModSetVar('itsp', 'aliasname','');

    /* Register our hooks that we are providing to other modules.  The itsp
     * module shows an itsp hook in the form of the user menu.
     */
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'itsp', 'user', 'usermenu')) {
        return false;
    }
    /* Register search hook */
    if (!xarModRegisterHook('item', 'search', 'GUI', 'itsp', 'user', 'search')) {
        return false;
    }
    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */

    /* Instance definitions serve two purposes:
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

    $query3 = "SELECT DISTINCT xar_name FROM " . $itsptable;
    $query2 = "SELECT DISTINCT xar_number FROM " . $itsptable;
    $query1 = "SELECT DISTINCT xar_planid FROM " . $planstable;
    $instances = array(
        array('header' => 'ITSP id:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'ITSP Number:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'ITSP ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('itsp', 'Item', $instances);
     */
    /* You can also use some external "wizard" function to specify instances :

      $instances = array(
          array('header' => 'external', // this keyword indicates an external "wizard"
                'query'  => xarModURL('itsp','admin','privileges',array('foo' =>'bar')),
                'limit'  => 0
          )
      );
      xarDefineInstance('itsp', 'Item', $instances);

     */
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'itsp'";
    $instances = array(
        array('header' => 'ITSP Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('itsp', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadITSPBlock', 'All', 'itsp', 'Block', 'All', 'ACCESS_OVERVIEW');
    // The ITSP seen from the student.
    // $itspid:$planid
    // TODO: add comment level
    xarRegisterMask('ViewITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminITSP', 'All', 'itsp', 'ITSP', 'All:All:All', 'ACCESS_ADMIN');
    // Let's seperate for the plans for now
    // $planid:$pitemid:
    //xarRegisterMask('ReadITSPBlock', 'All', 'itsp', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminITSPPlan', 'All', 'itsp', 'Plan', 'All:All:All', 'ACCESS_ADMIN');

    /* Initialisation successful so return true */
    return true;
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times
 */
function itsp_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case '0.1':
             return itsp_upgrade('1.0.0');
        case '1.0.0':
            break;
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool
 */
function itsp_delete()
{
    /* Get database setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    // Initialise table array
    $basename = 'itsp';

    foreach(array('plans', 'planitems', 'planlinks', 'itsp', 'itsp_courselinks', 'itsp_courses') as $table) {

    /* Drop the tables */
     $result = $datadict->dropTable($xartable[$basename . '_' . $table]);
    }
    /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('itsp','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='itsp')){
        xarModDelAlias($aliasname,'itsp');
    }

    /* Delete any module variables */
    xarModDelAllVars('itsp');

    /* Unregister each of the hooks that have been created */
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'itsp', 'user', 'usermenu')) {
        return false;
    }
    /* Remove Masks and Instances */
    xarRemoveMasks('itsp');
    xarRemoveInstances('itsp');

    /* Deletion successful*/
    return true;
}
?>
