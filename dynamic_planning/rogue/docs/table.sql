#$Id$
# Table structure for table tasks
#

CREATE TABLE nuke_tasks (
   taskid int(11) DEFAULT '0' NOT NULL auto_increment,
   trackid int(11) DEFAULT '0' NOT NULL,
   title varchar(80),
   text text,
   startdate date,
   enddate date,
   lastdate date,
   percent int(11) DEFAULT '0' NOT NULL,
   steps text,
   team text,
   PRIMARY KEY (taskid)
);


#
# Table structure for table tracks
#

CREATE TABLE nuke_tracks (
   trackid int(11) DEFAULT '0' NOT NULL auto_increment,
   trackname text,
   tracklead text,
   tracktext text,
   trackstatus text,
   trackcat int(11),
   PRIMARY KEY (trackid)
);
