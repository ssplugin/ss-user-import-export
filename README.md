# SS User Import Export plugin for Craft CMS 3.x

Import users and export users made fast and simple!
Craft User migration is vital part when site migration process. This plugin help new user import using csv and export user in the csv file. As well as keep users data in the CSV format.

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. In the Craft CMS Control Panel, go to Settings → Plugins Store and search for SS User Import Export:

OR

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require ssplugin/ss-user-import-export

3. Executing Migrations:

        php craft migrate/up --plugin=ss-user-import-export

4. In the Craft CMS Control Panel, go to Settings → Plugins and click the “Install” button for SS User Import Export:


## SS User Import Export features

- Easy to install and easy to use.
- No needed any extra configuration.
- Export only the data you need.
- User migration from one site to other.
- Export user fields data if user fields added.
- Export user as per your choice like only active users export.
- Field mapping is available while importing users.
- Choice to Send Activation Email for newly imported Active Users.

## Configuring SS User Import Export

- Craft Pro version is required because multiple users supported only in the pro version.

- After plugin installed go to the plugin settings. If plugin not installed then first install plugin from settings > plugins.

- Importing a CSV file format:
	- Firts row in the CSV file must be heading.
  <img src="http://datadazzle.com/ssplugin/csv-format.jpeg" alt="csv-example">

## Using SS User Import Export

**Export**

	- Export users using group wise or all the users in the CSV file.
	- You can able to enter file name and save exporting file as per your choice name.
	- Select status for example only active status users will export. If not selcet any one then all the users exported.

**Import**
	
	-First, To import multiple users, you need to upload a CSV file.. You can create the CSV file or you can export a CSV file from Export tabs then edit it. Also mapping Field as necessary:
	
	- The CSV file must include all of the columns in the following:
	- Field mapping:
	    - Username* : Username must be a unique and required field.
	    - Email*    : Email must be a unique and valid otherwise users will not imported.
	    - FirstName (Optional): Select value for first name otherwise empty added.
	    - LastName  (Optional): Select value for last name otherwise empty added.
	    - Group* : First you need to create a group after that add group handle value in the CSV file. if group value will empty or group handle value incorrect then Default User Group consider (Settings->Users->settings(Allow public registration?) ).
	    - Status (Optional)  : Default value is pending. Status value should be active, pending, suspended. 
	    - Password (Optional): This field value is a simple string not a hashed values. If password value will empty and user status active then activation mail will send to your email address where password set link available.

	-User fields:			  
	    Supported fieldType is 'Plain Text', 'Radio Buttons', 'Dropdown', 'Lightswitch', 'Email', 'URL' and 'Number'.

## SS User Import Export Roadmap

Three are two tabs avaliable in plugin settings.

**(1) Export:**

	You can able to select multiple groups and status.
	If the group is not selected then all the users exported and the same thing will happen in the Status. 

**(2) Import:** There are a few things need to understand.

	- Username and Email are unique and required.
	- Activation mail will only go if the proper email setting will be done( Settings < Email ).
	- You have to manualy create user group and then use group handle value in the CSV file.	
	- Pending status: User Status is a pending then admin need to active user account after that user will be able to login.
	                  But if password not import or empty while importing then admin need to Send Activation Mail manualy from CP.
	- Active status:  Activation mail has been sent to user email address where user can able to set password.
			  But if password imported while user importing then user can able to directly login a account.


Brought to you by [ssplugin](http://www.systemseeders.com/)
