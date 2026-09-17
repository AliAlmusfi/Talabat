<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldRows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:otp-old-rows {table_name=otps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletefrom the SMS OTPs Table the rows older than the expire_date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $table_name = $this->argument('table_name');
            DB::table($table_name)
            ->where('expire_date', '<=', now())
            ->delete();

            $this->info('OTPS old rows deleted successfully!');
        }catch(Exception $e){
            $this->info('Exception: ' . $e->getMessage());
        }
    }
}
