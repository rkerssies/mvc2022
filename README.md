# mvc2022 
v2.1 (renewed @ 2023-03)


## Basic MVC-framework for educational purposes

This small framework contains many features that Laravel has as well.
Learing a framework can be overweling in the amount of folders, files and possibilities.
Junior developers  'can't see the forest for the trees'.
In this framework the processes and functuonality beneath it all are less abstract and easier to track and learn. 

> Need help to set up your own project websote?<br> Read the helppage: [Guid for installation- and configuration](ReadMe/install_config.md)


### Features in this MVC-framework:

* Controller
  * Makes instance by typehinted classes in action-param's
  * Named-param's in route-url passed from route to action-parma's
  * Views are custom called in controller-action by folder and filename

* Models like Laravel
  * Models with fillables
  * Models with defining custom tablename (other than modelname + s)
    * Chainable methods to build database queries like Eloquent, like:
      * all
      * find
      * andWhere
      * CRUD-Mmethods
      * Min,Max,Sum,Avg-aggretates
      * raw
   * Database connection for full MySqli params-bind, MySQLi basic usage or PDO
   * Database connection-info stored in config.ini file
	

* Views and Layouts
  * Views handle all var-types from controller-action
  * Layout selection between multiple layouts with key in config.ini file
  * Build in responsive layout by [Guid for installation- and configuration](ReadMe/install_config.md) [Initializr](http://www.initializr.com) HTML5 with Bootsrap
  * Swithing between layouts is possible on a custom schedule</li>

* Some build-in feautures;
  * Login-form
  * Password renewal for logged-on users
  * Password-forgot-form
  * Administration example on the `fruit-table`
    * Overview of all records, ordered (index)
    * Adding a record (add) with input-validation
    * Altering records (update)with input-validation
    * Deleting a records (delete)
  * Deliverd with a basic gallery


* Security
  * Routes protected on submit-method (get, post, put, patch ad/or delete)
  * Routes protected with URL-whitelist 
    * Middleware protection on routes (optional for eq: authentication and authorization)
    * Classes, Traits and Inheritance namespaced by an autoloader on the app-folder
    * Unqiue encryption depending on: App-key
    * Salt ecryption and decryption
    * Config in INI-file and accessable via a definition
    * Session fingerprint-protected with IP-check
    * Forms for adding and updating protected with csrf-token
    * Form-data validation FormRequestson on pipe-line seperated strings multiple checks, like:
      *  required, nullable, between, same, string, min, max, reg_ex, alphanum, and more.
   * Single-point of entry by index-file in public-folder
   * Public content in public-folder, eq: css, js and images

  * Helper-functions
    * Request-dataobject and Response-dataobject
    * Dump 'n Die (dd)
    * Url, back and redirect
    * Set var in session and retrieving it

* Middleware
  * Middleware called on routes (optional calling multiple classes)
  * Structural calling Middleware before initiaating controllers
  * Structural calling Middleware after controller-action finished

* Services
  * Calling Services before views (eq: nav, meta-tags for seo, css-linktag and js-tags)
         
  * Smtp-mailer
       * Sending smtp-email with a view-template
       * WEB-server configuration in ini-file
       * Email-preview on screen-dump on flag in ini-file

  * Artibuild
      * An (basic) Artisan-alike terminal-command to create all kinds of MVC2022 controllers, models and views </li>
<br>
<br>
<img title="example homepage" alt="example homepage" height="150px" src="ReadMe/images/01 home.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/02 find by id.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/03 gallery.png">
<img title="example beheer" alt="example beheer" height="150px" src="ReadMe/images/04 administration.png">
<img title="example login" alt="example login" height="150px" src="ReadMe/images/05 app-hamburgermenu.png">
<img title="example app-login" alt="example app-login" height="150px" src="ReadMe/images/06 app-login.png">
<img title="example app-login" alt="example app-login" height="150px" src="ReadMe/images/07 email dump-example.png">
