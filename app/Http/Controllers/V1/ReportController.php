<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
      public function index()
    {
        return Report::latest()
            ->paginate();
    }

    public function download(
    Report $report
) {

    return Storage::disk('public')
        ->download(
            $report->file_path
        );
}
}
