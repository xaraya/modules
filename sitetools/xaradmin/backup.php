<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee <http://xaraya.athomeandabout.com >
*/

**
 * Backup tables in your database
 */
function sitetools_admin_backup()
{
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;
    
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data['finished']=false;
         // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        
           list($dbconn) = xarDBGetConn();
        $dbname= xarDBGetName();
        $data['dbname'] =$dbname;
        echo "The database name is ".$dbname;

        $tables = array();
        $query = mysql_list_tables($dbname);
        $result      = @mysql_query($query);
        if ($result) {
             while ($row = mysql_fetch_row($result))
            {
                $tables[] = array("id" => $row[0], "name" => $row[0]);
            }
       }else {
            echo "No table query results";
       }

       $data[]=$tables;
       // Return the template variables defined in this function
        return $data;
    }
    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;


   return true;
    }

/*
      $a = -1;

    foreach($tables as $table)
    {
     $a++;
     $output->TableRowStart();
     $output->FormHidden("tablenames[" . $a . "]", $table['name']);
     $output->TableColStart();
     $output->FormCheckbox("tables[" . $a . "]", 1);
     $output->TableColEnd();
     $output->TableColStart();
     $output->Text($table['name']);
     $output->TableColEnd();
     $output->TableRowEnd();
    }

    $output->TableEnd();
    // Return data
    return $data;

}


/////////////////////////////////////////////////////
switch($op) {

	


		case "mysql_tools2":
		@set_time_limit(600);




		switch($lang)
		{

			default : 
				// English Text	
				$strNoTablesFound = "No tables found in database.";
				$strHost = "Host";
				$strDatabase = "Database ";
				$strTableStructure = "Table structure for table";
				$strDumpingData = "Dumping data for table";
				$strError = "Error";
				$strSQLQuery = "SQL-query";
				$strMySQLSaid = "MySQL said: ";
				$strBack = "Back";
				$strFileName = "Save Database";
				$strName = "Database saved";
				$strDone = "the";
				$strat = "at";
				$strby = "by";
				$date_jour = date ("m-d-Y");		
				break;
		}

		

	
		
		header("Content-disposition: filename=$strFileName $dbname $datum.sql");
		header("Content-type: application/octetstream");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		// doing some DOS-CRLF magic...
		$client = getenv("HTTP_USER_AGENT");
		if(ereg('[^(]*\((.*)\)[^)]*',$client,$regs)) 
		{
		$os = $regs[1];
		// this looks better under WinX
		if (eregi("Win",$os)) 
		    $crlf="\r\n";
		}
		
		
		function my_handler($sql_insert)
		{
		    global $crlf;
		    echo "$sql_insert;$crlf";
		}
		
		// Get the content of $table as a series of INSERT statements.
		// After every row, a custom callback function $handler gets called.
		// $handler must accept one parameter ($sql_insert);
		function get_table_content($db, $table, $handler)
		{
		    $result = mysql_db_query($db, "SELECT * FROM $table") or mysql_die();
		    $i = 0;
		    while($row = mysql_fetch_row($result))
		    {
		//        set_time_limit(60); // HaRa
		        $table_list = "(";
		
		        for($j=0; $j<mysql_num_fields($result);$j++)
		            $table_list .= mysql_field_name($result,$j).", ";
		
		        $table_list = substr($table_list,0,-2);
		        $table_list .= ")";
		
		        if(isset($GLOBALS["showcolumns"]))
		            $schema_insert = "INSERT INTO $table $table_list VALUES (";
		        else
		            $schema_insert = "INSERT INTO $table VALUES (";
		
		        for($j=0; $j<mysql_num_fields($result);$j++)
		        {
		            if(!isset($row[$j]))
		                $schema_insert .= " NULL,";
		            elseif($row[$j] != "")
		                $schema_insert .= " '".addslashes($row[$j])."',";
		            else
		                $schema_insert .= " '',";
		        }
		        $schema_insert = ereg_replace(",$", "", $schema_insert);
		        $schema_insert .= ")";
		        $handler(trim($schema_insert));
		        $i++;
		    }
		    return (true);
		}
		
		// Return $table's CREATE definition
		// Returns a string containing the CREATE statement on success
		function get_table_def($db, $table, $crlf)
		{
		    $schema_create = "";
		    $schema_create .= "DROP TABLE IF EXISTS $table;$crlf";
		    $schema_create .= "CREATE TABLE $table ($crlf";
		
		    $result = mysql_db_query($db, "SHOW FIELDS FROM $table") or mysql_die();
		    while($row = mysql_fetch_array($result))
		    {
		        $schema_create .= "   $row[Field] $row[Type]";
		
		        if(isset($row["Default"]) && (!empty($row["Default"]) || $row["Default"] == "0"))
		            $schema_create .= " DEFAULT '$row[Default]'";
		        if($row["Null"] != "YES")
		            $schema_create .= " NOT NULL";
		        if($row["Extra"] != "")
		            $schema_create .= " $row[Extra]";
		        $schema_create .= ",$crlf";
		    }
		    $schema_create = ereg_replace(",".$crlf."$", "", $schema_create);
		    $result = mysql_db_query($db, "SHOW KEYS FROM $table") or mysql_die();
		    while($row = mysql_fetch_array($result))
		    {
		        $kname=$row['Key_name'];
		        if(($kname != "PRIMARY") && ($row['Non_unique'] == 0))
		            $kname="UNIQUE|$kname";
		         if(!isset($index[$kname]))
		             $index[$kname] = array();
		         $index[$kname][] = $row['Column_name'];
		    }
		
		    while(list($x, $columns) = @each($index))
		    {
		         $schema_create .= ",$crlf";
		         if($x == "PRIMARY")
		             $schema_create .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
		         elseif (substr($x,0,6) == "UNIQUE")
		            $schema_create .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
		         else
		            $schema_create .= "   KEY $x (" . implode($columns, ", ") . ")";
		    }
		
		    $schema_create .= "$crlf)";
		    return (stripslashes($schema_create));
		}
		
		function mysql_die($error = "")
		{
		    echo "<b> $strError </b><p>";
		    if(isset($sql_query) && !empty($sql_query))
		    {
		        echo "$strSQLQuery: <pre>$sql_query</pre><p>";
		    }
		    if(empty($error))
		        echo $strMySQLSaid.mysql_error();
		    else
		        echo $strMySQLSaid.$error;
		    echo "<br><a href=\"javascript:history.go(-1)\">$strBack</a>";
		    exit;
		}

		

		global $dbhost, $dbuname, $dbpass, $dbname;

list($dbconn) = pnDBGetConn();
$dbhost =  pnConfigGetVar('dbhost');
$dbuname = pnConfigGetVar('dbuname');
$dbpass = pnConfigGetVar('dbpass');
$dbname = pnConfigGetVar('dbname');

		

		@mysql_select_db("$dbname") or die ("Unable to select database");

		

		$tables = mysql_list_tables($dbname);

		

		$num_tables = @mysql_numrows($tables);

		if($num_tables == 0)

		{

		    echo "No tables found in database.";

		}

		else

		{

$i = 0;

$stunden = date ("H:i");

print "# ========================================================$crlf";
print "# This Backup was made with MySql-Tool Version 2.0$crlf";
print "# http://www.nukeland.de (michaelius@nukeland.de)$crlf";
print "# $crlf";
print "# $strName : $dbname$crlf";
print "# $strDone $datum $strat $stunden !$crlf";
print "# $crlf";
print "# ========================================================$crlf";
print " $crlf";		    
       while($i < $num_tables)
	{ 
	$table = mysql_tablename($tables, $i);
print  $crlf;
print "# --------------------------------------------------------$crlf";
print "#$crlf";
print "# $strTableStructure '$table'$crlf";
print "#$crlf";
print $crlf;
	echo get_table_def($dbname, $table, $crlf).";$crlf$crlf";
print "#$crlf";
print "# $strDumpingData '$table'$crlf";
print "#$crlf";
print $crlf;
			
get_table_content($dbname, $table, "my_handler");		
$i++;
}
}		
//Header("Location: admin.php?op=adminMain");
break;



return true;
}*/

?>
