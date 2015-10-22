<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Thujohn\Twitter\Facades\Twitter;
use Illuminate\Html;
class ApiController extends Controller
{   
    /**
     * Getting Tweets using Twitter APIs
     *
     * @return \Illuminate\Http\Response
     */
    public function search(){
        try {
            $response = ['stat' => true ,'message' => null, 'tweets' => null];
            $input=Input::all();
            
//            if(empty($input) || (is_array($input) && (!array_key_exists('lat', $input) || !array_key_exists('lng', $input) || !array_key_exists('words', $input))) ){
//                $response['message'] = 'Missing parameters!';
//                $response['stat'] = false;
//                
//                return json_encode($response);
//            }
            
            $response['tweets'] = $this->getResponse($input);
            
            return json_encode($response);
        } catch (Exception $ex) {
            throw new Exception("[".__CLASS__."][".__METHOD__."] : ".$ex->getMessage());
        }
    }
    
    /**
     * Getting Tweets using Twitter APIs
     *
     * @param  array  $input = ['lat' , 'lng', 'words']
     * @return array ['position' , 'icon' , 'tweet']
     */
    public function getResponse($input) {
        try {
            $tweets = [];
            $jsonString = file_get_contents("http://localhost/map/public/sample.json");
//            $jsonString = Twitter::getSearch(array('q' => $input['words'], 'geocode' => "{$input['lat']},{$input['lng']},50km" , 'count' => 10, 'format' => 'json'));
            $apiResponse = json_decode($jsonString);
            
            foreach($apiResponse->statuses as $tweetData) {
                $tweets[] = [
                    'position' => ['lat' => $tweetData->geo->coordinates[0] , 'lng' => $tweetData->geo->coordinates[1] ],
                    'icon' => $tweetData->user->profile_image_url,
                    'tweet'=> $this->getInfoWindow($tweetData)
                ];
            }
            
            return $tweets;
        } catch (Exception $ex) {
            throw new Exception("[".__CLASS__."][".__METHOD__."] : ".$ex->getMessage());
        }
    }
    
    /**
     * Template for infoWindow tooltips gmaps
     * 
     * @param object $param
     * @return string
     */
    public function getInfoWindow($param) {
        $template = "<div id='twitter-window'>"
                . "<div style='float:left;margin-bottom:5px;color:#55ACEE'><strong>@{$param->user->screen_name}</strong></div>"
                . "<div style='margin-top:3px;margin-left:5px;float:right;color:#8899a6;font-size:10px;'><strong>".Twitter::ago($param->created_at)."</strong></div>"
                . "<div style='clear:both;'>{$param->text}</div>"
                . "</div>";
        
        return $template;
    }
}
