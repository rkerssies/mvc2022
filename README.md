# mvc2022 
v2.1 (renewed @ 2023-03)


## Basic MVC-framework for educational purposes

This small framework contains many features that Laravel has as well.
Learing a framework can be overweling in the amount of folders, files and possibilities.
Junior developers  'can't see the forest for the trees'.
In this framework the processes and functuonality beneeth it all are les abstract and easier to track and learn. 

> NB: <b>clean-url's</b> and <b>domain-name</b> usage are required for this framework.

<b>Features in this MVC-framework:</b>
<ul>
	<li>Controller
		<ul>
			<li>Makes instance by typehinted classes in action-param's</li>
			<li>Named-param's in route-url passed from route to action-parma's</li>
			<li>views are custom called in controller-action by folder and filename</li>
		</ul>
	</li>
	<li>Models like Laravel
		<ul>
			<li>Models with fillables</li>
			<li>Models with defining custom tablename (other than modelname + s)</li>
			<li>Chainable methods to build database queries like Eloquent, like:</li>
            <ul>
                <li>all</li><li>find</li><li>andWhere</li><li>CRUD-Mmethods</li>
                <li>Min,Max,Sum,Avg-aggretates</li><li>raw</li>
            </ul>
			<li>Database connection for full MySqli params-bind, MySQLi basic usage or PDO</li>
            <li>Database connection-info stored in config.ini file</li>
		</ul>
	</li>
	<li>
		Views and Layouts
		<ul>
			<li>Views handle all var-types from controller-action </li>
			<li>Layout selection between multiple layouts with key in config.ini file</li>
			<li>Swithing between layouts is possible on a custom schedule</li>
			<li>Provide d with a responsive layout from <a href="http://www.initializr.com">Initializr</a></li>
		</ul>
	</li>
    <li>
		Some build-in feautures
		<ul>
			<li>Login-form</li>
			<li>Password renewal for logged-on users</li>
			<li>Password-forgot-form</li>
			<li>Administration example on the `fruit-table`</li>
            <ul>
                <li>Overview of all records, ordered (index)</li>
                <li>Adding a record (add) with input-validation</li>
                <li>Altering records (update)with input-validation</li>
                <li>Deleting a records (delete)</li>
            </ul>
			<li>Deliverd with a basic gallery</li>
		</ul>
	</li>
	<li>Security
		<ul>
			<li>Routes protected on submit-method (get, post, put, patch ad/or delete)</li>
			<li>Routes protected with URL-whitelist</li>
			<li>Middleware protection on routes (optional for eq: authentication and authorization)</li>
			<li>Classes, Traits and Inheritance namespaced by an autoloader on the app-folder</li>
			<li>Unqiue encryption depending on: App-key</li>
			<li>Salt ecryption and decryption</li>
			<li>Config in INI-file and accessable via a definition</li>
			<li>Session fingerprint-protected with IP-check</li>
			<li>Forms for adding and updating protected with csrf-token</li>
			<li>Form-data validation FormRequestson on pipe-line seperated strings multiple checks, like:</li>
                <ul>
                    <li>required, nullable, between, same, string, min, max, reg_ex, alphanum, and more.</li>
                </ul>
			<li>Single-point of entry by index-file in public-folder</li>
			<li>Public content in public-folder, eq: css, js and images</li>
		</ul>
	</li>
	<li>Helper-functions
		<ul>
			<li>Request-dataobject and Response-dataobject</li>
			<li>Dump 'n Die (dd)</li>
			<li>Url, back and redirect</li>
			<li>Set var in session and retrieving it</li>
		</ul>
	</li>
	<li>Middleware
		<ul>
			<li>Middleware called on routes (optional calling multiple classes)</li>
			<li>Structural calling Middleware before initiaating controllers</li>
			<li>Structural calling Middleware after controller-action finished</li>
		</ul>
	</li>
	<li>Services
		<ul>
			<li>Calling Services before views (eq: nav, meta-tags for seo, css-linktag and js-tags)</li>
		</ul>
	</li>
    <li>Smtp-mailer
		<ul>
			<li>Sending smtp-email with a view-template</li>
            <li>WEB-server configuration in ini-file</li>
            <li>Email-preview on screen-dump on flag in ini-file</li>
		</ul>
	</li>
    <li>Artibuild
		<ul>
			<li>An (basic) Artisan-alike terminal-command to create all kinds of MVC2022 controllers, models and views </li>
		</ul>
	</li>
</ul>
<br>
<br>
<img title="example homepage" alt="example homepage" height="150px" src="ReadMe/images/01 home.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/02 find by id.png">
<img title="example gallery" alt="example gallery" height="150px" src="ReadMe/images/03 gallery.png">
<img title="example beheer" alt="example beheer" height="150px" src="ReadMe/images/04 administration.png">
<img title="example login" alt="example login" height="150px" src="ReadMe/images/05 app-hamburgermenu.png">
<img title="example app-login" alt="example app-login" height="150px" src="ReadMe/images/06 app-login.png">
<img title="example app-login" alt="example app-login" height="150px" src="ReadMe/images/07 email dump-example.png">
