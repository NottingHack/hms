# Setting Up The HMS Development Environment

## Requirements

Before you get to any HMS specific setup, you will need the following running on your machine:

* Apache2 (Or other web server)
* MySQL 4 or above
* PHP 5.3 or above

PHP will require at least these extensions:

* curl
* dom
* hash
* json
* krb5
* libxml
* mbstring
* mhash
* MySQLi
* PDO
* pdo_mysql

## Setting up HMS

### Obtain the Code

Clone the repository wherever you like, and create a link to webroot.

**Linux**

Navigate to the webroot (often `/var/www`) and run  
`ln -s /path/to/hms hms`

You may need to `sudo` this command

**Mac**

**Windows**

### Set the Directories Up

Make sure these directories are writable by the webserver:

* app/tmp
* app/Config (during this setup only, change back to read only when you have setup HMS)

### Set Up the Database

Using your favourite method (command line, PHPMyAdmin, etc) set up two new databases and a user for each database that has full access.

### Set Up the Config and Populate the Database

You will need the `hms.settings` file.  This is not included in the repository, as it contain sensitive information.  You should have received this when you joined the HMS group.  If you are forking this for another Hackspace, see the `dev/setup.php` file for what settings are required and alter as required.

Open the hms.settings file and change the database settings to your newly created databases and users

In your browser, visit  
`http://localhost/hms/dev/setup.html`

Fill in your details and click **Go**.  This will create the config files for you and populate the database, including an admin member with the details you have provided.

**Note:** This form does zero error checking, so make sure you have entered your details correctly and **NEVER** use this script on a production system.