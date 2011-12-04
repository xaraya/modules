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
sys::import('xaraya.structures.containers.blocks.basicblock');

class Keywords_CloudBlock extends BasicBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'cloud';
    protected $module           = 'keywords'; // module block type belongs to, if any
    protected $text_type        = 'Keywords Cloud';  // Block type display name
    protected $text_type_long   = 'Display keywords cloud'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $multiplier = 'wordcount';
    public $cloud_font_min;
    public $cloud_font_max;
    public $cloud_font_unit;

        public $cloudtype           = 1;
        public $color               = '#000000';
        public $background          = '#FFFFFF';

    public function init()
    {
        if (empty($this->cloud_font_min))
            $this->cloud_font_min = xarModVars::get('keywords', 'cloud_font_min', 1);
        if (empty($this->cloud_font_max))
            $this->cloud_font_max = xarModVars::get('keywords', 'cloud_font_max', 3);
        if (empty($this->cloud_font_unit))
            $this->cloud_font_unit = xarModVars::get('keywords', 'cloud_font_unit', 'em');
    }
    function display()
    {
        $data = $this->getContent();
        $items = xarMod::apiFunc('keywords', 'words', 'getwordcounts',
            array(
                'skip_restricted' => true,
            ));
        if (empty($items)) return;
        
        $counts = array();
        foreach ($items as $item)
            $counts[$item['keyword']] = $item['count'];
        $font_min = $this->cloud_font_min;
        $font_max = $this->cloud_font_max;
        $font_unit = $this->cloud_font_unit;
        $min_count = min($counts);
        $max_count = max($counts);
        $range = $max_count - $min_count;
        if ($range <= 0)
            $range = 1;
        $font_range = $font_max - $font_min;
        if ($font_range <= 0)
            $font_range = 1;
        $range_step = $font_range/$range;
        foreach ($items as $k => $item) {
            $count = $counts[$item['keyword']];
            $items[$k]['weight'] = $font_min + ( ( $count - $min_count ) * $range_step );
        }
        $data['items'] = $items;
        $data['unit'] = $font_unit;                   
            
        return $data;
        /* 
            // @TODO: figure out where/how to implement these options 
            $vars = $this->getContent();
            $vars['tags'] = array();
            switch ($vars['cloudtype']) {
                case 1:
                break;
                case 2:
                case 3:
                    $vars['tags'] = xarMod::apiFunc('keywords','user','getkeywordhits',array('cloudtype' => $vars['cloudtype']));
                break;
            }
            return $vars;
        */
    }

    function modify()
    {
        $data = $this->getContent();

        $data['font_units'] = array(
            array('id' => 'em', 'name' => 'em'),
            array('id' => 'pt', 'name' => 'pt'),
            array('id' => 'px', 'name' => 'px'),
            array('id' => '%', 'name' => '%'),
        );

        return $data;
    
        /* 
            // @TODO: figure out where/how to implement these options         
            $data['status'] = '';
            switch ($data['cloudtype']) {
                default:
                case 1:
                    if (!xarModIsAvailable('categories')) $data['status'] = 'not_available';
                    break;
                case 3:
                    if (!xarModIsAvailable('keywords')) $data['status'] = 'not_available';
                    break;
                }
            }
            return $data;
        */
    }

    public function update()
    {
        if (!xarVarFetch('cloud_font_min', 'int:1:',
            $vars['cloud_font_min'], 1, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('cloud_font_max', 'int:1:',
            $vars['cloud_font_max'], 3, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('cloud_font_unit', 'pre:trim:lower:enum:em:pt:px:%',
            $vars['cloud_font_unit'], 'em', XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;
        /* 
            // @TODO: figure out where/how to implement these options 
            // Get the cloud type
            if (!xarVarFetch('cloudtype',  'int',      $vars['cloudtype'], $this->cloudtype, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('color',      'str:1:',   $vars['color'],          $this->color,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('background', 'str:1:',   $vars['background'],           $this->background,XARVAR_NOT_REQUIRED)) return;
            $this->setContent($vars);
            return true;
        */
    }
}
?>
