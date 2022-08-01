<?php

namespace App\Http\Controllers;

use App\Models\AcquisitionsList;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AcquisitionsListsController extends Controller
{
    /**
     * Display a list of available acquisitions lists that have been generated in our Alma Analytics.
     * This function is used in the /admin path only.
     *
     * @return Application|Factory|View
     * @throws GuzzleException
     */
    public function index(): Renderable
    {
        $path = config('acquisitions.admin_shared_reports_path');
        $almaAPIKey = config('acquisitions.alma_api_key');
        $almaClient = new GuzzleClient();
        $almaURL = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/analytics/paths' . $path . '?apikey=' . $almaAPIKey;
        $almaResponse = $almaClient->request('GET', $almaURL, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'timeout' => 30
        ]);

        $acquisitionLists = json_decode($almaResponse->getBody(), true);

        //NOTE Check if acquisition list exists in this application's database.
        //If it exists: append existsInDatabase = TRUE to the response.
        //Then check at what time the XML file was last modified.
        //Then check at what time the table was last updated.
        //Make an array of this data and pass it to the view.
        //This allows us to display the 'create acquisition list' or 'edit acquisition list' button in the view.

        foreach ($acquisitionLists['path'] as $individualAcquisitionList) {

            $acquisitionListName = $individualAcquisitionList['value'];
            if (!Schema::connection('mysql')->hasTable($acquisitionListName)) {
                $individualAcquisitionList['existsInDatabase'] = FALSE;
            } else {
                $individualAcquisitionList['existsInDatabase'] = TRUE;
                //
                //XML file last modified at...
                $lastModifiedTime = Storage::lastModified('XML/' . $acquisitionListName . '.xml');
                $lastModifiedTime = Carbon::createFromFormat("U", $lastModifiedTime)->setTimezone('Europe/Amsterdam');
                $lastModifiedTime = $lastModifiedTime->format('Y-m-d H:i:s');
                $individualAcquisitionList['XMLLastModified'] = $lastModifiedTime;
                $DBLastUpdated = DB::table($acquisitionListName)->where('id', '1')->value('Imported at');
                $individualAcquisitionList['DBLastModifiedAt'] = $DBLastUpdated;
            }

            $improvedAcquisitionLists[] = $individualAcquisitionList;

            //NOTE: make an array of the acquisitions lists present in Alma Analytics:
            $acquisitionListsNamesInAlma[] = $individualAcquisitionList['value'];
        }

        //NOTE An acquisitions list might have been created in this app, but it might have been deleted in Alma.
        //We must allow the admin to delete that database.
        //We create a list of tables that exist in the database, and a list of Acquisitions List in Alma.
        //We compare them, and show the one that are in the DB, but not in Alma.
        $acquisitionListsNamesInAlma = collect($acquisitionListsNamesInAlma);

        $tablesInDB = DB::select('SHOW TABLES');
        $DB = "Tables_in_" . env('DB_DATABASE');
        $tables = [];
        foreach ($tablesInDB as $table) {
            //do not allow the user to delete these tables. Deleting these lists will break the application!
            if ($table->{$DB} == 'acquisitions'
                or $table->{$DB} == 'acquisitions_lists'
                or $table->{$DB} == 'failed_jobs'
                or $table->{$DB} == 'migrations'
                or $table->{$DB} == 'password_resets'
                or $table->{$DB} == 'users') {
                //do not add these lists to the list.
            } else {
                $tables[] = $table->{$DB};
            }
        }
        $acquisitionsListsInDatabase = collect($tables);

        //Compare the acquisitions lists names in the database with the acquisitions lists names in Alma.
        //Returns and array of the acquisitions lists names that exist in our DB, but not in Alma, and should be deleted.
        $deletedInAlmaAnalyticsButExistsInDatabase = ($acquisitionsListsInDatabase->diff($acquisitionListsNamesInAlma))->all();

        //List of acquisitions in Alma, but not in the application's database:
        //$acquisitionsListsNotCreatedYet = ($acquisitionListsNamesInAlma->diff($acquisitionsListsInDatabase))->all();

        return view('admin.home', compact('improvedAcquisitionLists', 'deletedInAlmaAnalyticsButExistsInDatabase'));
    }

    /**
     * Show the form for creating a new acquisition list.
     *
     *
     * @param $acquisitionListTitle
     * @return string
     */
    public function create($acquisitionListTitle): string
    {
        //NOTE Check if an XML file for this resource exists
        if (!Storage::exists('XML/' . $acquisitionListTitle . '.xml')) {
            $XMLFile['XMLFileExists'] = FALSE;
        } else {
            $XMLFile['XMLFileExists'] = TRUE;
        }

        return view('admin.lists.create', compact('acquisitionListTitle', 'XMLFile'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $acquisitionListTitle
     * @return RedirectResponse
     * @throws ValidationException|GuzzleException
     */
    public function store(Request $request, $acquisitionListTitle): RedirectResponse
    { //TODO we must prevent users from creating an acquisitions list called 'admin' as it would conflict with the routes.

        //STEP 1: validate the request before starting to work on the database.
        $this->validate($request, [
            'acquisitions_alma_source' => 'required|string|unique:acquisitions_lists,acquisitions_alma_source',
            'acquisitions_list_name' => 'required|string|unique:acquisitions_lists,acquisitions_list_name',
            'url_path' => 'required|string|unique:acquisitions_lists,url_path|max:255',
        ]);


        //NOTE if there is not XML with acquisitions for this new list, import it:
        if (!Storage::exists('XML/' . $acquisitionListTitle . '.xml')) {

            $limit = '100';
            $importColumnNames = 'false';
            $path = config('acquisitions.shared_reports_path');

            $almaClient = new GuzzleClient();
            $almaURL = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?limit=' . $limit . '&col_names=' . $importColumnNames . '&path=' . $path . $acquisitionListTitle . '&apikey=' . config('acquisitions.alma_api_key');
            $almaResponse = $almaClient->request('GET', $almaURL, [
                'headers' => [
                    'Accept' => 'application/xml',
                ],
                'timeout' => 60,
            ]);

            $xml = $almaResponse->getBody()->getContents();

            Storage::put('XML/' . $acquisitionListTitle . '.xml', $xml);
        }

        //NOTE: get the acquisitions list's xml, transform it to JSON, and create a new table.
        try {

            $xmlDataString = Storage::get('XML/' . $acquisitionListTitle . '.xml');
            $xmlObject = simplexml_load_string($xmlDataString);
            $json = json_encode($xmlObject);
            $phpDataArray = json_decode($json, true);
            $acquisitions = $phpDataArray['QueryResult']['ResultXml']['rowset']['Row'];

            $table_name = $acquisitionListTitle;

            if (count($acquisitions) > 0) {

                //check is a table with the same name as the acquisitions list exists. If it does exist, empty the table and
                //replace its contents with the new acquisitions.
                if (Schema::hasTable($table_name)) {
                    Log::notice('A table with this name already exists. Emptying table.');
                    DB::table($table_name)->truncate();
                } else {
                    Log::notice('New acquisition list. Creating dedicated table.');

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
                    Log::notice('New acquisitions list table created. ');
                }

                Log::notice('Inserting acquisitions...');

                foreach ($acquisitions as $data) {
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
                }

            }

        } catch (Exception $e) {

            Log::error("Unable to refresh acquisitions databases: " . $e);
            return redirect()->route('home')->with("error", "Failed to import data into the database. See logs for additional information." . $e);
        }

        Log::notice("Importing new acquisitions was successful.");

        //NOTE: we have created a table  for the acquisitions list, and we have imported the data.
        //Since all went well so far, we can create a new item in the database
        try {
            $list = new AcquisitionsList([
                'acquisitions_alma_source' => $request->get('acquisitions_alma_source'),
                'acquisitions_list_name' => $request->get('acquisitions_list_name'),
                'url_path' => Str::slug($request->get('url_path')),
            ]);

            $list->save();

        } catch (Exception $e) {
            Log::error("Unable to create new acquisitions list: " . $e);
            return redirect()->route('home')->with("error", "Something went wrong: " . $e);
        }

        return redirect()->route('home')->with("success", "The list has been successfully created and the acquisitions imported into the database.");

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $acquisitionListTitle
     * @return Application|Factory|View
     */
    public function edit($acquisitionListTitle): string
    {
        $list_data = AcquisitionsList::where('acquisitions_alma_source', $acquisitionListTitle)->first();

        return view('admin.lists.update', compact('list_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  $acquisitionListTitle
     * @return RedirectResponse
     */
    public function update(Request $request, $acquisitionListTitle): RedirectResponse
    {
        //dd($request);
        $request->validate([
            'acquisitions_alma_source' => 'string|required|unique:acquisitions_lists,acquisitions_alma_source,' . $acquisitionListTitle . ' |required',
            'acquisitions_list_name' => 'string|unique:acquisitions_lists,acquisitions_list_name,' . $acquisitionListTitle . '|required',
            'url_path' => 'string|max:255|unique:acquisitions_lists,url_path,' . $acquisitionListTitle . '|required',
        ]);
        {

            $list_data = AcquisitionsList::where('id', $acquisitionListTitle)->first();

            //$list_data->acquisitions_alma_source = $request->input('acquisitions_alma_source'); #disabled, we don't want to change the source.
            $list_data->acquisitions_list_name = $request->input('acquisitions_list_name');
            $list_data->url_path = Str::slug($request->input('url_path'));

            try {
                $list_data->save();
                return redirect()->route('home')->with("success", "The list has been successfully updated.");

            } catch (Exception $e) {
                Log::error("Unable to update acquisitions list: " . $e);
                return redirect()->route('home')->with("error", "Something went wrong and the list was not updated." . $e);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $acquisitionListTitle
     * @return RedirectResponse
     */
    public function destroy($acquisitionListTitle): RedirectResponse
    {
        //NOTE in some environments tables use only lowercase. When asking to delete a table, make the request lowercase.
        //$tableToDelete = Str::lower($acquisitionListTitle);

        try {
            // NOTE If the acquisition list does not exist in the AcquisitionsList table, that means that there is another
            // table, with the name $acquisitionListTitle that has to be deleted.
            if ($acquisitionsList = AcquisitionsList::where('acquisitions_alma_source', $acquisitionListTitle)->first() === null) {
                try {
                    Schema::dropIfExists($acquisitionListTitle);
                } catch (Exception $e) {
                    Log::error("Unable to delete an acquisitions list: " . $e);
                    return redirect()->route('home')->with("error", "Unable to delete this acquisitions list.");
                }
            }
            else {
                // Delete the acquisition list from the AcquisitionsList table and then delete the corresponding table also.
                $acquisitionsList = AcquisitionsList::where('acquisitions_alma_source', $acquisitionListTitle)->first();
                $acquisitionsList->delete();
                Schema::dropIfExists($acquisitionListTitle);
            }
        } catch (Exception $e) {
            Log::error("Unable to delete an acquisitions list's table in database: " . $e);
        }
        Log::notice('Successfully deleted: ' . $acquisitionsList);
        return redirect()->route('home')->with("success", "The list was successfully deleted.");
    }

    /**
     * Display the specified acquisitions list.
     * Note that we must pass also the list of generated acquisitions list so that the user can choose which to visualize.
     *
     *
     * @param $acquisitionListTitle
     * @return string
     *
     */
    public function show($acquisitionListTitle): string
    {
        try {
            //get the list of generated acquisitions lists:
            $acquisitions_lists = AcquisitionsList::all();

            $acquisitions_data = AcquisitionsList::where('url_path', $acquisitionListTitle)->first();
            $acquisitions_list_name = $acquisitions_data->acquisitions_list_name;
            $alma_source = $acquisitions_data->acquisitions_alma_source;

            //get the acquisitions of the current acquisitions list:
            $acquisitions = DB::table($alma_source)->orderBy('Creation Date', 'DESC')->get();
        } catch (Exception $exception) {
            abort(404, 'This acquisitions list does not exist!');
        }


        return view('home', ['acquisitions' => $acquisitions, 'acquisitions_lists' => $acquisitions_lists, 'acquisitionListTitle' => $acquisitionListTitle, 'acquisitionsListName' => $acquisitions_list_name]);

    }
}
