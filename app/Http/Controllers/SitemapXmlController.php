<?php

namespace App\Http\Controllers;

use App\Models\AcquisitionsList;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SitemapXmlController extends Controller
{
    public function index(): Response
    {
        $currentDate = Carbon::today('Europe/Amsterdam')->addHours(6)->toIso8601String();
        $acquisitionLists = AcquisitionsList::all();
        return response()->view('sitemap', [
            'acquisitionsLists' => $acquisitionLists,
            'currentDate' => $currentDate
        ])->header('Content-Type', 'text/xml');
    }
}
