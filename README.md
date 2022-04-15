
## Overview

Xarpages is basically a module for managing pages on a site. It's main
aim is to provide a rigid framework within which a site designer can
structure the site from a visitor's point of view. There is an emphasis
on a hierarchical page structure, which feeds into the menus and short
URLs.

Xarpages does not manage content. It does not hold or store any content
directly, rather it relies on other modules to provide that content.
Content may be provided using as simple a method as creating a Dynamic
Data text field for entering HTML. It may be as complex as hooking in
categories, looking up stories, rendering them in a special template,
pulling in blocks related to that page and adding in additional dynamic
content through extensions to the short URL.

## The Page

The basic item in Xarpages is the page. Putting the page type aside for
one moment, each page may have a combination of these features:

  - A name and description
  - A position in the hierarchy
  - Optional Dynamic Data fields that are fed into the item template
  - An item template used to render the content of page
  - A page template that is always used for that page
  - A theme in which the page is always displayed
  - The page may be registered as a module alias
  - A custom function that can modify any of the above
  - Custom short URL functions that can extend the default short URL
    handling

Since all these properties can be configured, it is possible to define
the exact look and behavior of a given page.

Many of the above properties are inherited from pages further down in
the hierarchy, i.e. towards the root of the tree in which the page
appears. For example, if a page is set to use theme X, then all it's
descendant pages, assuming they do not over-ride the theme, will also
appear in theme X.

## Accessing a Page

Each page has a unique numeric page ID, or 'pid', and can be accessed
using the pid. A more useful way of accessing the pages is to use the
hierarchy presented when short URLs are enabled for the module. A page
can be accessed in two ways through the short URL:

  - By listing the full hierarchical path to the page, e.g.
    /index.php/xarpages/about/staff/directors
  - By naming any page that has been set up as a module alias, e.g.
    /index.php/directors

Note also that if the 'about' page has been registered as a module
alias, then the first example could shortened to
/index.php/about/staff/directors.

With careful use of mod\_rewrite(), or an equivalent, and modifications
to Xaraya global options, the 'index.php' part of the path can be
removed also, making the page structure almost mimic a static site.

## Customizing the Short URL

The components of the path are the names of the pages. So above there is
a page named 'about', with a child page named 'staff', and so on. If the
path is extended beyond the tree of real pages, then the custom short
url function will kick in. After as many components of the path as
possible are scanned to determine the page to be displayed, any further
unmatched components will usually be ignored. However, by creating a
custom URL decode function, and linking it to the page, a developer can
use the remaining components to feed into the page itself.

For example, if there were several directors in the list of staff, a
separate page could be created for each one. So Jim may have a page
/about/staff/directors/jim, containing his contact details. However,
that could become difficult to manage if dealing with dozens or hundreds
of directors. One way to solve that would be to create a single page
/about/staff/directors and then a custom URL decode function that reads
any additional path components, and looks up the director details for
passing to that page. The additional path may be 'jim', 'jane', or
perhaps numeric IDs - it is up to the developer.

The main difference between customizing this module to do the decoding
and lookups, and writing a completely new module, is that this module is
designed to be customized; most of the hard work is done for you and
only a small amount of customization is necessary. What's more, the
module can be upgraded without over-writing your changes, since there
are APIs reserved just for customizations.

## Custom Function

As well as customizing the short URL, a developer can add a custom
processing function to the page. That function is given all the data for
the page, and may chose to modify that data before it is passed to the
template for rendering. For example, the function may read some
additional GET parameters and use those to fetch, say, an article from a
certain category. Dynamic Data fields defined for the page may be used
as parameters for this custom function - the DD data doesn't all have to
be displayed directly.

Custom functions can be chained together by listing them in sequence,
each separated by a semi-colon.

## Page Types

As many different page types as desired can be defined. Different page
types allow different modules to be hooked into them. They also each use
a different template 'base'.

## Page Status

There are three main statuses for a template:

  - Active
  - Inactive
  - Template
  - Empty

Only active and empty pages are normally displayed. A future enhancement
may pull the status into the privileges so that the display of inactive
pages could be enabled for certain user groups. Inactive pages are not
normally displayed. They exist as placeholders, while a page is being
developed, or when a page has been withdrawn.

An empty page is one which has no content, and to which you cannot
navigate to directly. Attempting to navigate to an empty page will take
you to the page's first displayable child page. It allows an
administrator to set a 'placeholder' page to define a section, without
that placeholder page having to carry content.

A template-status page is used as a template when creating new pages.
Just create a template page as normal, set its status to 'template', and
that page will be used to provide default values for future created
pages of that type. Currently, only one template page (the first one
that comes out of the database) for each page type is supported, but
future enhancements will allow you to select from a range of templates.

## Item Templates

Each page type uses a default item template with the same name as that
type. For example, page type 'html' will use a default item template
"page-html.xt". Any specific page may request a more specific template
by name. For example, page "directors" of type "html" may define
"contact" as its template, which come from the theme
"page-html-contact.xt", falling back to "page-html.xt" if the more
specific template does not exist.

## Navigation Block

A navigation block has been designed for use with this module. The block
should be able to support all kinds of menus, including CSS, DHTML,
nested lists, multi-level, split menus etc. Each menu block has the
following features:


  - **Multi-homed flag.** This determines whether the root page will be
    displayed in the menu or hidden (just treated as a holding page).
    When hidden, the top level of the menu consists of all the children
    of the root page, i.e. multiple top-level pages.
    
  - **Current page source.** This determines how the 'current page' is
    determined. It can either come from a cache variable - normally
    created when a xarpages page is displayed, or it can be fixed to a
    specified page. If fixed, then the 'default page' is used as the
    current page at all times.

  - **Default Page.** The default page is used in two circumstances.
    Firstly it can be used as a fixed page, always displayed as
    'current' within the block. Secondly it can be used as a default
    when no current page is defined externally (i.e. not passed into the
    block as the 'current\_pid' attribute, and not set in the cache by a
    module). This is optional. If no xarpage page is displayed, and no
    default page is set, then the block will not be displayed at all.

  - **Root Pages.** By default, when the block is set to 'automatic' it
    will display the full menu for the tree under which the current page
    is located. You may wish to limit that to just certain trees, i.e.
    for the block only to display a menu if the current xarpages page is
    within certain bounds. One or more root pages can be set, and these
    will be checked at runtime to ensure the current page is located
    somewhere within the trees defined by these root pages. If the
    current page is outside of all the root pages, then the block is not
    displayed. If no root pages are defined, then there are not bounds,
    and a menu will be displayed for every xarpages page. A second
    function of the root pages is to allow menus to be split up into
    smaller chunks. For example, a side menu may display one of five
    sub-menus, each branches of the main page structure. The appropriate
    menu will be displayed only when a page from that menu is being
    displayed. Another example could be a top menu handling the first
    level of the main menu, and a side menu handling levels 2 onwards.

  - **Max Level.** Used in conjunction with the root pages, this setting
    defines the maximum number of levels a menu block will display.
    Anything below that level will be truncated.

There is a simple textual nested list template set up for this block,
that should demonstrate how it works. A more complex example shows how
the menu could be formatted as a side menu. The more complex template
can be invoked by entering *;css-side-menu1* as the 'instance template'
of the block. Further menus can be built up using similar menus.

If you have examples of generic menus that could be included in this
module, please feel free to pass them on to the author.

## Privileges

The privileges have been designed to allow sub-editors to manage
content, without being able to modify the structure of the pages.

See the privileges.txt file in the documentation set of this module.

## TODO List

See the [TODO.txt](modules/xarpages/xardocs/TODO.txt) file in the
documentation set of this module for a full list.

The main missing functionality at this point are an import/export
function, which would also be used to setup initial sample pages, and
blocks to display hierarchical menus based on the current page and its
position in the hierarchy.

## Author

Jason Judge

<judgej@xaraya.com>

