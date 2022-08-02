## Local installation

Assuming you have [Composer](https://getcomposer.org/) and PHP installed locally on your machine:

1. Clone the repository from GitHub.  
2. `composer install` to install the required dependencies.  
3. `cp .env.example .env` to generate a .env file.  
4. `php artisan key:generate` to generate an encryption key.  

### Local Database

!!! Note
    This application is based on Laravel. Laravel can use various database connections: if you would like something else than MySQL or SQLite, please refer to the official [Laravel documentation](https://laravel.com/docs/8.x/database) for more information.

#### Local SQLite

If you are only testing the application, we advise using SQLite:

1. In your .env file replace `DB_CONNECTION=mysql` with `DB_CONNECTION=sqlite`  
2. In the `/database` folder, create a text file called `database.sqlite`  
3. `php artisan migrate:fresh --seed` to populate the database, and create dummy data and an admin user.  
4. `php artisan serve` to lunch your application.  

#### Local MySQL

For production environments, or for extended development, we recommend using MySQL as your database.

1. In your .env file, make sure `DB_CONNECTION=mysql` is set.  
2. Generate a MySQL database, and make sure that the .env settings are properly set and that you can make a connection.  
3. `php artisan migrate:fresh` to populate the database, and create dummy data and an admin user.  
4. `php artisan serve` to lunch your application.  

## Server installation

You can install this application on your server in multiple ways. While we strongly recommend an SSH connection to your server, you can also install this application using FTP only.

!!! Warning
    While not obligatory, a SSH connection to the server will make your life much easier as you will be able to execute Artisan commands directly on the server, rather than having to upload files manually via FTP with every update.

To work only via FTP, install the application locally on your machine:

1. Clone the repository from GitHub.  
2. `composer install` to install the required dependencies.  
3. `cp .env.example .env` to generate a .env file.  
4. `php artisan key:generate` to generate an encryption key.
5. Work on you application.
6. Upload all the files to your server.

If, instead you have SSH connection, there are multiple ways to upload your app (via, GIT, GIT FTP, SFTP, etc.)  

Once your files are on the server, you can execute the commands listed above. If you do not have Composer installed on your server, upload the `composer.phar` file - available on the Composer website - to your application folder and use `php composer.phar install` to install dependencies.

!!! Note
    The main folder in Laravel is named `public`, some servers use `public_html` instead. Make sure your DNS points to the "public" folder.
    You can also decide to use the `public_html` folder instead, but you will have to make some changes to your configuration. How to do this is out of scope of this documentation.

!!! Note
    You can further optimize the application using more complex Laravel and Composer commands. See the [Laravel documentation on deployment](https://laravel.com/docs/8.x/deployment).

### Server Database

#### Server SQLite

If your server has no MySQL, you can use SQLite. The following commands can be run both locally or via SSH.

1. In your .env file replace `DB_CONNECTION=mysql` with `DB_CONNECTION=sqlite`  
2. In the `/database` folder, create a text file called `database.sqlite`  
3. `php artisan migrate:fresh --seed` to generate generate tables and populate the database, and to create dummy data and an admin user.  
4. Upload the generated SQLite file to your server, in the `/database` folder.  

#### Server MySQL

The following commands can be run both locally or via SSH.

1. In your .env file, make sure `DB_CONNECTION=mysql` is set.
2. Generate a MySQL database, and make sure that the .env settings are properly set and that you can make a connection.
3. `php artisan migrate:fresh` to populate the database. Unlike with the local installation, we are not using `--seed` in this case.

!!! Warning
    Do not use `php artisan migrate:fresh` on a public/production server, unless you are starting out: **you will overwrite all the data, and you will create the most easy to guess username and password ever!**

## Creating an admin account

To generate a admin user, or creating multiple admin users, we use Tinker. Tinker is included in Laravel.  
These commands can be used both locally or on the server for both SQLite and MySQL.

`php artisan tinker`  
`DB::table('users')->insert(['name'=>'YourUsername','email'=>'thisis@youremail.com','password'=>Hash::make('TypeYourPasswordHere')])`
