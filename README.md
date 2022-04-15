
## Overview

### What is it?

*SiteTools is a utility module that provides a number of related
database and site 'housekeeping' utilities*:

By choosing an option from the SiteTools menu. At the present time you
can:

  - Optimize your MySQL database tables
  - Backup your MySQL database tables (MySQL 3.22 or higher required)
  - Simple SQL Terminal - to issue SQL commands
  - Browse template cache files
  - Delete Template, RSS or ADOdb cache files
  - Web Link Checker - check web links, image links and so on.

### How to use it?

1.  SiteTools optimize and backup functions currently only work with
    MySQL. Further classes are in the making for Postgres, and SQLite.
2.  SiteTools only provides Administration, options and thus requires
    administration priviledges.
3.  Start by checking settings in the SiteTools configuration options.
4.  Warning: Default cache and backup directory paths should only be
    changed if alternative cache or backup paths have already been
    created and are writeable - chmod 777. At this time, backup paths
    are configurable, but cache paths are not configurable from within
    Xaraya administration.

#### Using Scheduler with Sitetools

A number of SiteTool functions also work in conjunction with the
Scheduler module to schedule regular maintenance activies. At the
present time the following functions are supported with Scheduler:

1.  Backup Database
2.  Optimize Database

You must have the Scheduler module installed and active to see further
scheduler options in your Sitetools Module. Once Scheduler is installed,
you can choose to set the interval for these functions. See the
Scheduler Module for more details.

### Included Blocks

There are no included blocks at this time.

### Included Hooks

There are no included hooks at this time.

### Further Information

Further information on the Sitetools module can be found at

  - Sitetools Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/887.html "Sitetools Module - Xaraya Extension 887").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on the Sitetools found at
    [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/sitetools/ "Related documentation on Sitetools")

** Sitetools Module - Overview**  
 Version 1.0.1  2005-11-08

