<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 * Purpose of file: Articles Block
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Articles_GlossaryBlock extends BasicBlock implements iBlock
{
    public $name                = 'GlossaryBlock';
    public $module              = 'articles';
    public $text_type           = 'Glossary';
    public $text_type_long      = 'Show a glossary summary in a side block';
    public $pageshared          = 0;
    public $usershared          = 1;
    public $nocache             = 1;

    public $paramname   = 'glossaryitem';
    public $ptid        = 0;
    public $cid         = 0;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $args=array())
    {
        $data = parent::display($args);
        if (empty($data)) return;

        $vars = $data['content'];

        // Get the glossary parameter.
        // TODO: make parameter name configurable.
        xarVarFetch($vars['paramname'], 'str', $glossaryterm, NULL, XARVAR_NOT_REQUIRED);

        if (empty($glossaryterm)) {
            // No glossary parameter found.
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
        $article = xarMod::apiFunc('articles', 'user', 'get', $articlecriteria);

        if (!empty($vars['cid']) && array_search($vars['cid'], $article['cids']) === NULL) {
            // Category not assigned to article.
            unset($article);
        }

        // Matching glossary item found.
        if (!empty($article)) {
            $vars['definition'] = $article['summary'];
            $vars['term'] = $glossaryterm;
            $vars['detailurl'] = xarModURL(
                'articles', 'user', 'display',
                array('aid' => $article['aid'], 'ptid' => $article['pubtypeid'])
            );
            $vars['detailavailable'] = !empty($article['body']);
        }

        // Replace the string '{term}' in the title with the term.
        // Note: the prep display prevents injected tags being rendered.
        // The title of a block does not go through any further tag stripping
        // because it is normally under admin control (the admin may wish to
        // add working tags to the title).
        $data['title'] = str_replace('{term}', xarVarPrepForDisplay($glossaryterm), $data['title']);

        $data['content'] = $vars;
        return $data;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        // Pub type drop-down list values.
        $data['pubtypes'] = xarMod::apiFunc('articles', 'user', 'getpubtypes');

        // Categories drop-down list values.
        $data['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');

        // Defaults.
        if (empty($data['ptid'])) {
            $data['ptid'] = 0;
        }
        if (empty($data['cid'])) {
            $data['cid'] = 0;
        }
        if (empty($data['paramname'])) {
            $data['paramname'] = 'glossaryterm';
        }

        // Return output
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        $vars = array();

        xarVarFetch('paramname', 'str:1:20', $vars['paramname'], 'glossaryterm', XARVAR_NOT_REQUIRED);
        xarVarFetch('ptid', 'int:0:', $vars['ptid'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('cid', 'int:0:', $vars['cid'], 0, XARVAR_NOT_REQUIRED);

        $data['content'] = $vars;
        return $data;
    }

    public function help(Array $data=array())
    {
        $data = parent::getInfo($data);
        return $data;
    }

}

?>