DBag README file

===
author: David Jensen
version: 1.whatever (ultra Alpha build)

===
prereqs:

You need MySQL, Apache, PHP all installed

===
to install:

1. You need to put MySQL login information in "<INSTALL_DIRECTORY>/modules/login.php.

2. Run "<INSTALL_DIRECTORY>/sql/system_tables.sql" into MySQL.  This creates the application database 'dbag' and fills it with tables it needs to store application data.

3. Make a virtual host with document root at "<INSTALL_DIRECTORY>/public/"

4. Go to your virtual domain in a browser.  The application should be manifest.

==
to learn application operation:

1. Figure it out?  I don't have documentation written yet.


==
directory structure:

'./' is used as <INSTALL_DIRECTORY>

./public - document root, public html directory, exposes this application through Apache, contains all html/css/image/javascript assets, !!extra static content should be placed here!!

./javascript - link to ./public/js, javascript files, this is where the majority of the application resides, client javascript using HTML5 Canvas and jQuery

./application - application specific code, Views/Controllers, bootstrap code
./library - database access, application logic.  this is the 'M' in MVC (Model)
./application/views - just front controller's index action since this is a SPA (Single Page Application) and RIA (Rich Internet Application). this is the 'V' in MVC (View)
./application/controllers - routing code, pulls it all together. this is the 'C' in MVC (Controller)
./application/modules - helpers, empty for now
./config - configuration.ini use by Zend/Application for application settings


./vendor - libraries, Zend Framework is installed here
./docs - formal documentation, like this README

./sql - sql for MySQL, system_tables.sql is SQL that should be run in to MySQL for keeping application data
./util - keeps various utility scripts, don't mistake this directory for an important directory
