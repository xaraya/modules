<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 * Purpose of file: Publications Block
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_GlossaryBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'glossary';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Glossary';  // Block type display name
    protected $text_type_long   = 'Show a glossary summary in a side block.'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared
    protected $show_help    = true; // let the subsystem know if this block type has a help() method

    public $paramname = 'glossaryterm';
    public $ptid = 0;
    public $cid = 0;
    
    public function display()
    {
        $vars = $this->getContent();
        
        if (!xarVar::fetch($vars['paramname'], 'str', $glossaryterm, null, XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!$glossaryterm) {
            return;
        }

        $articlecriteria = array();
        $articlecriteria['title'] = $glossaryterm;

        if (!empty($vars['ptid'])) {
            $articlecriteria['ptid'] = $vars['ptid'];
        }

        if (!empty($vars['cid'])) {
            $articlecriteria['withcids'] = true;
        }

        // Attempt to find an article with this title and optional category/pubtype.
        $article = xarMod::apiFunc('publications', 'user', 'get', $articlecriteria);

        if (!empty($vars['cid']) && array_search($vars['cid'], $article['cids']) === null) {
            // Category not assigned to article.
            unset($article);
        }

        // Matching glossary item found.
        if (!empty($article)) {
            $vars['definition'] = $article['summary'];
            $vars['term'] = $glossaryterm;
            $vars['detailurl'] = xarModURL(
                'publications',
                'user',
                'display',
                array('id' => $article['id'], 'ptid' => $article['pubtype_id'])
            );
            $vars['detailavailable'] = !empty($article['body']);
        }

        // Replace the string '{term}' in the block title with the term.
        // Note: the prep display prevents injected tags being rendered.
        // The title of a block does not go through any further tag stripping
        // because it is normally under admin control (the admin may wish to
        // add working tags to the title).
        $this->setTitle(str_replace('{term}', xarVar::prepForDisplay($glossaryterm), $this->title));
        
        return $vars;
    }

    public function modify()
    {
        $vars = $this->getContent();
        // Pub type drop-down list values.
        $vars['pubtypes'] = xarMod::apiFunc('publications', 'user', 'get_pubtypes');

        // Categories drop-down list values.
        $vars['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');

        // Defaults.
        if (empty($vars['ptid'])) {
            $vars['ptid'] = 0;
        }
        if (empty($vars['cid'])) {
            $vars['cid'] = 0;
        }
        if (empty($vars['paramname'])) {
            $vars['paramname'] = 'glossaryterm';
        }

        $vars['bid'] = $this->block_id;

        // Return output
        return $vars;
    }
    
    public function update()
    {
        xarVar::fetch('paramname', 'str:1:20', $vars['paramname'], 'glossaryterm', XARVAR_NOT_REQUIRED);
        xarVar::fetch('ptid', 'int:0:', $vars['ptid'], 0, XARVAR_NOT_REQUIRED);
        xarVar::fetch('cid', 'int:0:', $vars['cid'], 0, XARVAR_NOT_REQUIRED);
        $this->setContent($vars);
        return true;
    }
    
    public function help()
    {
        return (
            'Use {term} in the block title as a placeholder for the glossary term.'
            . ' Glossary terms will match an article title.'
            . ' In the block admin, choose the optional pub type and category that will define the glossary terms.'
            . ' The glossary term displayed will be the article summary.'
            . ' A link to the full article will be provided if the body of the article contains text.'
        );
    }
}
