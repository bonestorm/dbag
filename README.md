DBag
===

**author: David Jensen**

**version: 1.whatever (ultra Alpha build)**

prereqs:
===

You need MySQL, Apache, PHP all installed.
You also need Zend Framework 1.10.  Put (or link) the base Zend directory at &lt;INSTALL_DIRECTORY&gt;/vendor so &lt;INSTALL_DIRECTORY&gt;/vendor/Zend/library is where the Zend library is. NOTE: There's got to be a better way to do this.

to install:
===

1. You need to put MySQL login information in "&lt;INSTALL_DIRECTORY&gt;/config/app_login.ini" and "&lt;INSTALL_DIRECTORY&gt;/config/admin_login.ini".  app_login.ini is for the database installation you want to administer and admin_login.ini is for the database installation that keeps the application information (database 'dbag').  This can be the same database installation.

2. Run "&lt;INSTALL_DIRECTORY&gt;/sql/system_tables.sql" into MySQL.  This creates the application database 'dbag' and fills it with tables it needs to store application data.

6203. Run "php &lt;INSTALL_DIRECTORY&gt;/library/ZFExt/Model/import.php" to copy the database names that the application will use from the database install (RDBMS) listed in app_login.ini to the admin database.  You may have a mysqli.sock problem, there's a script in &lt;INSTALL_DIRECTORY&gt;/util/ to help you make a symbolic link if that's a problem.  You could also just add "table_shema" entries to the "dbag" database using phpMyAdmin if you only want to use a few select databases.

3. Make a virtual host for Apache with document root at "&lt;INSTALL_DIRECTORY&gt;/public/".  Many tutorials are available on the net for doing this but the basic steps:

- add this to you httpd.conf:

        Listen 80
        NameVirtualHost *:80
        <VirtualHost *:80>
            DocumentRoot <INSTALL_DIRECTORY>/public
            ServerName dbag
        </VirtualHost>

    The basic idea is that the server must be listening on port 80 (unless you want to add the port you're using like "dbag:8008"), and add NameVirtualHost to tell Apache to use virtual hosts, and add your virtual host 'dbag'.  Note: this will disable your existing host so should add it again as another virtual host in httpd.conf like:

        <VirtualHost *:80>
            ServerName <EXISTING_HOST>
            DocumentRoot <OLD_DOCUMENT_ROOT>
        </VirtualHost>

    See [Apache Virtual Hosts](http://httpd.apache.org/docs/2.2/vhosts/name-based.html#using), at "Main host goes away" section.

- add a new line to /etc/hosts file "127.0.0.1   dbag"
- restart Apache
- go to "http://dbag" in your browser

    There are other (better) ways of doing this but this is a start.

to learn application operation:
==

1. Figure it out?  I don't have documentation written yet.


directory structure:
==

('./' is used as &lt;INSTALL_DIRECTORY&gt;)

* ./public - document root, public html directory, exposes this application through Apache, contains all html/css/image/javascript assets, !!extra static content should be placed here!!
* ./javascript - link to ./public/js, javascript files, this is where the majority of the application resides, client javascript using HTML5 Canvas and jQuery
* ./application - application specific code, Views/Controllers, bootstrap code
* ./library - database access, application logic.  this is the 'M' in MVC (Model)
* ./application/views - just front controller's index action since this is a SPA (Single Page Application) and RIA (Rich Internet Application). this is the 'V' in MVC (View)
* ./application/controllers - routing code, pulls it all together. this is the 'C' in MVC (Controller)
* ./config - configuration.ini use by Zend/Application for application settings
* ./vendor - libraries, Zend Framework is installed here
* ./docs - formal documentation, like this README
* ./sql - sql for MySQL, system_tables.sql is SQL that should be run in to MySQL for keeping application data
* ./util - keeps various utility scripts, don't mistake this directory for an important directory
