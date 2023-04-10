# mvc2022
v2.1 (renewed @ 2023-03)


## MVC-method
There are many ways of developing an MVC-framework. MVC2022 is build with processes 
that are familiair in the PHP Laravel-framework. To have some insight the following process-schema (not UML, sorry) 
might help to understand the workings of this framework, and some others;<br>
<img title="mvc-process scheme" alt="mvc-process scheme" src="./images/schema mvc-process.png">


## Installation- & configuration-guide

To get <b>MVC20022</b> on your own domain, take the following steps:

1. Download the complete project from git-hub to place it in your desired location.<br>
    The project Github-link: <a href="https://github.com/InCubics/mvc2022">mvc2022</a>


2. For a local installation (not online);
    * Add an extra line in the vhosts / hosts-file with custom (local) domain-name.
    With the console for Mac : sudo nano /etc/hosts
    With the terminal for Windows (Admin-mode): C:\Windows\System32\drivers\etc\hosts 
   

```bash
    ##
    # Host Database
    #
    # localhost is used to configure the loopback interface
    # when the system is booting.  Do not change this entry.
    ##
           ::1  {{mvc2022.rk}} 
```           

* Configure the webserver with the (loacal) domain-name and pointer to your projectfolder
    For Mac: /Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf
    For Windows: C:\XAMPP\apache\conf\extra\httpd-vhosts.conf

```bash
    <VirtualHost *:80>
    ServerName {{mvc2022.rk}}
    DocumentRoot "/Users/{username>}/{projects}/{mvc2022}/public"
    <Directory "/Users/{username>}/{projects}/{mvc2022}/public" >
        Options Indexes FollowSymLinks Includes execCGI
         AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
NB: enter your domain-name within the dubble curly braces ( {{ }} ). This must be matching with the domain in de host-file and the domain in the config-file.
NB: uri-parts with curly braces ( {} ) need to be altered with actual path.
* REBOOT your webserver

3. For an online installation
   * Make sure that the app-folder has 755 rights. 
    In this way, your config-file is not accessable for visitors. 
   * Make sure the domain points to {your_projectname}/public instead of /public.<br>
     (it is possible that this requires an extra .htaccess-file with some settings)


4. Make a database with the desired database-name. 
   Make sure your remember the database-location, account, password and database-name.
    Open the in the projectfolder ./app/config/config.ini and change the database-settings


5. Change the value for the following keys in ./app/config/config.ini for a basic project-setup;
    * domain (our domain-name with http or https as value in this key)
    * app_key (a very long string of characters is required. This for eq. creating a private-key to secure sessions)
    * (optional) Checkout and change other key-values for more customisations. 
    * chanhe the rights on app/.PrivateKey and app/config/config.ini to: 640<br> 
      (for the administrator the file is still readable and editable)   


6. Open the terminal and run:
```bash
   php artibuild appkey:generate
```
You have now a unique 40 character string as a private-key, stored in ./app/config/.private.key


7. Remove the folder: ReadMe and the README.md file.


8. Remove the fruit_db.sql file


9. Start the webservies and database-service (if not already done). 


10. Start your browser and use the url of your projects, eq: `http://mvc2022.rk`<br>
Your site is running and will look like the picture below:
<img src="./images/01 home.png" height="250px">


### Some guidlines for your own developement
* Modifications of url-paths and the controller-actions they call.This can be changed in:  
    ./app/routs/web.php
* Controllers are stored in ./app/Http/Controllers.
* In a controller-method other classes an object can be made with namespaces and the class-name, for example Models and Lib-classes
* Controllers-methods call a specific view, from a subfolder in with the controllername 
 in the folder .app/views/. Views that injected in a layout.
* With a Model all kinds of data-queries can be made with chainable methods on that model-object.
* Validation on submitted data is possible with "request", that is configurable in ./app/Http/Validation
* Creating Controllers, Models, Requests, Services and Middleware with 'artibuild'
```bash
    php artibuild --help
```

* Optional:<br>
    * Change the layoput schedule in ./app/config/layoutSchedule.php. <br> 
        Make sure that key 'ScheduledLayout' in  ./app/config/config.ini is set on: true.
        The layout-names in layoutSchedule.php must match the folder-names (and there design) in ./app/layouts.
    * Configure your CDN-libraries within app/configure/css_cdn_resources.php or app/configure/js_cdn_resources.php  
