<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WikiController extends Controller
{
    protected $url;

    public function __construct(){
        $this->url = "https://en.wikipedia.org/w/api.php?action=query&explaintext=true&format=json&prop=extracts&pageids=";
    }

    public function CurlRequest($pageId){
        
        $this->url = $this->url."".$pageId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');

        $result = curl_exec($ch);

        if (!$result){
            return false;
        } else {
            return $result;
        }
    }

    public function getWordFrequency($sentence){
        $sentence_arr = explode(" ",$sentence);
        $count = count($sentence_arr);
        if($count>0){
            $final = array();
            for($i =0 ;$i<$count;$i++){
                if(ctype_alnum($sentence_arr[$i])){
                    $sentence_arr[$i] = strtolower($sentence_arr[$i]);
                    if($final[$sentence_arr[$i]]){
                        $final[$sentence_arr[$i]] += 1;
                    } else {
                        $final[$sentence_arr[$i]] = 1;
                    }
                }
            }
            arsort($final);
            $sorted = array();
            $count = count($final);
            foreach($final as $key=>$value){
                if($sorted[$value]){
                    $sorted[$value] = $sorted[$value].",".$key;
                } else {
                    $sorted[$value] = $key;
                }
            }
            if(count($sorted) > 0){
                return $sorted;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function countFrequency($pageId,$format=null){
        $result = $this->CurlRequest($pageId);

        $result = json_decode($result ,true);
        $title = $result["query"]["pages"][$pageId]["title"];
        if($title == ""){
            $title = "No Title found";
        }
        $result = $result["query"]["pages"][$pageId]["extract"];
        if($result != ""){
            $result = $this->getWordFrequency($result);
            $result = array_slice($result,0,5,true);
        }
        if(count($result) == 0){
            $response = '{"error" : true,"message" : "No data for given page id. Please try again later or change the page id"}';
        } else {
            if(strtolower($format) == "yaml"){
                $response = "URL : $this->url\nTitle : $title\nTop 5 words : \n";
                foreach($result as $key=>$value){
                    $response .= " - " .$key. " " .$value."\n";
                }
            } else {
                $words = array();
                foreach($result as $key=>$value){
                    $temp = [
                        "word" => $value,
                        "frequency" => $key
                    ];
                    array_push($words,$temp);
                }
                $response = [
                    "URL" => $this->url,
                    "Title" => $title,
                    "Top 5 words" => $words
                ];
                $response = json_encode($response,true);
            }
        }
        return $response;
    }
}
