<?php

namespace App\Http\Controllers;

use App\Jobs\BackupRppToGoogleDrive;
use App\Models\Rpp;

class RppBackupController extends Controller
{
    public function backup(Rpp $rpp)
    {
        // Dispatch the job to the queue
        BackupRppToGoogleDrive::dispatch($rpp, auth()->user());

        // Return an immediate success response
        return response()->json(['message' => 'Backup job for RPP ID ' . $rpp->id . ' has been dispatched.']);
    }
}

