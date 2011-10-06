<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * Handle the hitcount property
 * @author mikespub <mikespub@xaraya.com>
 *
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
        // we want a reference to the object here
        $this->include_reference = 1;

        // Force setting for datastore
        $this->source = '';
    }

    public function validateValue($value = null)
    {
        if (!parent::validateValue($value)) return false;

        $words = xarModAPIFunc('keywords',
                             'admin',
                             'separekeywords',
                              array('keywords' => $value));
        $cleanwords = array();
        foreach ($words as $word) {
            $word = trim($word);
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
        $data['value'] = $this->getKeywords($data);
        return parent::showInput($data);
    }

    public function showOutput(Array $data = array())
    {
        // the dummy datastore will use the itemid as value for this property !
        $data['value'] = $this->getKeywords($data);
        return parent::showOutput($data);
    }

    private function getKeywords(Array $data = array(), $update = 0)
    {
        // if we don't have an objectref, return the value as is
        if (empty($this->objectref) || empty($this->objectref->objectid)) {
            if (isset($data['value'])) {
                return $data['value'];
            } else {
                return $this->value;
            }
        }

        // we're dealing with a single item here
        if (!empty($this->_itemid)) {
            if (!isset($this->wordcache)) {
                $this->wordcache = xarMod::apiFunc('keywords', 'user', 'getwords',
                                                  array('modid'  => $this->objectref->moduleid,
                                                        'itemtype' => $this->objectref->itemtype,
                                                        'itemid' => $this->objectref->itemid));
                if (empty($this->wordcache)) {
                    $this->wordcache = '';
                } else {
                    $this->wordcache = implode(',',$this->wordcache);
                }
            }
            return $this->wordcache;
        }

    }
    
    public function createValue($itemid=0)
    {
        // Make sure we have the keywords table
        xarModAPILoad('keywords');
        
        $dbconn =& xarDB::getConn();
        $xartable =& xarDB::getTables();
        $keywordstable = $xartable['keywords'];
        foreach ($this->value as $word) {
            // Get a new keywords ID
            $nextId = $dbconn->GenId($keywordstable);
            // Create new keyword
            $query = "INSERT INTO $keywordstable (id,
                                               keyword,
                                               module_id,
                                               itemtype,
                                               itemid)
                                        VALUES (?,
                                                ?,
                                                ?,
                                                ?,
                                                ?)";
            $result =& $dbconn->Execute($query,array($nextId, $word, $this->objectref->moduleid, $this->objectref->itemtype, $itemid));
        }
        return true;
    }

    public function updateValue($itemid=0)
    {
        if (empty($itemid) || empty($this->objectref) || empty($this->objectref->objectid)) {
            return;
        }

        // get the current keywords for this item
        $oldwords = xarModAPIFunc('keywords','user','getwords',
                                  array('modid' => $this->objectref->moduleid,
                                        'itemtype' => $this->objectref->itemtype,
                                        'itemid' => $itemid));
    
        $delete = array();
        $keep = array();
        $new = array();
        // check what we need to delete, what we can keep, and what's new
        if (isset($oldwords) && count($oldwords) > 0) {
            foreach ($oldwords as $id => $word) {
                if (!in_array($word,$this->value)) {
                    $delete[$id] = $word;
                } else {
                    $keep[] = $word;
                }
            }
            foreach ($this->value as $word) {
                if (!in_array($word,$keep)) {
                    $new[] = $word;
                }
            }
            if (count($delete) == 0 && count($new) == 0) {
                return true;
            }
        } else {
            $new = $this->value;
        }

        // Make sure we have the keywords table
        xarModAPILoad('keywords');
        
        $dbconn =& xarDB::getConn();
        $xartable =& xarDB::getTables();
        $keywordstable = $xartable['keywords'];

        if (count($delete) > 0) {
            // Delete old words for this module item
            $idlist = array_keys($delete);
            $query = "DELETE FROM $keywordstable
                      WHERE id IN (" . join(', ',$idlist) . ")";
    
            $result =& $dbconn->Execute($query);
        }
    
        if (count($new) > 0) {
            foreach ($new as $word) {
                // Get a new keywords ID
                $nextId = $dbconn->GenId($keywordstable);
                // Create new keywords
                $query = "INSERT INTO $keywordstable (id,
                                                   keyword,
                                                   module_id,
                                                   itemtype,
                                                   itemid)
                        VALUES (?,
                                ?,
                                ?,
                                ?,
                                ?)";//echo $query;var_dump($word);var_dump($this->objectref->moduleid);var_dump($this->objectref->itemtype);var_dump($itemid);exit;
    
                $result =& $dbconn->Execute($query,array($nextId, $word, $this->objectref->moduleid, $this->objectref->itemtype, $itemid));
            }
        }
        return true;
    }

    public function deleteValue($itemid=0)
    {
        if (empty($itemid) || empty($this->objectref) || empty($this->objectref->objectid)) {
            return;
        }
        // delete hitcount entry
        xarMod::apiFunc('hitcount', 'admin', 'delete',
                        array('modname'  => xarMod::getName($this->objectref->moduleid),
                              'itemtype' => $this->objectref->itemtype,
                              'objectid' => $itemid));
        return true;
    }
}

?>