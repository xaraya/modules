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
    
    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'ckeditor';
        $this->template  = 'editor';
        $this->filepath  = 'modules/ckeditor/xarproperties';
    }
}


?>