<?php

function timezone_init()
{
    // give us as much time as necessary as this may take a moment
    @set_time_limit(0);
    
    // grab the timezone array so we can create the SQL inserts
    require('modules/timezone/tzdata.php');
    // we do not yet support the LeapSeconds
    unset($Leaps);
    // Sort the arrays
    ksort($Rules);
    ksort($Zones);
    ksort($Links);
    
    // get a database connection
    list($dbconn) = xarDBGetConn();
    // load the database table maintenance api
    xarDBLoadTableMaintenanceAPI();
    
    //======================================================================
    //  GET TIMEZONE DB TABLE INFORMATION
    //======================================================================
    $xartable           =& xarDBGetTables();
    $zones_table        =& $xartable['timezone_zones'];
    $zones_data_table   =& $xartable['timezone_zones_data'];
    $links_table        =& $xartable['timezone_links'];
    $rules_table        =& $xartable['timezone_rules'];
    $rules_data_table   =& $xartable['timezone_rules_data'];
    $zones_data_has_rules_table   =& $xartable['timezone_zones_data_has_rules'];
    
    //======================================================================
    //  ZONES TABLE FIELDS
    //======================================================================
    $zones_fields = array(
        'id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'increment'=>true,
            'primary_key'=>true
            ),
        'name' => array(
            'type'=>'varchar',
            'size'=>255,
            'null'=>false
            )
        );
    //======================================================================
    //  ZONES DATA TABLE FIELDS
    //======================================================================
    $zones_data_fields = array(
        'id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'increment'=>true,
            'primary_key'=>true
            ),
        'zones_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false
            ),
        'gmtoff' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'format' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'untilyear' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>true
            ),
        'untilmonth' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>true
            ),
        'untilday' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>true
            ),
        'untiltime' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            )
        );
    //======================================================================
    //  RULES TABLE FIELDS
    //======================================================================
    $rules_fields = array(
        'id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'increment'=>true,
            'primary_key'=>true
            ),
        'name' => array(
            'type'=>'varchar',
            'size'=>255,
            'null'=>false
            )
        );
    //======================================================================
    //  RULES DATA TABLE FIELDS
    //======================================================================
    $rules_data_fields = array(
        'id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'increment'=>true,
            'primary_key'=>true
            ),
        'rules_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false
            ),
        'rule_from' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false
            ),
        'rule_to' => array(
            'type'=>'varchar',
            'size'=>10,
            'null'=>false
            ),
        'rule_type' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'rule_in' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>true
            ),
        'rule_on' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'rule_at' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'rule_save' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            ),
        'rule_letter' => array(
            'type'=>'varchar',
            'size'=>20,
            'null'=>true
            )
        );
    //======================================================================
    //  LINKS TABLE FIELDS
    //======================================================================
    $links_fields = array(
        'id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'increment'=>true,
            'primary_key'=>true
            ),
        'zones_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false
            ),
        'name' => array(
            'type'=>'varchar',
            'size'=>255,
            'null'=>false
            )
        );
        
    //======================================================================
    //  ZONES_DATA_HAS_RULES TABLE
    //======================================================================
    $zones_data_has_rules_fields = array(
        'zones_data_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'primary_key'=>true
            ),
        'rules_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'primary_key'=>true
            )
        );
  
    //======================================================================
    // CREATE THE TABLES
    //======================================================================
    $query = xarDBCreateTable($zones_table, $zones_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateTable($zones_data_table, $zones_data_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateTable($links_table, $links_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateTable($rules_table, $rules_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateTable($rules_data_table, $rules_data_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateTable($zones_data_has_rules_table, $zones_data_has_rules_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    //======================================================================
    // CREATE ADDITIONAL TABLE INDEXES
    //======================================================================
    $query = xarDBCreateIndex($links_table,array('name'=>'links_FKIndex1', 'fields'=>array('zones_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($rules_data_table,array('name'=>'rules_data_FKIndex1', 'fields'=>array('rules_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($zones_data_table,array('name'=>'zones_data_FKIndex1', 'fields'=>array('zones_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($zones_data_has_rules_table,array('name'=>'zones_data_has_rules_FKIndex1', 'fields'=>array('zones_data_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($zones_data_has_rules_table,array('name'=>'zones_data_has_rules_FKIndex2', 'fields'=>array('rules_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //======================================================================
    // POPULATE THE RULES TABLES
    //======================================================================
    
    // this array contains the values of the db fields for each rule entry
    $rules_data_field_names = array(
        'rule_from', 'rule_to', 'rule_type', 'rule_in',
        'rule_on', 'rule_at', 'rule_save', 'rule_letter'
        );
    
    // loop over the Rules
    foreach($Rules as $name => $rule_data) {
        // we need to get the next ID for the rules table
        $rules_id = $dbconn->GenId($rules_table);
        // insert the Rule Name into the rules table
        $name = xarVarPrepForStore($name);
        $sql_rules = "INSERT INTO $rules_table (id, name) VALUES ('$rules_id', '$name');";
        $result =& $dbconn->Execute($sql_rules);
        //TODO::return an exception
        if(!$result) return false;
        // grab the last inserted rules_id
        $rules_id = $dbconn->PO_Insert_ID($rules_table,'id');
        // we need to insert the rules data
        for($i=0, $max=count($rule_data); $i<$max; $i++) {
            // create the next insert id for the rules_data table
            $rules_data_id = $dbconn->GenId($rules_data_table);
            
            // set the initial sql parameters
            $rules_data_insert_fields = 'id,rules_id';
            $rules_data_insert_values = xarVarPrepForStore($rules_data_id).','.xarVarPrepForStore($rules_id);
            
            // create the insert statement
            $p = 0;
            foreach($rule_data[$i] as $data) {
                $rules_data_insert_fields .= ',' . xarVarPrepForStore($rules_data_field_names[$p]);
                $rules_data_insert_values .= ',\'';
                if($rules_data_field_names[$p] == 'rule_in') {
                    $rules_data_insert_values .= xarVarPrepForStore(timezone__getMonth($data));
                } else {
                    $rules_data_insert_values .= xarVarPrepForStore($data);
                }
                $rules_data_insert_values .= '\'';
                $p++;
            }

            // execute the SQL
            $sql_rules_data = "INSERT INTO $rules_data_table ($rules_data_insert_fields) VALUES ($rules_data_insert_values)";
            $result =& $dbconn->Execute($sql_rules_data);
            //TODO::return an exception
            if(!$result) return false;
        }
    }
    // housekeeping
    unset($Rules,$rules_data_field_names);
    
    //======================================================================
    // POPULATE THE ZONES TABLES
    //======================================================================
    
    // this array contains the value type of the fields for each zone entry
    $zones_data_field_names = array('gmtoff','rule','format','untilyear','untilmonth','untilday','untiltime');
    // loop over the Zones
    foreach($Zones as $name => $zones_data) {
        $zones_id = $dbconn->GenID($zones_table);
        $name = xarVarPrepForStore($name);
        $sql_zones = "INSERT INTO $zones_table (id, name) VALUES ('$zones_id', '$name');";
        $result =& $dbconn->Execute($sql_zones);
        //TODO::return an exception
        if(!$result) return false;
        $result->Close();
        
        // grab the last inserted zones_id
        $zones_id = $dbconn->PO_Insert_ID($zones_table,'id');
        
        // time to insert the zone data
        for($i=0, $max=count($zones_data); $i<$max; $i++) {
            $zones_data_id = $dbconn->GenId($zones_data_table);
            // set the initial sql parameters
            $zones_data_insert_fields = 'id,zones_id';
            $zones_data_insert_values = xarVarPrepForStore($zones_data_id).','.xarVarPrepForStore($zones_id);
            $p = 0; // pointer
            // reset our flags
            $hasRule = false;
            $rule_id = 0;
            foreach($zones_data[$i] as $data) {
                if($zones_data_field_names[$p] == 'rule') {
                    // grab the rule id or set to 0 when no rule applies
                    $getRuleSql = "SELECT id FROM $rules_table WHERE name = '".xarVarPrepForStore($data)."'";
                    $result =& $dbconn->Execute($getRuleSql);
                    if($result && !$result->EOF) {
                        $hasRule = true;
                        $rule_id = $result->fields[0];
                    }
                    $result->Close();
                } else {
                    $zones_data_insert_fields .= ',' . xarVarPrepForStore($zones_data_field_names[$p]);
                    $zones_data_insert_values .= ',\'';
                    if($zones_data_field_names[$p] == 'untilmonth') {
                        $zones_data_insert_values .= xarVarPrepForStore(timezone__getMonth($data));
                    } else {
                        $zones_data_insert_values .= xarVarPrepForStore($data);
                    }
                    $zones_data_insert_values .= '\'';
                }
                
                // increment our pointer
                $p++;
            }
            // execute the SQL
            $sql_zones_data = "INSERT INTO $zones_data_table ($zones_data_insert_fields) VALUES ($zones_data_insert_values)";
            $result =& $dbconn->Execute($sql_zones_data);
            //TODO::return an exception
            if(!$result) return false;
            $result->Close();
            
            // Check to see if we need to populate the rule relationship table
            if($hasRule && $rule_id > 0) {
                $zones_data_id = $dbconn->PO_Insert_ID($zones_data_table,'id');
                $sql = "INSERT INTO $zones_data_has_rules_table (zones_data_id, rules_id)
                        VALUES ($zones_data_id,$rule_id)";
                $result =& $dbconn->Execute($sql);
                if(!$result) return false;
                $result->Close();
            }
        }
    }
    // housekeeping
    unset($Zones,$zones_data_field_names);
    
    //======================================================================
    // POPULATE THE LINKS TABLE
    //======================================================================
    foreach($Links as $name => $zone) {
        $sqlGetZone = "SELECT id FROM $zones_table WHERE name = '".xarVarPrepForStore($zone)."'";
        $result =& $dbconn->Execute($sqlGetZone);
        if(!$result || $result->EOF) {
            // we don't have anything to link
            continue;
        } else {
            $links_id = $dbconn->GenId($links_table);
            $sql_links = "INSERT INTO $links_table (id,zones_id,name)
                          VALUES (".xarVarPrepForStore($links_id).",
                                  ".xarVarPrepForStore($result->fields[0]).",
                                  '".xarVarPrepForStore($name)."')";
            $result =& $dbconn->Execute($sql_links);
            if(!$result) return false;
            $result->Close();
        }
    }
    unset($Links);
    
    // hopefully all went well
    return true;
}

function timezone_upgrade($oldversion)
{
    switch($oldversion) {
        
        // Upgrade From Version 0.1.0
        case '0.1.0':
            // This version had no DB tables, so
            // we should only need to run the init 
            timezone_init();
            break;
        
        // Upgrade From Version 0.2.0
        case '0.2.0':
            // remove the old 0.2.0 tables
            timezone__delete_020_tables();
            // re-run the init to install the new tables
            timezone_init();
            break;
        
        // Upgrade From Version 0.2.1
        case '0.2.1':
            // new tzdata was released on 20031215
            // remove the old tables
            timezone_delete();
            // reinstall the tables
            timezone_init();
            break;
        
        // Upgrade From Version 0.2.2 
        case '0.2.2':
            // remove the old 0.2.1 tables
            timezone__delete_021_tables();
            // re-run the init to install the new tables
            timezone_init();
            break;
       
        // Upgrade From Version 0.2.3
        case '0.2.3':
            break;     
        
    }
    
    return true;
}

function timezone_delete()
{
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    
    // drop timezone_zones
    $query = xarDBDropTable($xartable['timezone_zones']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_zones_data
    $query = xarDBDropTable($xartable['timezone_zones_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules
    $query = xarDBDropTable($xartable['timezone_rules']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules_data
    $query = xarDBDropTable($xartable['timezone_rules_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_links
    $query = xarDBDropTable($xartable['timezone_links']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_links
    $query = xarDBDropTable($xartable['timezone_zones_data_has_rules']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    return true;
}

/**
 *  Translates month strings into integer values
 *  @access private
 */
function timezone__getMonth($month)
{
    // this is used to convert some of the zone parameters to an integer
    $Months = array(
        'jan'=>1,'feb'=>2,'mar'=>3,'apr'=>4,'may'=>5,'jun'=>6,
        'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12
        );
        
    return (int) $Months[strtolower($month)];
}

/**
 *  Function to remove the 0.2.0 version tables
 *  @access private
 */
function timezone__delete_020_tables()
{
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    
    // drop timezone_zones
    $query = xarDBDropTable($xartable['timezone_zones']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_zones_data
    $query = xarDBDropTable($xartable['timezone_zones_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules
    $query = xarDBDropTable($xartable['timezone_rules']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules_data
    $query = xarDBDropTable($xartable['timezone_rules_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_links
    $query = xarDBDropTable($xartable['timezone_links']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    return true;
}

function timezone__delete_021_tables()
{
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    
    // drop timezone_zones
    $query = xarDBDropTable($xartable['timezone_zones']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_zones_data
    $query = xarDBDropTable($xartable['timezone_zones_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules
    $query = xarDBDropTable($xartable['timezone_rules']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_rules_data
    $query = xarDBDropTable($xartable['timezone_rules_data']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_links
    $query = xarDBDropTable($xartable['timezone_links']);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    // drop timezone_links
    $query = xarDBDropTable(xarDBGetSiteTablePrefix().'_timezone_zones_has_rules');
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    
    return true;
}

?>