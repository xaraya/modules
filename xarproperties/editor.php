<?php
/**
 * Editor GUI property
 *
 * @package modules
 * @copyright (C) 2009 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 */

/**
 * Handle the editor property
 * Utilizes JavaScript based WYSIWYG Editor, CKEditor
 *
 * @author M. Lutolf (mfl@netspan.ch)
 * @package ckeditor
 */
sys::import('modules.base.xarproperties.textarea');

class EditorProperty extends TextAreaProperty
{
    public $id         = 30091;
    public $name       = 'editor';
    public $desc       = 'Editor';
    public $reqmodules = array('ckeditor');
    
    public $editor     = null;
    public $version    = 'fckeditor';
    
    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'ckeditor';
        $this->template  = 'editor';
        $this->filepath  = 'modules/ckeditor/xarproperties';        
    }

    public function showInput(Array $data = array())
    {
        if ($this->version == 'fckeditor') {
            sys::import('modules.ckeditor.xartemplates.includes.fckeditor.fckeditor');
            $editorpath = 'modules/ckeditor/xartemplates/includes/fckeditor/';
            if(!isset($data['name'])) $data['name'] = $this->name;
            if(!empty($this->_fieldprefix) || $this->_fieldprefix === 0)  $prefix = $this->_fieldprefix . '_';
            // A field prefix added here can override the previous one
            if(isset($data['fieldprefix']))  $prefix = $data['fieldprefix'] . '_';
            if(!empty($prefix)) $data['name'] = $prefix . $data['name'];
            $this->editor = new FCKeditor($data['name']) ;
            $this->editor->BasePath = $editorpath;
            $this->editor->Value = $this->value;
            $data['editor'] = $this->editor;
        }
        $data['version'] = $this->version;
        return parent::showInput($data);
    }
}


?>