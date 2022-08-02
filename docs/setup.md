# Setup

## Config file

Once Alma Acquisitions (Open Source) is installed, visit the config files in the ```/config``` folder and make the necessary edits.


This application makes no changes to the standard Laravel config files, but adds a new config file called ```acquisitions.php```

### Acquisitions.php

```
'institution_name' => env('INSTITUTION_NAME', 'My Institution'),
```
Your institution's name.

```
'homepage_acquisitions_list' => env('HOMEPAGE_ACQUISITIONS_LIST', NULL),
```
Select the name of the acquisitions list you would like to display as a landing page.
This variable prevents the accidental deletion of the default acquisitions list in the admin panel.

```
'google_analytics_id' => env('GOOGLE_ANALYTICS_TRACKING_ID', NULL),
```
If you use Google Analytics add your Google Analytics id here to start collecting statistics.

```
'alma_api_key' => env('ALMA_API_KEY', NULL),
```
You Alma API key that will be used by this application.

```
'shared_reports_path' => env('SHARED_REPORTS_PATH', NULL),
```
The 'shared reports' path in Alma Analytics where your acquisitions lists are stored in.
!!!NOTE
    The URL must be encoded. For example, in your ```.env``` file you should add:
    ```SHARED_REPORTS_PATH='%2Fshared%2FUniversity%20of%20Leiden%2FReports%2FDD%20Beheer%2FAanwinsten%2F'```

```
'admin_shared_reports_path' => env('ADMIN_SHARED_REPORTS_PATH', NULL),
```
The 'shared reports' path that will be used in the admin directory.

```
'primo_URL' => env('PRIMO_URL', NULL),
```
You Primo base URL. Example: ```https://catalogue.leidenuniv.nl/```

```
'primo_tab' => env('PRIMO_TAB', NULL),
```
The Primo tab where your acquisitions can be found.

```
'primo_scope' => env('PRIMO_SCOPE', NULL),
```
The Primo search scope where your acquisitions will be found. Most often acquisitions are found in the ```Local``` search scope.

```
'primo_vid' => env('PRIMO_VID', NULL),
```
Your Primo view code.

!!! NOTE
    If you do not want to use the 'tab' and/or the 'scope' filter you must edit the Primo URL in ```resources/views/home.blade.php```. Remove the ```&search_scope=``` and the ```&tab=``` sections. The 'vid' section is required.
