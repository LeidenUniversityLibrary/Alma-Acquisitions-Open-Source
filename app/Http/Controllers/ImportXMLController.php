<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportXMLController extends Controller
{

    public function __construct()
    {
        //NOTE The Alma Analytics API is very slow. Allow this script to run longer than the standard 60 seconds:
        set_time_limit(120);
    }

    /**
     * Creates an XML file for an individual acquisition list while creating a new list.
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
            return redirect()->route('home')->with("error", "Failed to create XML file. Please try again. If the problem persists see the application's log files.");
        }
        Log::info('Alma API responded! Creating XML file.');

        $xml = $almaResponse->getBody()->getContents();

        Storage::put('XML/' . $acquisitionListTitle . '.xml', $xml);

        Log::info('XML file created.');

        if (!Storage::exists('XML/' . $acquisitionListTitle . '.xml')) {
            $XMLFile['XMLFileExists'] = FALSE;
        } else {
            $XMLFile['XMLFileExists'] = TRUE;
        }

        Log::info('XML file created: ' . $acquisitionListTitle . '.xml');
        return redirect()->route('create_new_acquisitions_list', $acquisitionListTitle)->with(['acquisitionListTitle' => $acquisitionListTitle, 'XMLFile' => $XMLFile])->with("success", "Successfully imported XML.");
    }
}
