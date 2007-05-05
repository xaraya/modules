<?php
    sys::import('modules.dynamicdata.class.objects.base');

    class Category extends DataObject
    {
        public $parentindices = array();

        function createItem(Array $args = array())
        {
            extract($args);

            // there may not be an entry point passed
            $entry = isset($entry) ? $entry : array();

            // replace parentid imported with the local ones
            $parentindex = $args['parent_id'];
            if (in_array($parentindex,array_keys($this->parentindices))) {
                $args['parent_id'] = $this->parentindices[$parentindex];
            } else {
                // there could be more than 1 entry point, therefore the array
                if (count($entry > 0)) {
                    $this->parentindices[$parentindex] = array_shift($entry);
                    $args['parent_id'] = $this->parentindices[$parentindex];
                } else {
                    $args['parent_id'] = 0;
                }
            }

            // we have all the values, do it
            $id = parent::createItem($args);

            // add this category to the list of known parents
            $this->parentindices[$args['id']] = $id;

            // do the Celko dance and update all the left/right values
            return xarModAPIFunc('categories','admin','updatecelkolinks',array('cid' => $id));
        }

        function updateItem(Array $args = array())
        {
            $id = parent::updateItem($args);
            return xarModAPIFunc('categories','admin','updatecelkolinks',array('cid' => $id));
        }
    }
?>
