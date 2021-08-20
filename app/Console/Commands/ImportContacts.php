<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\csvFile;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import modules from stored csv files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = base_path("resources/modules/*.csv"); 
    
        foreach (array_slice(glob($path),0,2) as $file) {
            $data = array_map('str_getcsv', file($file));
            $i=0;
            foreach($data as $row) {
                $csv = new csvFile();
                $csv->Module_code = $row[$i];
                $csv->Module_name = $row[$i+1];  
                $csv->Module_term = $row[$i+2];  
                $csv->save();
            }

            //delete the file
            unlink($file);
        }

        
    }
}
