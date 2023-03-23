# mvc2022
v2.1 (renewed @ 2023-03)


## Installation- & configuration-guide

To get mvc20022 on your own domain, take the following steps:

1. Download the complete project from git-hub to place it in your desired location.
    <a href="https://github.com/InCubics/mvc2022">mvc2022</a>
2. For a local installation (not online);
    * vhosts with custom (local) domainname
    * configure the webserver with the (loacal) domainname and projectfolder  
3. For an online installation
   * Make sure that the app-folder has 755 rights. 
    In this way, your config-file is not accessable for visitors. 
   * Make sure the domain points to <your_projectname>/public instead of /public.<br>
     (it is possible that this requires an extra .htaccess-file with some settings)
4. Make a database with the desired database-name. 
   Make sure your remember the database-location, account, password and database-name.
    Open the in the projectfolder ./app/config/config.ini and change the database-settings
5. Change te following settings for a basic project setup;
    * domain
    * app_key (this for eq. hashing passwords)
   Checkout the other options for more customisations. 
6. Open ./app/config/.private.key and change the string in an random string op 34 characters. 
    This for encrypting and decrypting your sessions. 
7. Remove the folder: ReadMe and the README.md file.
8. Remove the fruit_db.sql file
9 Start the webservies and database-service (if not already done). 


<img src="ReadMe/images/01home.png" height="150px">

* The datatabel `users` has two default users: admin@app.com and user@app.com with the password: password


### Some guidlines for your own developement
* Modifications of url-paths and the controller-actions they call, can be changed in:  
    ./app/routs/web.php
* Controllers are stored in ./app/Http/Controllers.
* In a controller-method oyther classes ar instatiable with namespandes, for example Models and Lib-classes
* Controllers-methods call a specific view, from a subfolder in with the controllername 
 in the folder .app/views/. Views that injected in a layout.
* With a Model all kinds of data-queries can be made with chainable methods on that model-object.
* Validation on submitted data is possible with "request", that is configurable in ./app/Http/Validation

Optional:
*Change the layoput schedule in ./app/config/layoutSchedule.php, 
    Make sure that key 'ScheduledLayout' in  ./app/config/config.ini is set on: true.
    The layoutnames in layoutSchedule.php must match the foldernames (and there design) in ./app/layouts.
