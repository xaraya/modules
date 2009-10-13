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
    public $version    = 'ckeditor';
    
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
            $editorpath = sys::code() . 'modules/ckeditor/xartemplates/includes/fckeditor/';
            $name = $this->getCanonicalName($data);
            $this->editor = new FCKeditor($name) ;
            $this->editor->BasePath = $editorpath;
            $this->editor->Value = $this->value;
            $data['editor'] = $this->editor;
        }
        $data['version'] = $this->version;
        return parent::showInput($data);
    }
}


?>