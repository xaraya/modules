<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.base.xarproperties.textbox');

class KeywordsProperty extends TextBoxProperty
{
    public $id         = 30117;
    public $name       = 'keywords';
    public $desc       = 'Keywords';
    public $reqmodules = array('keywords');

    private $wordcache  = null;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->filepath   = 'modules/keywords/xarproperties';
        // We want a reference to the object here
        $this->include_reference = 1;

        // Force setting of the datastore to NONE
        $this->source = '';
    }

    public function validateValue($value = null)
    {
        if (!parent::validateValue($value)) return false;

        $words = xarModAPIFunc('keywords', 'admin', 'separatekeywords', array('keywords' => $value));
        $cleanwords = array();
        foreach ($words as $word) {
            if (empty($word)) continue;
            $cleanwords[] = $word;
        }
        $this->value = $cleanwords;
        return true;
    }

    public function getValue()
    {
        return $this->getKeywords();
    }

    function getItemValue($itemid)
    {
        return $this->getKeywords(array('value' => $itemid));
    }

    public function showInput(Array $data = array())
    {
        // The virtual datastore will use the itemid as value for this property
        $words = $this->getKeywords($data);
        $keywords = array();
        foreach ($words as $word) $keywords[] = $word['keyword'];
        $data['value'] = implode(',', $keywords);
        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        // The virtual datastore will use the itemid as value for this property
        $words = $this->getKeywords($data);
        $keywords = array();
        foreach ($words as $word) $keywords[] = $word['keyword'];
        $data['value'] = implode(',', $keywords);
        return parent::showOutput($data);
    }

    private function getKeywords(Array $data = array())
    {
        // Make sure we have the keywords table
        xarMod::apiLoad('keywords');

        $table =& xarDB::getTables();
        $q = new Query('SELECT');
        $q->addtable($table['keywords'], 'k');
        $q->addtable($table['keywords_index'], 'i');
        $q->join('i.keyword_id', 'k.id');
        $q->addfield('i.id AS id');
        $q->addfield('k.keyword AS keyword');
        $q->eq('i.module_id', $this->objectref->moduleid);
        $q->eq('i.itemtype', $this->objectref->itemtype);
        $q->eq('i.itemid', $this->_itemid);
        $q->addorder('keyword', 'ASC');
//        $q->qecho();
        $q->run();
        $words = $q->output();
        return $words;
    }

    function createValue($itemid=0)
    {
        $words = $this->value;
        $keyword_ids = $this->updateKeywords($words);
        $this->updateAssociations($itemid, $keyword_ids);
        return $itemid;
    }

    public function updateValue($itemid=0)
    {
        return $this->createValue($itemid);
    }

    public function deleteValue($itemid=0)
    {
        $associations = $this->getAssociations($itemid);
        $this->deleteAssociations($itemid, array_keys($associations));
        return true;
    }

#----------------------------------------------------------------
# Check if we have the words in the database and add those missing
#
    private function updateKeywords($words) 
    {        
        if (empty($words)) return array();
        
        // Make sure we have the keywords table
        xarMod::apiLoad('keywords');

        $table =& xarDB::getTables();
        $q = new Query('SELECT', $table['keywords']);
        $q->in('keyword', $words);
        $q->run();
        $keywords = array();
        $keyword_ids = array();
        
        // Reshuffle the results. This may be overkill as we don't (for now) pass it back
        foreach($q->output() as $row) {
            $keywords[$row['keyword']] = $row;
            $keyword_ids[$row['id']] = $row;
        }

        $q = new Query('INSERT', $table['keywords']);
        foreach ($this->value as $word) {
        
            // If we already have this keyword in the database, move on
            if (isset($keywords[$word])) continue;
            
            // Thiis is a new keyword; add it to the index
            $q->addfield('keyword', $word);
            $q->run();
            $keyword_id = $q->lastid($table['keywords'], 'id');
            $keywords[$word] = array('id' => $keyword_id, 'keyword' => $word);
            $keyword_ids[$keyword_id] = array('id' => $keyword_id, 'keyword' => $word);
            $q->clearfields();
        }
        $ids = array_keys($keyword_ids);
        return $ids;
    }

#----------------------------------------------------------------
# After saving one or more keyword entries, update the associations table
#
    private function updateAssociations($itemid, $keyword_ids=array()) 
    {
        // Check if we are in an object or not
        $moduleid = isset($this->objectref->moduleid) ? $this->objectref->moduleid : null;
        if (!empty($moduleid) && !empty($itemid)) {
            sys::import('modules.keywords.class.association');
            $association = new Association();
            $association->sync_associations($moduleid, $this->objectref->itemtype, $itemid, $keyword_ids);
        }
        return true;
    }

#----------------------------------------------------------------
# Get the associations of this item
#
    private function getAssociations($itemid) 
    {
        $associations = array();
        // Check if we are in an object or not
        $moduleid = isset($this->objectref->moduleid) ? $this->objectref->moduleid : null;
        if (!empty($moduleid) && !empty($itemid)) {
            sys::import('modules.keywords.class.association');
            $association = new Association();
            $args = array(
                    'module_id'    => $moduleid,
                    'itemtype'     => $this->objectref->itemtype,
                    'property_id'  => (int)$this->id,
                    'itemid'       => $itemid,
            );
            $associations = $association->get_associations($args);
        }
        return $associations;
    }
#----------------------------------------------------------------
# After creating a keyword entry, add the required association
#
    private function addAssociation($itemid, $keyword_id=0) 
    {
        // Check if we are in an object or not
        $moduleid = isset($this->objectref->moduleid) ? $this->objectref->moduleid : null;
        if (!empty($moduleid) && !empty($itemid)) {
            sys::import('modules.keywords.class.association');
            $association = new Association();
            $args = array(
                    'keyword_id'  => $keyword_id,
                    'module_id'    => $moduleid,
                    'itemtype'     => $this->objectref->itemtype,
                    'property_id'  => (int)$this->id,
                    'itemid'       => $itemid,
            );
            $association->add_association($args);
        }
        return true;
    }

}

?>