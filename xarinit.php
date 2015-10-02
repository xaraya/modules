<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the sitemapper module
 *
 */

    function sitemapper_init()
    {
    # --------------------------------------------------------
    #
    # Set up tables
    #
        sys::import('xaraya.structures.query');
        $q = new Query();
        $prefix = xarDB::getPrefix();

        $query = "DROP TABLE IF EXISTS " . $prefix . "_sitemapper_links";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_sitemapper_links (
          id                   integer unsigned NOT NULL auto_increment,
          type                 varchar(64) NULL,
          subtype              varchar(64) NULL,
          location             varchar(254) NULL,
          language             varchar(64) NULL,
          access               tinyint unsigned default 0 NOT NULL,
          last_modified        integer unsigned default 0 NOT NULL,
          change_frequency     varchar(64) NULL,
          priority             float default 0.5 NOT NULL,
          change_count         integer unsigned default 0 NOT NULL,
          state                tinyint unsigned default 0 NOT NULL,
        PRIMARY KEY  (id)
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_sitemapper_maps";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_sitemapper_maps (
          id                   integer unsigned NOT NULL auto_increment,
          context_hash         varchar(64) NULL,
          context              text,
          links                integer unsigned default 0 NOT NULL,
          chunks               integer unsigned default 0 NOT NULL,
          max_filesize         integer unsigned default 0 NOT NULL,
          state                tinyint unsigned default 0 NOT NULL,
          modified             integer unsigned default 0 NOT NULL,
        PRIMARY KEY  (id)
        )";
        if (!$q->run($query)) return;
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_sitemapper_engines";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_sitemapper_engines (
          id                   integer unsigned NOT NULL auto_increment,
          name                 varchar(64) NULL,
          submission_url       varchar(254) NULL,
          help_url             varchar(254) NULL,
          state                tinyint unsigned default 0 NOT NULL,
          modified             integer unsigned default 0 NOT NULL,
        PRIMARY KEY  (id)
        )";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_sitemapper_sources";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_sitemapper_sources (
          id                   integer unsigned NOT NULL auto_increment,
          module               varchar(64) NULL,
          source_type          tinyint unsigned default 1 NOT NULL,
          gen_type             varchar(64) NULL,
          gen_function         varchar(64) NULL,
          dis_type             varchar(64) NULL,
          dis_function         varchar(64) NULL,
          parameters           varchar(254) NULL,
          state                tinyint unsigned default 0 NOT NULL,
          modified             integer unsigned default 0 NOT NULL,
        PRIMARY KEY  (id)
        )";
        if (!$q->run($query)) return;
    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewSitemapper','All','sitemapper','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadSitemapper','All','sitemapper','All','All','ACCESS_READ');
        xarRegisterMask('EditSitemapper','All','sitemapper','All','All','ACCESS_EDIT');
        xarRegisterMask('AddSitemapper','All','sitemapper','All','All','ACCESS_ADD');
        xarRegisterMask('ManageSitemapper','All','sitemapper','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminSitemapper','All','sitemapper','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ManageSitemapper','All','sitemapper','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminSitemapper','All','sitemapper','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up configuration modvars (general)
    #
            $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'sitemapper'));
            $module_settings->initialize();

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('sitemapper', 'defaultmastertable', 'sitemapper_engines');
        xarModVars::set('sitemapper', 'submit_engines', 'a:0:{}');
        xarModVars::set('sitemapper', 'file_create_xml', true);
        xarModVars::set('sitemapper', 'file_create_zip', true);
        xarModVars::set('sitemapper', 'xml_filename', 'sitemap');
        xarModVars::set('sitemapper', 'zip_filename', 'sitemap');
        xarModVars::set('sitemapper', 'template', '
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <xar:foreach in="$items" value="$item">
    <url>
      <loc>#$item["location"]#</loc>
      <xar:set name="last_modified">date("Y-m-d",$item["last_modified"])</xar:set>
      <lastmod>#$last_modified#</lastmod>
      <changefreq>#$item["change_frequency"]#</changefreq>
      <priority>#$item["priority"]#</priority>
    </url>
  </xar:foreach>
</urlset>');
    # --------------------------------------------------------
    #
    # Set up hooks
    #
//        sys::import('xaraya.structures.hooks.observer');
//        $observer = new BasicObserver('sitemapper');
//        $subject = new HookSubject('comments');
//        $subject->attach($observer);

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'sitemapper';
        $objects = array(
//                       'sitemapper_maps',
                       'sitemapper_engines',
                       'sitemapper_sources',
                       'sitemapper_links',
                         );

        if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

        return true;
    }

    function sitemapper_upgrade()
    {
        return true;
    }

    function sitemapper_delete()
    {
        // Only change the next line. No need for anything else
        $module = 'sitemapper';

        if(!xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module))) return false;

        return true;
    }

?>