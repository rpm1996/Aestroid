<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class NasaController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function getNasa(Request $request){
        $fromDate = request('from_date');
        $toDate = request('to_date'); 

        if($fromDate>$toDate)
            return redirect()->to('/')->with('alertMessage', 'End Date Should be greater then Start Date')->withInput();

        $sDate = new DateTime(($fromDate));
        $eDate = new DateTime(($toDate)); 
        $diff = $sDate->diff($eDate);
        $day = str_replace("+", '', $diff->format('%R%a')); 
        if($day>7)
            return redirect()->to('/')->with('alertMessage', 'Date range must be 8 Days or less then')->withInput();
 

        $client = new \GuzzleHttp\Client(); 
        $response = $client->get('https://api.nasa.gov/neo/rest/v1/feed?api_key=DEMO_KEY&start_date='.$fromDate.'&end_date='.$toDate.'');  


        $nasaCollectData = json_decode((string)$response->getBody(), true);

        $nasaData = $nasaDataByDate = $nasaAestroid_KMPH = $nasaAestroid_distanceKM =  $nasaAestroid_countingDate =  $averageKM = [];
        $averageSizeOfAetroid = '';

        foreach($nasaCollectData['near_earth_objects'] as $key => $value){
            $nasaDataByDate[$key] = $value;
            foreach ($nasaDataByDate[$key] as $dataByDate) {
                $nasaData[] = $dataByDate; 
            }
        }
        // dd($nasaData);

        foreach($nasaData as $nd){ 
            foreach($nd['close_approach_data'] as $closeAapproachData){
                foreach($closeAapproachData['relative_velocity'] as $relative_velocity => $value){
                    if($relative_velocity == 'kilometers_per_hour'){
                        $nasaAestroid_KMPH[] = $value;
                    }
                }
                foreach($closeAapproachData['miss_distance'] as $miss_distance => $value){
                    if($miss_distance == 'kilometers'){
                        $nasaAestroid_distanceKM[] = $value;
                    }
                }
            }
        }

        $getNasaDataByDate = array_keys($nasaDataByDate);
        foreach($getNasaDataByDate as $key => $value){
            $nasaAestroid_countingDate[$value] = count($nasaDataByDate[$value]);
        }

        // Retrieving Fastest Aestroid
        arsort($nasaAestroid_KMPH);
        $getfastestAestroid = Arr::first($nasaAestroid_KMPH);
        $getfastestAestroidKey = array_key_first($nasaAestroid_KMPH);
        $getfastestAestroidId = $nasaData[$getfastestAestroidKey]['id'];
        // dd($getfastestAestroid, $getfastestAestroidKey, $getfastestAestroidId);

        // Retrieving Closest Aestroid
        asort($nasaAestroid_distanceKM);
        $getclosestAestroid = Arr::first($nasaAestroid_distanceKM);
        $getclosestAestroidKey = array_key_first($nasaAestroid_KMPH);
        $getclosestAestroidId = $nasaData[$getclosestAestroidKey]['id'];
        // dd($getclosestAestroid, $getclosestAestroidKey, $getclosestAestroidId);


        // Average Size of the Asteroids in kilometers
        foreach($nasaAestroid_KMPH as $valueKMPH){
            $averageKM[] = $valueKMPH;
        }
        $averageSizeOfAetroid = array_sum($averageKM) / count($nasaAestroid_KMPH);


        // For Chart Data
        ksort($nasaAestroid_countingDate);
        $nasaCountByDateChartKeys = array_keys($nasaAestroid_countingDate); 
        $nasaCountByDateChartValue = array_values($nasaAestroid_countingDate);
        // dd($nasaCountByDateChartKeys, $nasaCountByDateChartValue);

        return view('welcome', compact('fromDate', 'toDate', 'getfastestAestroid', 'getfastestAestroidId', 'getclosestAestroid', 'getclosestAestroidId', 'averageSizeOfAetroid', 'nasaCountByDateChartKeys', 'nasaCountByDateChartValue'));
    }


}
