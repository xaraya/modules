<?php

abstract class indexer
{
    var $name = 'unknown';         // name of the indexer
    var $base = 'var/sitesearch/'; // base directory

    var $db_conn;           // xaraya db connection
    var $record_set;        // holds the dataset


    var $database;          // xapian database object to write to
    var $stemmer;           // xapian stemmer object

    var $args;
    var $mappings;

    function __construct($args=null)
    {
        if(isset($args['database_name'])) $this->name = $args['database_name'];
        
        $this->db_conn =& xarDBNewConn();

        $db_path = xarModGetVar('sitesearch', 'database_path');
        if( file_exists($db_path) )
        {
            if( file_exists($db_path . "{$this->name}/db_lock") ){ unlink($db_path . "{$this->name}/db_lock"); }
            $this->database = new XapianWritableDatabase($db_path . $this->name, Xapian::DB_CREATE_OR_OPEN);
        }

        $this->stemmer = new XapianStem("english");
        /*
        $this->mappings = array(
            0 => 'id',
            1 => 'title',
            2 => 'value',
            3 => 'format',
            4 => 'url'
        );*/
    }

    // Gettting the content items for a certain indexer must be implemented by our descendents
    abstract function get_items();
    //abstract function make_entry($fields);
    
    function process()
    {
        $i = 0;
        if( is_object($this->record_set) )
        {
            while( !$this->record_set->EOF )
            {
                //if( ++$i % 100 == 0 ){ echo memory_get_usage() . ' '; }
                $document = $this->make_document($this->record_set->fields);
                $this->record_set->MoveNext();
            }
            $this->record_set->Close();
        }
        return;
    }

    /**
        Indexes the text for searching
    */
    function index_text(&$text, XapianDocument &$document, $weight, $prefix=null)
    {
        $words = split(" ", strip_tags($text));
        $pos = 0;
        $len = sizeof($words);
        for( $i = 0; $i < $len; $i++ )
        {
            $word = strtolower(trim($words[$i]));
            // from what I've seen there are no words more than 50 chars
            //  Also remember terms MUST be under 255 chars other wise the BTREE can not handle it.
            //  There may be a xapian imposed limit of 252 so that would be the better value to use to.
            if( !empty($word) && strlen($word) < 50 )
            {
                ++$pos;
                // Add an occurance of $word at $pos in $document
                if( !empty($prefix) ) 
                { 
                    $document->add_posting("$prefix$word", $pos, $weight); 
                }
                $document->add_posting($word, $pos, $weight);
                $cleaned_word = $this->stemmer->apply($word);
                if( $word != $cleaned_word )
                {
                    if( !empty($prefix) )
                    {
                        $document->add_posting("$prefix$cleaned_word", $pos, $weight);
                    }
                    $document->add_posting($cleaned_word, $pos, $weight);
                }
            }
        }
        xarLogMessage("SS: doc now has :" . $document->termlist_count() ." terms");
    }


    function indexer_control()
    {
        $text =
            "id : field=id boolean=Q unique=Q\n" .
            "url: field=url unique=Q\n" .
            "title : field=title index=S index\n" .
            "value : field=value truncate=255 index\n" .
            "type : field=type boolean=XT\n";

        return $text;
    }
}
?>
