<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Jobs\FileReader;
use App\csvFile;
use File;
use Illuminate\Support\Facades\Schema;
use Mail;


class FileUploadController extends Controller
{
    //

    public function fileUploadView()
    {
        return view('fileupload');
    }
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
            // mimes:xlsx
           
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','data' => 'Invalid extension'],400);
        }else{ 
            
            $err = [];
            $csv = fopen($request['file'], 'r');  
            $headers = fgetcsv($csv, 0, ',');
            $file_table = new csvFile();
            $columns = Schema::getColumnListing($file_table->getTable());
            // dd($columns);
            $file = $request['file'];
            $count = count($headers);
            // dd($count);
            $status = 0;
            for($i=0;$i<$count;$i++)
            {
                // dd($headers[$i] == $columns[$i+1]);
                if($headers[$i] != $columns[$i+1])
                {
                    $status =1;
                    $err[] = "Error in header ".$i;
                }
            }    
            $path = request()->file('file')->getRealPath();
            $file = file($path);    
            $data = array_slice($file, 1);
            $parts = (array_chunk($data, 1000));
            $i = 1;
            $values = [];
            $j = 1;
            foreach($parts as $line) {
                foreach($line as $lines)
                { 
                    $values = explode(",",$lines);
                    for($i=0;$i<count($values);$i++)
                    {
                        if($i== count($values)-1)
                        {
                            $val=[];
                            $val = explode("\r",$values[$i]);
                            // dump($val[0]);
                            if(!preg_match("^[a-zA-Z0-9]+$^",$val[0]))
                            {
                                $status =1;
                                $err[] = "Error in ".$headers[i]." in row".$j;
                            }
                        }
                        else{
                            // dump($values[$i]);
                            // dump(!preg_match("^[a-zA-Z0-9]+$^",$values[$i]));
                            if(!preg_match("^[a-zA-Z0-9]+$^",$values[$i])){
                                $status =1;
                                $err[] = "Error in ".$headers[$i]." in row".$j;
                            }
                        }
                        // dump($values[$i]); 
                       
                    }
                    $j++;
                  
                }
                // dd("hi");
                $filename = base_path('resources/modules/'.date('y-m-d-H-i-s').$i.'.csv');
                file_put_contents($filename, $line);
                $i++;
            }
        }
        if($status == 1)
        {
            // dd(json_encode($err));
           $this->sendMail(json_encode($err));
            return response()->json(['status' => 'error','data' => $err],400);
        }
        else{
            $this->sendMail("File has been successfully uploaded");
            return response()->json(['status' => 'success','data' => "File has been successfully uploaded"],200);
        }
    }


    public function sendMail($body)
    {
        $to_name = "charush";
        $to_email = 'charush@accubits.com';
        // charush@accubits.com
        $data = array('name'=>"Sender", "body" => $body);
        Mail::send("emails.mail", $data, function($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)
        ->subject("CSV File error");
        $message->from('neenuofficial2789@gmail.com','CSV');
        });
    }
}
