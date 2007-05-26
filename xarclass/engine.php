<?php

class sitesearch_engine
{
    /**
        Hold the keywords being used in the search/query
    */
    var $keywords;
    
    /**
        Constructs the SiteSearch engine
    */
    function sitesearch_engine()
    {
    
    }
    
    /**
        Perform search
    */
    function search($keywords)
    {
        $this->log_search($keywords);
    
        return true;
    }
    
    /**
        Log a search query
    */
    function log_search($keywords)
    {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        
        $log_table = $xartable['sitesearch_query_log'];
        
        $sql = "
            UPDATE $log_table
            SET
                count = count + 1,
                last_search = ?
            WHERE
                keywords = ?        
        ";
        
        $result = $dbconn->Execute($sql, array(time(), $keywords));
        if( !$result ){ return false; }

        if( $dbconn->Affected_Rows() <= 0 )
        {
            $sql = "
                INSERT INTO $log_table ( keywords, count, last_search )
                VALUES ( ?, 1, ?)
            ";
            $result =& $dbconn->Execute($sql, array($keywords, time()));
            if( !$result ){ return false; }
        }
        
        return true;
    }
    
    /**
        Get the search query logs
    */
    function get_logs($start_num=1, $num=20)
    {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        
        $log_table = $xartable['sitesearch_query_log'];
        
        // Get Results
        $sql = "
            SELECT keywords, count, last_search
            FROM $log_table 
            GROUP BY keywords
            ORDER BY last_search DESC
        ";
        $result = $dbconn->SelectLimit($sql, $num, $start_num-1);
    
        $qtracks = array();
        while( (list($keywords, $count, $qtime) = $result->fields) != null )
        {
            $qtracks[] = array(
                'word' => $keywords,
                'count' => $count,
                'qtime' => $qtime
            );
        
            $result->MoveNext();
        }
    
        return $qtracks;
    }
    
    /**
        Get the number of keywords searched for
    */
    function count_logs()
    {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        
        $log_table = $xartable['sitesearch_query_log'];

        $sql = "
            SELECT COUNT(*)
            FROM $log_table 
        ";
        $result = $dbconn->Execute($sql);
        $size = $result->fields[0];
    
        return $size;
    }
    
    /**
        Count the total number of search performed
    */
    function count_searches()
    {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        
        $log_table = $xartable['sitesearch_query_log'];

        $sql = "
            SELECT SUM(count)
            FROM $log_table 
        ";
        $result = $dbconn->Execute($sql);
        $num = $result->fields[0];
    
        return $num;
    }
    
    /**
        Get possible limits
    */
    function get_limits()
    {
        $limits = xarModGetVar('sitesearch', 'databases');        
        $limits = trim($limits);
        if( empty($limits) ){ return array(); }
        
        $num_dbs = -1;
        $dbs = array();
        $lines = split("\n", $limits);
        foreach( $lines as $line )
        {
            $line = trim($line);
            if( isset($line[0]) and $line[0] != "=" )
            {
                $num_dbs++;
                list( $db, $indexer, $display ) =@ split(":", $line);
                $dbs[$num_dbs] = array(
                    'database_name' => $db,
                    'indexer_type'  => $indexer,
                    'display_name'  => $display,
                    'args'          => array(),
                    'mappings'      => array()
                );
            }
            else 
            {
                $line = substr($line, 1);
                if( $line == 'args' )
                    $what = 'args';
                else if( $line == 'mappings' )
                    $what = 'mappings';
                    
                if( strchr($line, ":" ) )
                {
                    list($key, $value) = @ split(":", $line);                
                    if( $what == 'mappings' )
                    {
                        if( strpos($value, ' ') != false )
                        {
                            list($value, $prefix) =@ split(' ', $value);
                        }
                    }
                    $dbs[$num_dbs][$what][$key] = $value;
                    /*
                    $dbs[$num_dbs][$what][$key] = array(
                        'field' => $value,
                        'prefix' => isset($prefix) ? $prefix : ''
                    );
                    */
                }
            }
        }
        
        return $dbs;
    }
    
    /**
        Highlight words with a string
        @author dbergeron [at] clincab [dot] com from php.net docs
            http://us2.php.net/manual/en/function.str-replace.php
    */
    function highlight($x, $var) 
    {
        $hl_begin = xarModGetVar('sitesearch', 'HLBeg');
        $hl_end = xarModGetVar('sitesearch', 'HLEnd');
       //$x is the string, $var is the text to be highlighted
       if ($var != "") 
       {
           $xtemp = "";
           $i=0;
           while($i<strlen($x)){
               if((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0)) {
    //this version bolds the text. you can replace the html tags with whatever you like.
                       $xtemp .= $hl_begin . substr($x, $i , strlen($var)) . $hl_end;
                       $i += strlen($var);
               }
               else {
                   $xtemp .= $x{$i};
                   $i++;
               }
           }
           $x = $xtemp;
       }
       return $x;
    }     
}
?>