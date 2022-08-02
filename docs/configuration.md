## Alma Analytics

Alma Acquisitions (Open Source) requires data to be generated in Alma Analytics first. This data will then be requested via the Alma Analytics API and finally imported into the application.

``` mermaid
graph LR
A[Alma Analytics API] --> |1. requests data from| B[Alma Analytics];
B --> |2. requests data from| C[Oracle Analytics Server]
C --> |3. sends data to| B;
B --> |4. sends data to| A;
A --> |5. sends data to| D[Alma Acquisitions Open Source];
```

!!! Important
      **Before attempting to run this application and importing data, you must make sure that your Alma Analytics data configuration is properly setup.**

!!! Note
      Alma Analytics refreshes its data only once a day, furthermore it is build on top of Oracle Analytics Server (OAS)[^1]. For performance and usability reasons, this application stores and consumes data from a database, rather than relying on *on-the-fly* requests via the Alma Analytics API.

### Creating an 'Latest Acquisitions' query in Alma Analytics

!!! Important
      Alma Acquisitions (Open Source) expects **exactly** the following columns. You can use other columns, but you will have to edit the views in `resources\views` and the JavaScript in `public_html\js\datatable.js` to accommodate your customizations.

In Alma Analytics, create a new 'Analysis'. In the 'Criteria' tab, add the following columns:

(Bibliographic Details)  

* MMS Id  
* Title  
* Author  
* Publisher  
* Publication Date  
* Resource Type  
* Creation Date  
* Subjects

(LC Classifications)  

* Start Range

As 'Filters', add the following:

* (Advanced SQL Filter) ```"Bibliographic Details"."Creation Date" >= TIMESTAMPADD(SQL_TSI_MONTH, -6, CURRENT_DATE)```[^2]  
  ![Advanced SQL filter](/assets/images/docs/advanced_sql_filter.png){ loading=lazy }
**AND**
* ```MMS Id is not equal to any  MMS Id in Acquisitions Process Types```[^3]  
  ![MMS Id](/assets/images/docs/mms_id.png){ loading=lazy }
**AND**
* ```Resource Type is equal to / is in  Book - Electronic; Book - Physical; Book chapter - Electronic; Journal - Electronic; Journal - Physical```[^4]
  ![Resource Type](/assets/images/docs/resource_type.png){ loading=lazy }
**AND**
* (Physical Items)```Bibliographic Lifecycle is equal to / is in  In Repository```[^5]  
   **OR**
  * (E-Inventory)```Bibliographic Lifecycle is equal to / is in  In Repository```[^6]

Your criteria should look like this:
![Latest acquisitions criteria in Alma Analytics](/assets/images/docs/all_acquisitions.png){ loading=lazy }

### Creating an acquisitions list for a specific subject in Alma Analytics

To create an acquisitions list that includes items from a specific subject, start by following the steps in the [previous chapter](configuration.md#creating-an-latest-acquisitions-query-in-alma-analytics). In the 'Criteria' tab, add a new **AND** to your filters and choose any filter that may fit your case. For example, if you are creating a acquisitions list for *Russian Studies*, you might want to add a filter like:
```Subjects contains any Russia.```  

![Russian Studies filter](/assets/images/docs/russian_studies.png){ loading=lazy }

You can add as many filters as you need.

!!! Tip
      At Leiden University Libraries, the filters for a specific acquisitions list are defined by the Subject Specialist and implemented in Alma Analytics by one of the Alma System Managers.

## Configuration file

You can change the configuration of the Alma Acquisitions (Open Source) app in the configuration file:

```config/acquisitions.php```

You can set the values of each option in your `.env` file.

### General

* `institution_name => env('INST_NAME', NULL),`  
Your institution's name. Defaults to `My Institution`.

* `homepage_acquisitions_list => env('HOMEPAGE_ACQUISITIONS_LIST', NULL),`  
The name of the acquisitions list that will be displayed on the homepage off the app. Defaults to `NULL`.

* `google_analytics_id => env('GOOGLE_ANALYTICS_TRACKING_ID', NULL)`  
Your Google Analytics ID. Defaults to `NULL`.

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
      Alma Acquisitions (Open Source) searches for an item's MMS ID, which is a unique identifier; this is why in the URL construction we chose for `any,exact,MMS_ID`. If your institution prefers or requires another URL construction, you can easily edit this part to fit your needs by looking at the [Performing Advanced Searches](https://knowledge.exlibrisgroup.com/Primo/Product_Documentation/Primo/End_User_Help_-_New_UI/020Performing_Advanced_Searches) page in the documentation.

[^1]: ExLibris <2022> [Introduction to Analytics and Terminology](https://knowledge.exlibrisgroup.com/Alma/Product_Documentation/010Alma_Online_Help_(English)/080Analytics/010Introduction). (Accessed: 1 June 2022)
[^2]: ```SQL_TSI_MONTH, -6, CURRENT_DATE``` will collect all the acquisitions in the previous 6 months from the current date. You can edit the amount to fit your needs.
[^3]: ```MMS Id is not equal to any  MMS Id in Acquisitions Process Types``` filters items that are not yet available to the library users.
[^4]: ```Resource Type is equal to / is in  Book - Electronic; Book - Physical; Book chapter - Electronic; Journal - Electronic; Journal - Physical``` are the type of materials we want to show our users.
[^5]: (Physical Items)```Bibliographic Lifecycle is equal to / is in  In Repository``` and (E-Inventory)```Bibliographic Lifecycle is equal to / is in  In Repository``` prevent displaying empty records to the end users.
