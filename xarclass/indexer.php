<?php

class indexer
{
    /**
        Name of the indexer
    */
    var $name = 'unknown';

    /**
        Base dir
    */
    var $base;

    /**
        The main xaraya DB connection used to get the data.
    */
    var $db_conn;

    /**
        Holds data set
    */
    var $record_set;

    /**
        The Xapian database to write to.
    */
    var $database;

    /**
        The Xapian Stemmer Object.
    */
    var $stemmer;

    /**

    */
    var $args;
    var $mappings;

    /**
        The main DB indexer
    */
    function indexer($args=null)
    {
        $this->base = 'var/sitesearch/';

        $db_args = array(
            'databaseType' => 'mysql',
            'databaseHost' => '',
            'databaseName' => '',
            'userName' => '',
            'password' => ''
        );
        $this->db_conn =& xarDBNewConn();

        $db_path = xarModGetVar('sitesearch', 'database_path');
        if( file_exists($db_path) )
        {
            if( file_exists($db_path . "{$this->name}/db_lock") ){ unlink($db_path . "{$this->name}/db_lock"); }
            $this->database = new_writabledatabase($db_path . $this->name, DB_CREATE_OR_OPEN);
        }

        $this->stemmer = new_stem("english");
        /*
        $this->mappings = array(
            0 => 'id',
            1 => 'title',
            2 => 'value',
            3 => 'format',
            4 => 'url'
        );*/
    }

    /**
        Query the DB and setup the record set
    */
    function get_items()
    {

        return true;
    }

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
    function index_text(&$text, &$document, $weight, $prefix=null)
    {
        $words = split(" ", strip_tags($text));
        $pos = 0;
        $len = sizeof($words);
        for( $i = 0; $i < $len; $i++ )
        {
            $word = strtolower(trim($words[$i]));
            // from what I've seem there are no words more than 50 chars
            //  Also remember terms MUST be under 255 chars other wise the BTREE can not handle it.
            //  There may be a xapian imposed limit of 252 so that would be the better value to use to.
            if( !empty($word) && strlen($word) < 50 )
            {
                ++$pos;
                if( !empty($prefix) ){ document_add_posting($document, "$prefix$word", $pos, $weight); }
                document_add_posting($document, $word, $pos, $weight);
                $cleaned_word = stem_stem_word($this->stemmer, $word);
                if( $word != $cleaned_word )
                {
                    if( !empty($prefix) ){
                        document_add_posting($document, "$prefix$cleaned_word", $pos, $weight);
                    }
                    document_add_posting($document, $cleaned_word, $pos, $weight);
                }
            }
        }
    }

    function make_entry($fields)
    {
        return false;
    }

    /**

    */
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
