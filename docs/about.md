## Generating acquisitions lists in Alma

The generation of new acquisitions lists in Alma is a task of the Alma FB Team. Requests should be sent their way.  
The acquisitions reports are generated in Alma Analytics. Location: `/Shared Folders/University of Leiden/Reports/Derk-Jan/`

## SFTP Server

The Alma acquisitions lists are placed, daily, on an SFTP server. This server is accessed by this application to import the text files and to transform them.  
Which SFTP server the files are placed on is determined in Alma. If you want the files to be placed on a different server, you must make the change in the `Analytics Objects List` menu in Alma: Got to the menu, find the acquisitions lists you want to change, edit it, and select the right SFTP service.

## Database structure

All the acquisitions from all the sources are imported, daily, into the `acquisitions` table.

Subject views are filters of this table.

### Changing imported columns

To change the data imported into the application first make the necessary changes to all the Alma Analytics export. Make sure they all export the same data.  

In `database\migrations` create a new migration to generate the required columns. Use `dropColumn` to delete columns no longer in use.  

`php artisan migrate:fresh` to execute the migrations. This command is valid both locally and on the server.

Reimport the data via the admin panel, or check the routes at `routes\web.php` for the individual steps.

Read the [official documentation](https://laravel.com/docs/8.x/migrations)

## Important Notes

All the exports from alma *must* have the same columns in the same order.
