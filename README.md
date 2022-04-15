
## Overview

### What is it?

See the
[introduction](xardocs/concepts.htm) and [user manual](xardocs/manual.html) of Galaxia for
more details on what this module is all about, and how to use it.

However, we clarify their definitions here a bit. In the workflow module
a distinction could be made between:

1.  The definition part: processes and activities
2.  The running part: instances and workitems

![Workflow overview
diagram](xarimages/whowhatwhenhow.gif)


So, you *define* processes with activities and you *run* instances to
complete workitems. During the use of the module you *Manage* processes
and activities. There is functionality available to *monitor* all of
processes, activities, instances and workitems. Each workitem can be
completed by one or more process roles. In Xaraya, you map the users or
groups in the roles module to these process roles. The description given
here will be reflected in the Xaraya interface for the workflow module.

### Getting started

1.  make sure that the webserver can write to the directory
    `var/processes` (chmod 777 or equivalent)
2.  install [GraphViz](http://www.research.att.com/sw/tools/graphviz/)
    if you want to generate some nice process graphs, and adapt
    GRAPHVIZ\_DIR in `modules/workflow/lib/galaxia/config.php` if
    necessary;
3.  go to the Dynamic Data [Import Object
    Definition](index.php?module=dynamicdata&type=util&func=import) page
    and paste the content of the file
    [cdcollection3\_dd.xml](xardata/cdcollection3_dd.xml)
    in the text area to create the sample database table;
4.  go to [Admin
    Processes](index.php?module=workflow&type=admin&func=processes) and
    upload the file
    [cdcollection3.xml](xardata/cdcollection3.xml)
    to create a sample process;
5.  map some Xaraya users to the 'admin' and 'user' workflow roles for
    that process, or add all current users from a Xaraya group to one of
    the workflow roles \[TODO: Galaxia does not support a permanent
    mapping between Xaraya groups and workflow roles at the moment\]
6.  mark the process as *active* once you no longer have the errors
    "Role: ... is not mapped"
7.  go to the [Workflow User Interface](index.php?module=workflow) and
    play around :-)
8.  come back here and try some of the monitoring and administration
    options
9.  create your own fancy processes and submit them back to the open
    source community

Note: this is mostly a 1-to-1 conversion of the current Galaxia
interface scripts from TikiWiki, and a quick & dirty conversion of the
templates that go with them. The sample CD Loan process has been
modified a bit to be more representative of actual processes, but is
otherwise unchanged.

With version 1.1, you can also automatically start a workflow process
after some module item is created, updated or deleted. To try this out,
you should :

1.  upload the
    [review\_articles.xml](xardata/review_articles.xml)
    file to create the Review Articles process
2.  assign some users to the 'editors' workflow role and mark the
    process as active
3.  enable the workflow hooks for articles
4.  configure the workflow module so that the Review Articles - start
    activity is started when Create hooks are called from articles
5.  create a new article and have fun...

Version 1.3 adds the support for two BL tags : \<xar:workflow-activity
activityId="123" ... /\> and \<xar:workflow-status ... /\>. Those can be
used in any template to run some workflow activity and show its output,
resp. to show you the status of all the instances \*you\* started. To
try that out, you should :

1.  delete your old "Music Loan 3" process, import the new version,
    assign users and mark as active
2.  find out what the activityId of activity "Request CD" is, in that
    new process
3.  in some template, add the following BL tags :
    \<xar:workflow-activity activityId="123"/\> (with 123 the
    activityId) and \<xar:workflow-status/\>
4.  go to that page and enjoy...

Version 1.4 also adds the BL tag \<xar:workflow-instances .../\> to show
the instances that are assigned/accessible to you (i.e. your task list).

**Patches and contributions are most welcome ;-)**

*Based on the [Galaxia Workflow
Engine](http://tikiwiki.org/tiki-index.php?page=GalaxiaWorkflow)*

``` 
    // Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
    // All Rights Reserved. See copyright.txt for details and a complete list of authors.
    // Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
    
```

### Further Information

Further information on the Workflow module can be found at

  - Workflow Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/188.html "Workflow Module - Xaraya Extension 188").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on Workflow found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/workflow/ "Related documentation on Workflow")

** Workflow Module Overview**  
 Version 1.0.0  2006-03-08

