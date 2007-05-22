<?php
    sys::import('xaraya.structures.tree');

    class Comments extends Object
    {
        function get(int $id)
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT id,
                                pid,
                                modid,
                                itemtype,
                                objectid,
                                date,
                                author,
                                title,
                                hostname,
                                text,
                                left,
                                right
                                status,
                                anonpost
                           FROM  " . $xartable['comments'] . " WHERE id = ?";
            $bindvars = array($id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            $c = new CommentTreeNode();
            list($c->id, $c->pid, $c->modid, $c->itemtype, $c->objectid, $c->date, $c->author,  $c->title,
            $c->hostname, $c->text, $c->left, $c->right,$c->status, $c->anonpost) = $result->fields;
            return $c;
        }
    }

    class CommentTreeNode extends TreeNode
    {
        public $pid = 0;
        public $modid;
        public $itemtype;
        public $objectid;
        public $date;
        public $author;
        public $title;
        public $hostname;
        public $text;
        public $status;
        public $anonpost;
        public $left = 0;
        public $right = 0;

        public $mindepth = 1;
        public $maxdepth;
        public $getchildren = true;
        public $returnitself = true;

        function getChildren()
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT id,
                                pid,
                                modid,
                                itemtype,
                                objectid,
                                date,
                                author,
                                title,
                                hostname,
                                text,
                                left,
                                right
                                status,
                                anonpost
                           FROM  " . $xartable['comments'] . " WHERE parent = ? ORDER BY cleft";
            $bindvars = array($this->id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            sys::import('xaraya.structures.sets.collection');
            $set = new BasecSet();
            while (!$result->EOF) {
                $c = new CommentTreeNode();
                list($c->id, $c->pid, $c->modid, $c->itemtype, $c->objectid, $c->date, $c->author,  $c->title,
                $c->hostname, $c->text, $c->left, $c->right,$c->status, $c->anonpost) = $result->fields;
                $collection->add($c);
            }
            return $collection;
        }

        function getParent()
        {
            return Comments::get($this->parent);
        }

        function getChildAt()
        {

        }

        function getChildCount()
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT COUNT(*) FROM " . $xartable['comments'] . " WHERE pid = ? ORDER BY left";
            $bindvars = array($this->id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            $fields = $result->fields;
            return array_pop($fields);
        }


        function isDescendant(CommentTreeNode $n)
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $query = '
                SELECT  P1.id
                FROM    ' . $xartable['comments'] . ' AS P1,
                        ' . $xartable['comments'] . ' AS P2
                WHERE   P2.left >= P1.left
                AND     P2.left <= P1.right
                AND     P2.id = ' . $n->id . '
                AND     P1.id = ' . $this->id . '
                AND     P1.id !=' . $n->id;

            $result = $dbconn->SelectLimit($query, 1);
            if (!$result) {return;}

            if (!$result->EOF) {
                return true;
            } else {
                return false;
            }
        }

        function load(Array $args)
        {
            foreach($args as $key => $value) $this->$key = $value;
        }

        function setfilter($args=array())
        {
            foreach ($args as $key => $value) $this->$key = $value;
        }
        function toArray()
        {
            return array('id' => $this->id, 'name' => $this->name);
        }
    }

    class CommentTree extends Tree
    {
        function createnodes(CommentTreeNode $node)
        {
            $data = xarModAPIFunc('categories',
                                    'user',
                                    'getcat',
    //                                array(
    //                                      'id' => false,
    //                                      'getchildren' => true));
                                  array('eid' => $node->eid,
                                        'id' => $node->id,
                                        'return_itself' => $node->returnitself,
                                        'getchildren' => $node->getchildren,
                                        'maximum_depth' => $node->maxdepth,
                                        'minimum_depth' => $node->mindepth,
                                        ));
             foreach ($data as $row) {
                $nodedata = array(
                    'id' => $row['id'],
                    'parent' => $row['parent'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'indentation' => $row['indentation'],
                    'image' => $row['image'],
                    'left' => $row['left'],
                    'right' => $row['right'],
                );
                if (!empty($node->idlist) && isset($node->idlist[$node->id])) {
                    $idlist = $node->idlist[$node->id];
                    if (in_array($row['id'],$idlist)) $this->treedata[] = $nodedata;
                } else {
                    $this->treedata[] = $nodedata;
                }
            }
            parent::createnodes($node);
        }
    }
?>
