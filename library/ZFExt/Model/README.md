
Source.php
===

This is the model for the target of the application.  It is in charge of interacting with the RDBMS that you want to work with.  It has the databases that you want to model in the application.  All interaction with it goes through here.  It is strictly read-only.  That's why this is not so much of a administration tool as it is a database visualization tool.  All writes are done to the Application model, keeping information about the databases there and modeling it as needed.

Application.php:
===

This is for keeping administration information.  This is in charge of keeping information about where in the grid you placed a table.  This is in charge of comments you make in the grid.  This is in charge of leads between tables.  All of Application information is in the dbag database.  At the install phase of this application, the database names from the Source model must be copied into dbag.table_schema so the Application will now know which databases it is allowed to present and work on with the user.  Originally I was going use a 'SHOW databases' command in the Source model to get the names but I found that Zend requires you to connect to a named database and there was no prescribed way to call something like $db->getDatabases();  I need to keep an eye out on a solution to this.


