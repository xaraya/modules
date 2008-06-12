<?php
    sys::import('xaraya.structures.sets.collection');

    class MapLocations extends BasicSet implements Locations
    {
    }

    interface Locations
    {
    }

    class BasicMapObject extends Object implements MapObject
    {
        public $id;
        public $extid;
        public $name;
        public $description;
        public $windowtext;
        public $longitude;
        public $latitude;

        public function __construct($args=array())
        {
            $this->loaddata($args);
        }
        public function toArray()
        {
            return array(
                    'id' => $this->id,
                    'extid' => $this->extid,
                    'name' => $this->name,
                    'description' => $this->description,
                    'windowtext' => $this->windowtext,
                    'longitude' => $this->longitude,
                    'latitude' => $this->latitude,
                );
        }
        public function add(array $args)
        {
            $this->loaddata($args);
        }
        function hash()
        {
            return sha1(serialize($this));
        }
        protected function loaddata(array $args)
        {
            if (isset($args['id'])) $this->id = $args['id'];
            if (isset($args['extid'])) $this->extid = $args['extid'];
            if (isset($args['name'])) $this->name = $args['name'];
            if (isset($args['description'])) $this->description = $args['description'];
            if (isset($args['windowtext'])) $this->windowtext = $args['windowtext'];
            if (isset($args['longitude'])) $this->longitude = $args['longitude'];
            if (isset($args['latitude'])) $this->latitude = $args['latitude'];
        }
    }

    interface MapObject
    {
        public function add(array $args);
    }
?>