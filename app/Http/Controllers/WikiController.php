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
            $stop_words = "i,me,my,myself,we,our,ours,ourselves,you,your,yours,yourself,yourselves,he,him,his,himself,she,her,hers,herself,it,its,itself,they,them,their,theirs,themselves,what,which,who,whom,this,that,these,those,am,is,are,was,were,be,been,being,have,has,had,having,do,does,did,doing,a,an,the,and,but,if,or,because,as,until,while,of,at,by,for,with,about,against,between,into,through,during,before,after,above,below,to,from,up,down,in,out,on,off,over,under,again,further,then,once,here,there,when,where,why,how,all,any,both,each,few,more,most,other,some,such,no,nor,not,only,own,same,so,than,too,very,s,t,can,will,just,don,should,now";
            $stop_words = explode(',', $stop_words);
            for($i =0 ;$i<$count;$i++){
                $sentence_arr[$i] = strtolower($sentence_arr[$i]);
                if(ctype_alnum($sentence_arr[$i]) && !in_array($sentence_arr[$i],$stop_words)){
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
