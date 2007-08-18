<?php

include_once("modules/sitesearch/xarclass/indexer.php");
class xarbb_indexer extends indexer
{
    var $name = 'xarbb';

    /**
      * Set up a base dataset from xarbb by querying what is in the fora
      *
      *
    **/
    function get_items()
    {
        $topics   = xarDBGetSiteTablePrefix() . '_xbbtopics';
        $comments = xarDBGetSiteTablePrefix() . '_comments';

        // 1. Get the topics plus all responses (which are in comments)
        // 2. Exclude root node sideeffects from comments
        // 3. TODO: only consider active topics
        // 4. TODO: only consider topics from forums which are enabled.

        $sql ="SELECT xar_tid as id,
                       xar_ttitle as title,
                       xar_tpost as post FROM $topics
        UNION
                SELECT CONCAT(xar_objectid,xar_cid) as id,
                       xar_title as title,
                       xar_text as post
                FROM   xar_comments
                WHERE xar_pid!=? AND xar_modid=?";
        $this->record_set = $this->db_conn->Execute($sql,array(0,300));
        if( !$this->record_set ) return false;

        return true;
    }

    /**
      * Given one item of the recordset as produced by get_items, create
      * a search index document bases on the fields given
      *
     **/
    function &make_document($fields)
    {
        list($topic_id, $subject, $text) = $fields;

        // base URL for the topic
        $url = xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $topic_id));


        $document = new XapianDocument();

        // Index the subject and the text, where subject is a bit more important to match
        $this->index_text($subject, $document, $weight=3);
        $this->index_text($text, $document, $weight=2);

        /*
            Add values to documents for use in displaying search results
        */
        $i = 0;
        $document->add_value($i++, $topic_id);
        $document->add_value($i++, $subject);
        // For the excerpt in results, 255 is enough
        // @todo, make this a modvar for sitesearch
        $document->add_value($i++, substr(strip_tags(trim($text)), 0, 255));
        $document->add_value($i++, 'HTML');
        $document->add_value($i, $url);

        $this->database->replace_document($topic_id, $document);

        return $document;
    }
}


?>