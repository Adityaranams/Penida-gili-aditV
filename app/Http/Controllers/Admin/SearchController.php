<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ConsoleSearch;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Results for the console header's search box. */
class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = (string) $request->query('q', '');
        $groups = ConsoleSearch::run($term);

        return view('admin.search', [
            'term' => $term,
            'groups' => $groups,
            'total' => ConsoleSearch::total($groups),
        ]);
    }
}
