<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\WikiController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WikiTest extends TestCase
{
    
    public function test_curl_call(){
        $response = $this->get('/wikiApi/21721040');
        $response_arr = json_decode($response->content(), true);
        $response->assertStatus(200);
        $this->assertNotNull($response);
        $this->assertArrayNotHasKey('error',$response_arr);
        $response->assertJsonStructure(["query"=>["pages"=>["21721040"=>["title","extract"]]]]);
    }

    public function test_count_words(){
        $sentence = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam officia qui quos quod, odit nesciunt facilis iste quis non enim eum esse, ea pariatur corrupti omnis. Facere doloremque delectus dignissimos.";
        $controller = new WikiController();
        $response = $controller->getWordFrequency($sentence);
        $this->assertNotNull($response);
    }

    public function test_own_api()
    {
        $response = $this->get('/wiki/21721040');
        $response_arr = json_decode($response->content(),true);
        $response->assertStatus(200);
        $this->assertNotNull($response);
        $this->assertArrayNotHasKey('error',$response_arr);
        $response->assertJsonStructure(['URL','Title','Top 5 words' => [['word','frequency']]]);
    }

    public function test_own_api_yaml(){
        $response = $this->get('/wiki/21721040/yaml');
        $response->assertStatus(200);
        $this->assertNotNull($response);
        $this->assertNotRegexp('/error/',$response->content());
    }
}
