DBag
===

**author: David Jensen**

**version: 1.whatever (ultra Alpha build)**

prereqs:
===

You need MySQL, Apache, PHP all installed

to install:
===

1. You need to put MySQL login information in "&lt;INSTALL_DIRECTORY&gt;/modules/login.php.

2. Run "&lt;INSTALL_DIRECTORY&gt;/sql/system_tables.sql" into MySQL.  This creates the application database 'dbag' and fills it with tables it needs to store application data.

 Make a virtual host for Apache with document root at "&lt;INSTALL_DIRECTORY&gt;/public/".  Many tutorials are available on the net for doing this but the basic steps:

- add this to you httpd.conf:

	Listen 80
	NameVirtualHost *:80
	<VirtualHost *:80>
		DocumentRoot &lt;INSTALL_DIRECTORY&gt;/public
		ServerName dbag
	</VirtualHost>

The basic idea is that the server must be listening on port 80 (unless you want to add the port you're using like "dbag:8008"), and add NameVirtualHost to tell Apache to use virtual hosts, and add your virtual host 'dbag'.  Note: this will disable your existing host so should add it again as another virtual host in httpd.conf like:

	<VirtualHost *80>
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
* ./application/modules - helpers, empty for now
* ./config - configuration.ini use by Zend/Application for application settings
* ./vendor - libraries, Zend Framework is installed here
* ./docs - formal documentation, like this README
* ./sql - sql for MySQL, system_tables.sql is SQL that should be run in to MySQL for keeping application data
* ./util - keeps various utility scripts, don't mistake this directory for an important directory
