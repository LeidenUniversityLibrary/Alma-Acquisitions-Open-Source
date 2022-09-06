## Configuration file

You can change the configuration of the Alma Acquisitions (Open Source) app in the configuration file:

```config/acquisitions.php```

You can set the values of each option in your `.env` file.

### General

* `institution_name => env('INST_NAME', NULL),`  
Your institution's name. Defaults to `My Institution`.

* `homepage_acquisitions_list => env('HOMEPAGE_ACQUISITIONS_LIST', NULL),`  
The name of the acquisitions list that will be displayed on the homepage off the app. Defaults to `NULL`.

### Alma Settings

* `alma_api_key => env('ALMA_API_KEY', NULL),`  
Your Alma API key. Defaults to `NULL` if not set.

* `shared_reports_path => env('SHARED_REPORTS_PATH', NULL),`  
The `path` that leads to your acquisitions lists generated in Alma Analytics. See the [official Alma API documentation](https://developers.exlibrisgroup.com/alma/apis/docs/analytics/R0VUIC9hbG1hd3MvdjEvYW5hbHl0aWNzL3BhdGhzL3twYXRofQ==/) to find the path that applies to your institution. Defaults to `NULL`.  

* `admin_shared_reports_path => env('ADMIN_SHARED_REPORTS_PATH', NULL),`  
This path is slightly different from the `shared_reports_path`: if the path is encoded, the API call will not work, returning a `Error 400` by the API.

### Primo Settings

These settings are used to build the links from this application to our Primo installation.
Here is an example of the URL generated at Leiden University:
`https://catalogue.leidenuniv.nl/primo-explore/search?query=any,exact,9939992010202711&tab=leiden&search_scope=Local&vid=UBL_V1&lang=en_US&offset=0`

* `'primo_URL' => env('PRIMO_URL', NULL),`
The base URL of your primo installation. For example: <https://catalogue.mylibrary.com>

* `'primo_tab' => env('PRIMO_TAB', NULL),`
The [Primo Tab](https://knowledge.exlibrisgroup.com/Primo/Product_Documentation/Primo/Back_Office_Guide/060Configuring_Primo%E2%80%99s_Front_End/020Views_Wizard#ww1329562) where your acquired items appear.

* `'primo_scope' => env('PRIMO_SCOPE', NULL),`
The Primo Search Scope - Also known as [Search Profile](https://knowledge.exlibrisgroup.com/Primo/Product_Documentation/020Primo_VE/Primo_VE_(English)/040Search_Configurations/010Configuring_Search_Profiles_for_Primo_VE) in Primo VE - where your new acquisitions appear.

* `'primo_vid' => env('PRIMO_VID', NULL),`
The [Primo View Code](https://knowledge.exlibrisgroup.com/Primo/Product_Documentation/Primo/Back_Office_Guide/060Configuring_Primo%E2%80%99s_Front_End/020Views_Wizard) used at your institution.

!!! NOTE
      Alma Acquisitions (Open Source) searches for an item's MMS ID, which is a unique identifier; this is why in the URL construction we chose for `any,exact,MMS_ID`. If your institution prefers or requires another URL construction, you can edit this part to fit your needs by looking at the [Performing Advanced Searches](https://knowledge.exlibrisgroup.com/Primo/Product_Documentation/Primo/End_User_Help_-_New_UI/020Performing_Advanced_Searches) page in the documentation.

### Google Analytics

This application can use Google Analytics to track usage if needed. You can configure your GA tracking ID in the `.env` file.

Note that the Google Analytics Tracking ID is called from the services file in `config/services.php`, like so:

* `google_analytics_id => env('GOOGLE_ANALYTICS_TRACKING_ID', NULL)`  
Defaults to `NULL`.

## Changing imported columns

You can change the data you import from Alma Analytics into AAOS. For example, you might not want to import the LC Classification, or would like to display different data than what Leiden University Libraries has chosen to display.

First you will have to edit the analyses in Alma Analytics.

!!! IMPORTANT
    All your reports in Alma Analytics must use the same columns: AAOS expects all the acquisitions lists to use the same columns in all the Alma Analytics reports. You cannot have an acquisition list that displays the authors, and another acquisitions list that does not.[^1]

Once your analyses have been edited in Alma Analytics, you will have to edit the database and add or remove the columns that will host your new data:

1. Open `app\Http\Controllers\AcquisitionsListsController.php
2. Find these lines:
    ```
    Schema::create($table_name, function (Blueprint $table) {
                            $table->id();
                            $table->text('Author')->nullable();
                            $table->dateTime('Creation Date')->nullable();
                            $table->string('MMS Id')->nullable();
                            $table->string('Publication Date')->nullable();
                            $table->text('Publisher')->nullable();
                            $table->string('Resource Type')->nullable();
                            $table->text('Subjects')->nullable();
                            $table->text('Title')->nullable();
                            $table->text('Start Range')->nullable();
                            $table->timestamp('Imported at')->nullable();
                        });
    ```
3. Add or remove columns that you require.
4. Find these lines:
   ```
   DB::table($table_name)->insert(
        [
            'Author' => $data['Column1'] ?? null,
            'Creation Date' => $data['Column2'] ?? null,
            'MMS Id' => $data['Column3'] ?? null,
            'Publication Date' => $data['Column4'] ?? null,
            'Publisher' => $data['Column5'] ?? null,
            'Resource Type' => $data['Column6'] ?? null,
            'Subjects' => $data['Column7'] ?? null,
            'Title' => $data['Column8'] ?? null,
            'Start Range' => $data['Column9'] ?? null,
            'Imported at' => Carbon::now(),
        ]
    );
   ```
5. Add or remove columns as needed.
6. Repeat steps 2. to 5. also for `app\Http\Controllers\old_way\ImportDataFromAlmaController.php` and for `app\Http\Controllers\old_way\IndividualStepsToImportAcquisitionsController.php`

[^1]: To be precise: you can, but you will have to heavily modify either the frontend code, or the way the backend pushes the acquisitions lists to the user. This is out of scope of this documentation.
