<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        //redirect to the latest acquisitions.
        return redirect()->route('read_acquisitions_list', ['latest']);

    }
}
