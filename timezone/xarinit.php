<?php

function timezone_init($keepSettings = false)
{
    if(!$keepSettings) {
        // what is the default timezone
        xarModSetVar('timezone','default','Etc/UTC');
        // what is the correction, in seconds, we'll make for the server's utc timestamp
        xarModSetVar('timezone','server_correction',0);
    }
    
    // give us as much time as necessary as this may take a moment
    // supress warnings/errors in case this function does not exist
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
    $dbconn =& xarDBGetConn();
    // load the database table maintenance api
    xarDBLoadTableMaintenanceAPI();
    
    //======================================================================
    //  GET TIMEZONE DB TABLE INFORMATION
    //======================================================================
    $xartable                   = & xarDBGetTables();
    $zones_table                = & $xartable['timezone_zones'];
    $zones_data_table           = & $xartable['timezone_zones_data'];
    $links_table                = & $xartable['timezone_links'];
    $rules_table                = & $xartable['timezone_rules'];
    $rules_data_table           = & $xartable['timezone_rules_data'];
    $zones_data_has_rules_table = & $xartable['timezone_zones_data_has_rules'];
    $zones_has_links_table      = & $xartable['timezone_zones_has_links'];
    
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
        'rules'=> array(
            'type'=>'varchar',
            'size'=>10,
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
    //  ZONES_HAS_LINKS TABLE
    //======================================================================
    $zones_has_links_fields = array(
        'zones_id' => array(
            'type'=>'integer',
            'unsigned'=>true,
            'null'=>false,
            'primary_key'=>true
            ),
        'links_id' => array(
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
    
    $query = xarDBCreateTable($zones_has_links_table, $zones_has_links_fields);
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    //======================================================================
    // CREATE ADDITIONAL TABLE INDEXES
    //======================================================================
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
    
    $query = xarDBCreateIndex($zones_has_links_table,array('name'=>'zones_has_links_FKIndex1', 'fields'=>array('zones_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($zones_has_links_table,array('name'=>'zones_has_links_FKIndex2', 'fields'=>array('links_id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($links_table,array('name'=>'idx_name_id', 'fields'=>array('name','id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($zones_table,array('name'=>'idx_name_id', 'fields'=>array('name','id')));
    if (empty($query)) return; // throw back
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBCreateIndex($rules_table,array('name'=>'idx_name_id', 'fields'=>array('name','id')));
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
        $sql_rules = "INSERT INTO $rules_table (id, name) VALUES ('$rules_id', '$name')";
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
    $zones_data_field_names = array('gmtoff','rules','format','untilyear','untilmonth','untilday','untiltime');
    // loop over the Zones
    foreach($Zones as $name => $zones_data) {
        $zones_id = $dbconn->GenID($zones_table);
        $name = xarVarPrepForStore($name);
        $sql_zones = "INSERT INTO $zones_table (id, name) VALUES ('$zones_id', '$name')";
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
                if($zones_data_field_names[$p] == 'rules') {
                    $zones_data_insert_fields .= ',' . xarVarPrepForStore($zones_data_field_names[$p]);
                    
                    if(preg_match('/[\d]+:[\d]+/',$data)) {
                        // this is a time element and not a rule name
                        // insert it into the zone table
                        $zones_data_insert_values .= ',\'';
                        $zones_data_insert_values .= xarVarPrepForStore($data);
                        $zones_data_insert_values .= '\'';
                    } else {
                        // insert a null value so we load the rule
                        $zones_data_insert_values .= ',NULL';
                        // grab the rule id or set to 0 when no rule applies
                        $getRuleSql = "SELECT id FROM $rules_table WHERE name = '".xarVarPrepForStore($data)."'";
                        $result =& $dbconn->Execute($getRuleSql);
                        if($result && !$result->EOF) {
                            $hasRule = true;
                            $rule_id = $result->fields[0];
                        
                        }
                        $result->Close();
                    }
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
        $result_sqlGetZone =& $dbconn->Execute($sqlGetZone);
        if(!$result_sqlGetZone || $result_sqlGetZone->EOF) {
            // we don't have anything to link
            continue;
        } else {
            // insert the Link into the links table
            $links_id = $dbconn->GenId($links_table);
            $sql_links = "INSERT INTO $links_table (id,name)
                          VALUES ('".xarVarPrepForStore($links_id)."',
                                  '".xarVarPrepForStore($name)."')";
            $result_sql_links =& $dbconn->Execute($sql_links);
            if(!$result_sql_links) return false;
            $result_sql_links->Close();
            
            // insert the zones_id and links_id into the relation table
            $links_id = $dbconn->PO_Insert_ID($links_table,'id');
            $sql_zones_has_links = "INSERT INTO $zones_has_links_table (zones_id, links_id)
                                    VALUES ('".xarVarPrepForStore($result_sqlGetZone->fields[0])."',
                                            '".xarVarPrepForStore($links_id)."')";
            $result_sqlGetZone->Close();
            $result_zones_has_links =& $dbconn->Execute($sql_zones_has_links);
            if(!$result_zones_has_links) return false;
            $result_zones_has_links->Close();
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
            timezone__delete_tables('020');
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
            timezone__delete_tables('021');
            // re-run the init to install the new tables
            timezone_init();
            break;
       
        // Upgrade From Version 0.2.3 through 0.2.5
        case '0.2.3':
        case '0.2.4':
        case '0.2.5':
            // remove the old tables
            timezone__delete_tables('022');
            // re-run init to create the new table structure
            timezone_init();
            break; 
            
        // Upgrade from Version 0.3.0
        case '0.3.0':
            // a modified version of tzdata.php is available for 0.3.1
            timezone_delete();
            timezone_init();
            break;
            
        // Upgrade from Version 0.3.1
        case '0.3.1':
            // added a rules column in zones for 0.3.2 to hold
            // rules that only consist of a timestamp
            // also added some indexes to the zones, rules and links table
            timezone_delete();
            timezone_init();
            break;
        
        // Upgrade from Version 0.3.2
        case '0.3.2':
            break;   
        
    }
    
    return true;
}

function timezone_delete($keepSettings = false)
{
    
    if(!$keepSettings) {
        // Delete the module vars
        xarModDelAllVars('timezone');
    }
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();
    
    // tables in the 0.2.2 version
    $tables = array(
        $xartable['timezone_zones'],
        $xartable['timezone_zones_data'],
        $xartable['timezone_rules'],
        $xartable['timezone_rules_data'],
        $xartable['timezone_links'],
        $xartable['timezone_zones_data_has_rules'],
        $xartable['timezone_zones_has_links'],
        );
    
    foreach($tables as $table) {
        $query = xarDBDropTable($table);
        if (empty($query)) return; // throw back
        $result = &$dbconn->Execute($query);
        if (!$result) return;
        $result->Close();
    }
    
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
 *  Function to remove old tables not in $xartables
 *  @access private
 */
function timezone__delete_tables($version)
{
    $dbconn =& xarDBGetConn();
    xarDBLoadTableMaintenanceAPI();
    $prefix = xarDBGetSiteTablePrefix();
    
    // tables in the 0.2.0 version
    $tables_020 = array(
        '_timezone_zones',
        '_timezone_zones_data',
        '_timezone_rules',
        '_timezone_rules_data',
        '_timezone_links'
        );
    
    // tables in the 0.2.1 version
    $tables_021 = array(
        '_timezone_zones',
        '_timezone_zones_data',
        '_timezone_rules',
        '_timezone_rules_data',
        '_timezone_links',
        '_timezone_zones_has_rules'
        );
    
    // tables in the 0.2.2 - 0.2.5 version
    $tables_022 = array(
        '_timezone_zones',
        '_timezone_zones_data',
        '_timezone_rules',
        '_timezone_rules_data',
        '_timezone_links',
        '_timezone_zones_data_has_rules'
        );
    
    // reference the correct tables array
    $tables =& ${'tables_'.$version};
    
    foreach($tables as $table) {
        $query = xarDBDropTable($prefix.$table);
        if (empty($query)) return; // throw back
        $result = &$dbconn->Execute($query);
        if (!$result) return;
        $result->Close();
    }
    
    return true;
}

?>