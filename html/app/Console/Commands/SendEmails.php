<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // sleep(5);
        // Log::info('send mail in background');
        try {
            while(true){
                //to infinity and beyond...
                $this->info('aaa');
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }

    }
}
