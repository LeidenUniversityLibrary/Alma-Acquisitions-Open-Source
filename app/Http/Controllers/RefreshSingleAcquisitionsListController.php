<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RefreshSingleAcquisitionsListController extends Controller
{
    public function __construct()
    {
        //NOTE The Alma Analytics API is very slow. Allow this script to run longer than the standard 60 seconds:
        set_time_limit(120);
    }

    /**
     * Refresh a single XML file while editing an individual acquisitions list.
     *
     * @param Request $request
     * @param $acquisitionListTitle
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function __invoke(Request $request, $acquisitionListTitle): RedirectResponse
    {
        $limit = '100';
        $importColumnNames = 'false';
        $path = config('acquisitions.shared_reports_path');
        Log::info('Initiating XML import for: ' . $acquisitionListTitle);

        try {
            $almaClient = new GuzzleClient();
            $almaURL = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?limit=' . $limit . '&col_names=' . $importColumnNames . '&path=' . $path . $acquisitionListTitle . '&apikey=' . config('acquisitions.alma_api_key');
            Log::info('Contacting Alma API at this URL: ' . $almaURL);
            $almaResponse = $almaClient->request('GET', $almaURL, [
                'headers' => [
                    'Accept' => 'application/xml',
                ],
                'timeout' => 60,
            ]);
        } catch (Exception $e) {

            Log::error("Unable to create XML file: " . $e);
            return redirect()->route('home')->with("error", "Failed to refresh XML file. Please try again. If the problem persists see the application's log files.");
        }
        Log::info('Alma API responded! Creating XML file.');

        $xml = $almaResponse->getBody()->getContents();

        Storage::put('XML/' . $acquisitionListTitle . '.xml', $xml);

        if (!Storage::exists('XML/' . $acquisitionListTitle . '.xml')) {
            $XMLFile['XMLFileExists'] = FALSE;
        } else {
            $XMLFile['XMLFileExists'] = TRUE;
        }

        Log::info('XML file created' . $acquisitionListTitle . '.xml');
        Log::info('Updating database.');

        try {
            $xmlDataString = Storage::get('XML/' . $acquisitionListTitle . '.xml');
            $xmlObject = simplexml_load_string($xmlDataString);
            $json = json_encode($xmlObject);
            $phpDataArray = json_decode($json, true);
            $acquisitions = $phpDataArray['QueryResult']['ResultXml']['rowset']['Row'];

            $table_name = $acquisitionListTitle;

            if (count($acquisitions) > 0) {

                //check is a table with the same name as the acquisition list exists. If it does exist, empty the table and
                //replace its contents with the new acquisitions.
                Log::notice('Emptying acquisitions...');

                DB::table($table_name)->truncate();

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

        Log::notice("Refreshing acquisitions was successful.");
        return redirect()->route('update_acquisitions_list', $acquisitionListTitle)->with(['acquisitionListTitle' => $acquisitionListTitle, 'XMLFile' => $XMLFile])->with("success", "Successfully refreshed this acquisition list. New acquisitions has been loaded into the database and are visible by the end users.");

    }
}
