<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WikiController extends Controller
{
    // wiki url to fetch data from
    protected $url;

    public function __construct(){
        // wiki pedia url with query paramter page-id kept as blank
        $this->url = "https://en.wikipedia.org/w/api.php?action=query&explaintext=true&format=json&prop=extracts&pageids=";
    }

    // Method to make curl request and get the json data
    public function CurlRequest($pageId){
        // Appending page id received in get request to url
        $this->url = $this->url."".$pageId;
        // CURL process
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

    // Method to get frequencies of each word
    public function getWordFrequency($sentence){
        // explode helps to separate the words in paragraph/sentence into an array
        $sentence_arr = explode(" ",$sentence);
        // Get the count of array in variable for use in for loop to save computation of counting in each iteration
        $count = count($sentence_arr);
        if($count>0){
            $final = array();
            // stop words from https://gist.github.com/sebleier/554280
            $stop_words = "i,me,my,myself,we,our,ours,ourselves,you,your,yours,yourself,yourselves,he,him,his,himself,she,her,hers,herself,it,its,itself,they,them,their,theirs,themselves,what,which,who,whom,this,that,these,those,am,is,are,was,were,be,been,being,have,has,had,having,do,does,did,doing,a,an,the,and,but,if,or,because,as,until,while,of,at,by,for,with,about,against,between,into,through,during,before,after,above,below,to,from,up,down,in,out,on,off,over,under,again,further,then,once,here,there,when,where,why,how,all,any,both,each,few,more,most,other,some,such,no,nor,not,only,own,same,so,than,too,very,s,t,can,will,just,don,should,now";
            $stop_words = explode(',', $stop_words);
            for($i =0 ;$i<$count;$i++){
                // convert to lowercase
                $sentence_arr[$i] = strtolower($sentence_arr[$i]);

                // consider alpha numeric and words not in stop word list
                if(ctype_alnum($sentence_arr[$i]) && !in_array($sentence_arr[$i],$stop_words)){
                    if($final[$sentence_arr[$i]]){
                        $final[$sentence_arr[$i]] += 1;
                    } else {
                        $final[$sentence_arr[$i]] = 1;
                    }
                }
            }
            // sort the array in descending order of the count
            arsort($final);
            $sorted = array();
            $count = count($final);
            // merge all words with similar count into 1 string and in comma separated form
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
        // Make CURL request
        $result = $this->CurlRequest($pageId);
        // Convert JSON data into array
        $result = json_decode($result ,true);
        // Fetch the title
        $title = $result["query"]["pages"][$pageId]["title"];
        if($title == ""){
            $title = "No Title found";
        }
        // Fetch page content
        $result = $result["query"]["pages"][$pageId]["extract"];
        if($result != ""){
            // Get Frequencies of each word in array format in descending order of frequencies
            $result = $this->getWordFrequency($result);
            // Get the top 5 values of array
            $result = array_slice($result,0,5,true);
        }
        // If there is no data from above processing then display error
        if(count($result) == 0){
            $response = '{"error" : true,"message" : "No data for given page id. Please try again later or change the page id"}';
        } else {
            // Check if output is required in YAML or JSON(default)
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
