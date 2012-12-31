
Application.php:
===

    This is the model for the application.  It is in charge of interacting with the RDBMS that you want to work with.  It has the databases that you want to model in the application.

Admin.php:
===

    This is for keeping administration information.  This is in charge of keeping information about where in the grid you placed a table.  This is in charge of comments you make in the grid.  This is in charge of leads between tables.  All of Admin information is in the dbag database.  At the install phase of this application, the database names from the Application model must be copied into dbag.table_schema so the Admin will now know which databases it is allowed to present and work on with the user.  Originally I was going use a 'SHOW databases' command in the Application model to get the names but I found that Zend requires you to connect to a named database and there was no prescribed way to call something like $db->getDatabases();  I need to keep an eye out on a solution to this.


