################################################################################
##                                                                            ##
##                              Todolist.php                                  ##
##                              ------------                                  ##
##                                                                            ##
################################################################################

what is it?
-----------
"Todolist.php" is a set of PHP scripts that create a web-based list of things to
do.  Items can be added and removed, and are sorted by an assigned priority. 
Also it is possible to assign special tasks to another person and for example
to specify a due date. ToDoList is multilingual (although some languages are 
still under development). Lots of unmentioned features.

"Todolist.php" was inspired by "todo", some CGI-Scripts, written by Marc
Bayerkohler and Joel Thomas. It was started because I wanted to learn PHP3 and
really needed an easy to use todo-list for the IT-Service-Dep. I worked at.
Max joined in April 2001 and brought a more extensible rewrite of much of it
with him. It's a pitty he's quite occupied with his studies so he wasn't able to
continue coding since summer.

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.
This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
<http://www.opensource.org/licenses/gpl-license.html> for more details.

If you have any suggestions or wishes for future versions feel free to contact
me and subscribe to the mailing-list (very low traffic).

Have fun,
Jörg Menke <jhm@gmx.net>


System requirements
-------------------
All you need is an PHP-enabled web-server (Apache preferred) and mySQL. It is
developed with a mod_php-Version of PHP and Apache under Linux. When you use the
CGI-Version of PHP you'll have some (smaller) limitations, p.e. remembering the
last person that logged in doesn't work at the moment. Other databases will
(maybe) be supported in the future.


Features
--------
- Assign due-dates, responsible users, etc. to tasks
- Sort TodoList after different columns
- Search
- Graphical Administration of users/projects
- multilingual, every user can use his preferred language. Its really easy to
  add new languages.
- more...


How do I install?
-----------------
- put the .php files somewhere where they are accessible by the webserver
- create the database-tables (just run the "databasestructure.sql" in mysql)

  $ mysql -u DBUSER DBINSTANCE < databasestructure.sql

- Login as the admin user, and set up whatever users you need. A "test"-user
  with password "test" is also created as example by the setup sql script.
- with the admin-user you can also create projects and assign members to them.
  Every newly created user is automatically member of project '1' which is
  named - guess it - 'default'. The default-project shouldn't be deleted.
- edit the "include/config.inc"-file to fit your needs.
- Change the password for user "admin"! (Default password is "admin"....), or
  give a new user admin rights and delete the "admin" user. An admin-user is
  quite the same as a normal user but has access to the administration-forms.
  (You can have more than one user with admin-rights)


How do I update?
----------------
Please refer to doc/UPDATE for information.


How to support the project?
---------------------------
If you like Todolist.php, then please consider supporting me. Developing this
software keeps me busy a long time, and by giving support you will help it to
develop faster. 

Why should I support Todolist.php?

Think of the time saved by using Todolist.php rather than writing your own
Todolist-software or keeping track of your tasks on paper. So I ask you to give
back a little to support this project and guarantee future maintainance and
development. You also have access to unlimited free technical support in the
forums (http://sf.net/forum/?group_id=8452), or via the projects mailing lists
(http://sourceforge.net/mail/?group_id=8452), to help you if anything goes wrong
which many other programs (even paid ones) do not provide.
 
Great, so how can I support you?

- Help me to update the translations (my Japanese isn't the best... :-) ) and
  documentation.
- Report Bugs if you find any and also fixes or ideas for feature/usability-
  enhancements
- Rate and review Todolist.php. Please add it to any script directories it isn't
  in at present.
- Buy me something from my Amazon-Wishlist
  (http://www.amazon.de/exec/obidos/wishlist/1Q1KPRYFF96UM/)
- Donate to account "jhm@gmx.net" at PayPal.
################################################################################
$Id$
vi:tw=80