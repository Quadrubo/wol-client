<?php

namespace App\Actions;

use App\Events\ComputerReachableStatusUpdated;
use App\Jobs\PingComputer;
use App\Models\Computer;
use App\Support\Concerns\InteractsWithBanner;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Throwable;

class PingComputersAction
{
    use InteractsWithBanner;

    public function execute()
    {
        if (! Auth::check()) {
            flash('You have to be authenticated to ping computers.')->error();

            return back();
        }

        $batchArray = [];

        $computers = Computer::all();

        foreach ($computers as $computer) {
            array_push($batchArray, new PingComputer($computer));
        }

        $batch = Bus::batch($batchArray)
        ->then(function (Batch $batch) {
            // All jobs completed successfully...
            ComputerReachableStatusUpdated::dispatch();
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...
        })->dispatch();

        flash('Pinging computers...');
    }
}
