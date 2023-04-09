# mvc2022 
v2.2 (renewed and stable @ 2023-04-10)  <img title="new release" alt="new version relase" height="25px" src="ReadMe/images/new.png">


## Basic MVC-framework for educational purposes

This small framework contains many features that Laravel has as well.
Learing a framework can be overwhelming in the amount of folders, files and possibilities.
Junior developers  'can't see the forest for the trees'.
In this framework the processes and functionality beneath it all are less abstract and easier to track and learn. 

> Need help to set up your own project websote?<br> Read the helppage: [Guid for installation- and configuration](ReadMe/install_config.md)


### Features in this MVC-framework:

* Controller
  * Makes instance by typehinted classes in action-param's
  * Named-param's in route-url passed from route to action-parma's
  * Views are custom called in controller-action by folder and filename

* Models like Laravel
  * Models that receives just the fillable fieldnames
  * Optional hidden fields that ar taken out from the response
  * Models with defining custom tablename (other than modelname + s)
  * Chainable methods to build database queries Eloquent-alike, eq:
    * all
    * find
    * andWhere
    * CRUD-methods
    * min,max,sum,avg-aggregates
    * raw
    * oneOnMany, manyOnMany
    * optional get all realted data within a nested-key 
    * optional change output to json-format
  * Database connection for full MySqli params-bind, MySQLi basic usage or PDO
  * Database connection-info stored in config.ini file
  * Example-page with Controller that uses Mysqli CRUD-queries and
   <br> a page with CRUD-queries that uses MySqli Bind-Params

* Views and Layouts
  * Views handle all var-types from controller-action
  * Layout selection between multiple layouts with key in config.ini file
  * Build in responsive layout by [Initializr](http://www.initializr.com) HTML5 with Bootsrap
  * Swithing between layouts is possible on a custom schedule
  * External CSS and JS scripts via CDN are configurable in config-files
  * Showing a message in a show-hide animated bar at the top of the screen
  * Pagination with chained query on Models and buttons by paginatorService 

* Some build-in feautures;
  * Login-form
  * Password renewal for logged-on users
  * Password-forgot-form
  * Usage of extra globals PUT, PATCH and DELETE
  * Administration example on the 'fruit-table'
    * Overview of all records, ordered (index)
    * Adding a record (add) with input-validation
    * Altering records (update)with input-validation
    * Deleting a records (delete)
  * Deliverd with a basic gallery; [W3 slideshow](https://www.w3schools.com/howto/howto_js_slideshow_gallery.asp)


* Security
  * Routes protected on submit-method (get, post, put, patch ad/or delete)
  * Routes protected with URL-whitelist 
    * Middleware protection on routes (optional for eq: authentication and authorization)
    * Classes, Traits and Inheritance namespaced by an autoloader on the app-folder
    * Unique encryption depending on: App-key
    * Salt encryption and decryption
    * Config in INI-file and accessible via a definition
    * Session fingerprint-protected with IP-check
    * Forms for adding and updating protected with csrf-token
    * Form-data validation FormRequests on pipe-line seperated strings multiple checks, like:
      *  eq: required, nullable, between, same, string, min, max, reg_ex, alphanum, and more.
   * Single-point of entry by index-file in public-folder
   * Public content in public-folder, eq: css, js and images

  * Helper-functions
    * Request-dataobject and Response-dataobject
    * Dump 'n Die (dd)
    * Url, back and redirect
    * Set var in session and retrieving it

* Middleware
  * Middleware called on routes (calling multiple classes with optional params)
  * Structural calling Middleware before initiating controllers
  * Structural calling Middleware after controller-action finished

* Services
  * Calling Services before views (eq: nav, meta-tags for seo, css link-tag and js-tags)
  * Appending meta-tags in head-tag for keyword and description into the layout from the controller-action
  * Smtp-mailer
       * Sending smtp-email with a view-template
       * WEB-server configuration in ini-file
       * Email-preview on screen-dump on flag in ini-file
* API
  * Model-name in url triggers controller-method
  * Action: all, find by id, insert, update and delting records
  * Optional: pagination
  * Validation on submitted-data by fillable keys in Models 
  * oAuth width JWT-token, check token by middleware in route
  * API-response has one structure with request-data, response-date and meta-data 

* Artibuild
  * An (basic) Artisan-alike terminal-command to create all kinds of MVC2022 controllers, models and views
<br>
<br>
<div style="display:inline-block; margin: 3px;">
<img title="example homepage" alt="example homepage" height="150px" src="ReadMe/images/01 home.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/02 find by id.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/03 gallery.png">
<img title="example beheer" alt="example beheer" height="150px" src="ReadMe/images/04 administration.png">
<img title="example login" alt="hamburgermenu" height="150px" src="ReadMe/images/05 app-hamburgermenu.png">
<img title="example app-login" alt="example app-login" height="150px" src="ReadMe/images/06 app-login.png">
<img title="example app-login" alt="example email forgotten" height="150px" src="ReadMe/images/07 email dump-example.png">
<img title="example app-message-bar" alt="example messagebar pagination" height="150px" src="ReadMe/images/08 messagebar - pagination.png">
<img title="example api JWT-token" alt="example token jwt" height="150px" src="ReadMe/images/10 api - get token.png">
<img title="example api paginated results" alt="example paginating" height="150px" src="ReadMe/images/11 api - get paginated.png">
<img title="example api find by id" alt="example api find" height="150px" src="ReadMe/images/12 api - get by id.png">
<img title="example api inserting" alt="example api inserting" height="150px" src="ReadMe/images/13 app - inserting record.png">
<img title="example api meta-structure" alt="example api meta" height="150px" src="ReadMe/images/14 api - meta-data.png">
<img title="example api validation on insert-update" alt="example api validation" height="150px" src="ReadMe/images/15 api- validation.png">
</div>
