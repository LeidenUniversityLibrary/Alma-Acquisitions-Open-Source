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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ForceRefreshAllAcquisitionsListsController extends Controller
// NOTE This controller forces a refresh of the database and the XML files.
{
    public function __construct()
    {
        //NOTE Alma Analytics is slow in giving responses. This script must be able to run for an extended time.
        //In this case, we allow it to run for 10 minutes.
        set_time_limit(600);
    }

    /**
     * This controller refreshes all the generated acquisitions lists with new data.
     * Since the Alma Analytics API is slow, we allow this process to run for longer.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Log::info('Initiating database refresh with new acquisitions');

        $XMLAcquisitionFiles = Storage::files('XML/');

        Log::info('The following files will be updated: ' . print_r($XMLAcquisitionFiles, true));

        foreach ($XMLAcquisitionFiles as $acquisitionsFile) {

            $acquisitionListTitle = pathinfo($acquisitionsFile)['filename'];

            //Check if we have a table with the name of the XML file. If we do not, don't to anything.
            if (!Schema::hasTable($acquisitionListTitle)) {
                Log::notice('XML file exists, but no table is linked to it. Skipping.');
            } else {
                Log::info($acquisitionsFile . ' Refreshing XML file.');

                $limit = '100';
                $importColumnNames = 'false';
                $path = config('acquisitions.shared_reports_path');
                $almaAPIKey = config('acquisitions.alma_api_key');

                $almaClient = new GuzzleClient();
                $almaURL = 'https://api-eu.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?limit=' . $limit . '&col_names=' . $importColumnNames . '&path=' . $path . $acquisitionListTitle . '&apikey=' . $almaAPIKey;
                Log::info('Calling the ALMA API and refreshing XML file: ' . $acquisitionsFile);
                //NOTE: if Alma doesn't respond within 60 seconds, skip and move on to the next request.
                try {
                    $almaResponse = $almaClient->request('GET', $almaURL, [
                        'headers' => [
                            'Accept' => 'application/xml',
                        ],
                        'timeout' => 60,
                    ]);
                } catch (Exception $e) {
                    Log::error('No response received from Alma Analytics, Skipping: ' . $acquisitionsFile);
                }

                Log::info('Alma API response received!');
                try {
                    $xml = $almaResponse->getBody()->getContents();

                    Storage::put('XML/' . $acquisitionListTitle . '.xml', $xml);

                    Log::info('Successfully generated refreshed file: ' . $acquisitionsFile . '. Inserting new data into database.');

                    //insert content of the refreshed xml files into the individual databases
                    $xmlDataString = Storage::get('XML/' . $acquisitionListTitle . '.xml');
                    $xmlObject = simplexml_load_string($xmlDataString);
                    $json = json_encode($xmlObject);
                    $phpDataArray = json_decode($json, true);
                    $acquisitions = $phpDataArray['QueryResult']['ResultXml']['rowset']['Row'];

                    $table_name = $acquisitionListTitle;

                    if (count($acquisitions) > 0) {

                        DB::table($table_name)->truncate();
                        Log::notice('Refreshing acquisitions for: ' . $acquisitionsFile);

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
                                    'Imported at' => Carbon::now()
                                ]

                            );
                        }

                    }
                    Log::notice('Successfully refreshed: ' . $acquisitionsFile);
                } catch (Exception $e) {
                    Log::error('Unable to refresh ' . $acquisitionsFile);
                }
                try {
                    LOG::notice($acquisitionListTitle . ' - refreshing database entries.');
                    $xmlDataString = Storage::get('XML/' . $acquisitionListTitle . '.xml');
                    $xmlObject = simplexml_load_string($xmlDataString);
                    $json = json_encode($xmlObject);
                    $phpDataArray = json_decode($json, true);
                    $acquisitions = $phpDataArray['QueryResult']['ResultXml']['rowset']['Row'];

                    $table_name = $acquisitionListTitle;

                    if (count($acquisitions) > 0) {

                        DB::table($table_name)->truncate();
                        Log::notice('Refreshing acquisitions for: ' . $acquisitionsFile);

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
                                    'Imported at' => Carbon::now()
                                ]
                            );
                        }
                    }
                    Log::notice('Successfully refreshed: ' . $acquisitionsFile);
                } catch (Exception $e) {
                    Log::error('Unable to refresh ' . $acquisitionsFile);
                }
            }
        }
        //TODO This is not true: if an exception occurs (an acquisitions list is not refreshed), it gets logged, but the admin user will not informed, unless it looks in the logs.
        // Inform the admin user that some refreshes have failed via email or teams.
        Log::notice("Refresh process completed.");
        return redirect()->route('home')->with("success", "The refreshing process is complete.");

    }

}
