<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Support\Facades\Log;
use Validator;
use View;
use Cookie;

class HistoryController extends Controller {

    /**
     * Homepage view with all history data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            $sessid = $this->setCookie();
            
            $date = new \DateTime;
            $date->modify("-" . Config::get('constants.EXPIRED') . " minutes");
            $expired = $date->format('Y-m-d H:i:s');
            $history = History::where('session_id', $sessid)
                    ->where('created_at', '>=', $expired)
                    ->orderBy('created_at', 'desc')
                    ->get();
            
            return View::make('home', compact('history'));
        } catch (Exception $ex) {
            throw new Exception("[" . __CLASS__ . "][" . __METHOD__ . "] : " . $ex->getMessage());
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCache(array $request) {
        try {
            $rules = array("session_id" => "required",
                "city" => "required",
                "tweets" => "required"
            );

            $validation = Validator::make($request, $rules);
            if ($validation->fails()) {
                Log::info('Missing parameter!');

                return false;
            }

            //Save history
            $data = new History();
            $data->session_id = $request['session_id'];
            $data->city = $request['city'];
            $data->data = $request['tweets'];

            $data->save();
        } catch (Exception $ex) {
            throw new Exception("[" . __CLASS__ . "][" . __METHOD__ . "] : " . $ex->getMessage());
        }
    }

    /**
     * Checking cache of search history.
     *
     * @param  int  $sessionId
     * @param  string  $city
     * @return boolean
     */
    public function getCache($sessionId, $city) {
        try {
            $date = new \DateTime;
            $date->modify("-" . Config::get('constants.EXPIRED') . " minutes");
            $expired = $date->format('Y-m-d H:i:s');
            $data = History::where('city', \strtoupper($city))
                    ->where('session_id', $sessionId)
                    ->where('created_at', '>=', $expired)
                    ->get();

            if ($data->isEmpty()) {
                return false;
            }

            return $data->first()->data;
        } catch (Exception $ex) {
            throw new Exception("[" . __CLASS__ . "][" . __METHOD__ . "] : " . $ex->getMessage());
        }
    }
    
    /**
     * Set session id cookie
     * 
     * @return string
     */
    public function setCookie() {
        $getcookies = (Cookie::get('sessid') !== null) ? Cookie::get('sessid') : uniqid('USR'); 
        //update expired time
        Cookie::queue('sessid', $getcookies, Config::get('constants.EXPIRED'));
        
        return Cookie::get('sessid');
    }
}
