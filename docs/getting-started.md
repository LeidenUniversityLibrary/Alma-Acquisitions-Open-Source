
1. Download the [latest package](https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/packages) available from GitHub.
2. Unzip the package in a directory of your choosing.
3. Navigate to the directory.
4. `composer install` to install the required dependencies.  
5. `cp .env.example .env` to generate a .env file.  
6. `php artisan key:generate` to generate an encryption key.
7. Create a database following the instructions on this page.
8. Create an admin account following the instructions on this page.
9. `php artisan serve` to locally experiment with Alma Acquisitions - Open Source (AAOS.)

!!! Tip
    If you do not have Composer installed on your server, upload the `composer.phar` file - available on the [Composer website](https://getcomposer.org/download/) - to your application folder and use `php composer.phar install` with a non-root account to install dependencies.

## Database

!!! Note
    This application is based on Laravel. Laravel can use various database connections: if you would like something else than MySQL or SQLite, please refer to the official [Laravel documentation](https://laravel.com/docs/8.x/database) for more information.

### MySQL

For production environments, or for extended development, we recommend using MySQL as your database.

1. In your .env file, make sure `DB_CONNECTION=mysql` is set.  
2. Generate a MySQL database on the server, and make sure that the `.env` settings are properly set and that the application can make a connection.  
3. `php artisan migrate:fresh` to populate the database, and create dummy data and an admin user.  
4. `php artisan serve` to lunch your application.

!!! Danger
    Do not use `php artisan migrate:fresh` on a public/production server, unless you are starting out: **you will overwrite all the data!**

### SQLite

If you are only testing the application, we advise using SQLite:

1. In your .env file replace `DB_CONNECTION=mysql` with `DB_CONNECTION=sqlite`  
2. In the `/database` folder, create a text file called `database.sqlite`  
3. `php artisan migrate:fresh` to populate the database, and create dummy data and an admin user.  
4. `php artisan serve` to lunch your application.

## Creating an admin account

Once you have created and connected to a database, you must generate an admin account. To generate a admin user, or creating multiple admin users, we use Tinker. Tinker is included in Laravel.  
These commands can be used both locally or on the server for both SQLite and MySQL.

1. `php artisan tinker`  
2. `DB::table('users')->insert(['name'=>'YourUsername','email'=>'thisis@youremail.com','password'=>Hash::make('TypeYourPasswordHere')])`

This will return `true`, meaning that and account has been added to the database.

## Start using your application

On your local machine, you can use `php artisan serve` to launch a local version of your application. You will be able to login at <http://127.0.0.1:8000/login>

If you have installed the application on your server, and used Tinker to create an admin account, proceed directly to `yourURL.com/login`.

## Additional notes

!!! Note
    The main folder in Laravel is named `public`, some servers use `public_html` instead. Make sure your DNS points to the "public" folder.
    You can also decide to use the `public_html` folder instead, but you will have to make some changes to your configuration. How to do this is out of scope of this documentation.

!!! Note
    You can further optimize the application using more complex Laravel and Composer commands. See the [Laravel documentation on deployment](https://laravel.com/docs/8.x/deployment).
