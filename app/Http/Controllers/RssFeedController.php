<?php

namespace App\Http\Controllers;

use App\Models\AcquisitionsList;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Throwable;

class RssFeedController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param $feed_url_path
     * @return Response
     */

    public function __invoke($feed_url_path): Response
    {
        /* NOTE
        Similarly to displaying the acquisitions through a view, we are passing the data to the RSS view instead.
            1) Query the URL slug in the URL ($url_path is selected in the routes, web.php)
            2) based on the URL slug, find the entry / id on the database
            3) find the alma acquisitions list source of that entry
            4) find the DB with that alma acquisitions list source name
            5) display the contents of that DB.
        */

        try {
            //NOTE url_path is always unique, so we can use "first"
            $acquisitions_data = AcquisitionsList::where('url_path', $feed_url_path)->first();

            //NOTE now that we have all the data about the acquisitions list, we want to extract the Alma acquisitions list source.
            $alma_source = $acquisitions_data->acquisitions_alma_source;

            //NOTE now that we have the alma source id, we are going to use it to find the right table and get all the data about the acquisitions!
            $acquisitions = DB::table($alma_source)->orderBy('Creation Date', 'DESC')->get();
            //dd($acquisitions);

        } catch (Throwable $e) {
            Log::notice('User landed on a page not linked to an active feed: ' . URL::current());
           abort(404, 'This page does not exist or an feed for this acquisitions list has not been created yet.');

        }

        //NOTE if we could get all the data, then the data is up-to-date, and we can inform the user that the feed are as fresh as they can be.

        $today = Carbon::today()->toDateString();

        //NOTE unlike the acquisition page, we pass only the data of the selected list to the RSS view.
        return response()->view('vendor.feed.atom_ubl', ['acquisitions' => $acquisitions, 'feed_url_path' => $feed_url_path, 'today' => $today])->header('Content-Type', 'application/xml');
    }

}
