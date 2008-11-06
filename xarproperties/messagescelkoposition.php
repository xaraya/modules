<?php
    sys::import('modules.categories.xarproperties.celkoposition');

    class MessagesCelkoPositionProperty extends CelkoPositionProperty
    {
        public $id           = 30088;
        public $name         = 'messagescelkoposition';
        public $desc         = 'Messages Celko Position';
        public $reqmodules   = array('messages');

        public $initialization_celkoparent_id = 'pid';
        public $initialization_celkoname = 'title';

        function __construct(ObjectDescriptor $descriptor)
        {
            parent::__construct($descriptor);
            $this->filepath   = 'modules/messages/xarproperties';

            sys::import('modules.messages.xartables');
            xarDB::importTables(messages_xartables());
            $xartable = xarDB::getTables();
            $this->initialization_itemstable = $xartable['messages'];
        }
    }
?>