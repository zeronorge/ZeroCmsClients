#!/usr/bin/bash
<?php

/**
 * Simple script to import articles from octopress. First testclient to ZeroCMS
 * TODO: Rewrite script in ruby and add Rakefile.
 * Author: Tarjei Huse (tarjei@scanmine.com) http://www.kraken.no
 */


class OctoPressImporter {

  public function __construct($client, $postsPath) {
    $this->client = $client;
    $this->path = $postsPath;
  }


  public function run() {

    $posts = glob($this->path . "/*.markdown");
    foreach($posts as $post) {
        
      print $post ;
      $story = new OctoPressStory($post);
      try {

      $this->client->add($story->toDocumentArray());
      } catch (Exception $e) {
        print $e->getMessage() . "\n";
        print $e->getTraceAsString() . "\n";
        print "Array: \n" . json_encode($story->toDocumentArray());
        print "\n";
        throw $e;

      }
      print ".... done\n";
    }

  }
}


class OctoPressStory {

  public $body = "";
  public $categories ="";
  
  public function __construct($file) {
    $this->documentId = basename($file, ".markdown");
    $this->parse(file_get_contents($file));
  }

  public function toDocumentArray() {
    return array('documentId' => $this->documentId, 'source' => 'kraken.no',
        'body' => $this->body, 'title' => $this->title, 
        'tags' => $this->categories, 
        'dateCreated' => $this->makeDate(), 
        'dateUpdated' => $this->makeDate(),
        'author' => 'tarjei@kraken.no',
        'type' => 'blogPost',
        'url' => $this->makeUrl(),
        );
  }

  public function makeDate() {
    $date = new DateTime($this->date);
    return $date->format('Y-m-d\\TH:i:s.000\\Z');
  }
  public function makeUrl() {
    $date = new DateTime($this->date);
    return sprintf("http://www.kraken.no/blog/%s/%s", $date->format("Y/m/d"),
        preg_replace("/^\\d{4}-\\d{2}-\\d{2}-/","",$this->documentId));
  }

  public function parse($content) {
    $lines = preg_split("/\n/", $content);
    $numLinesWithDashes=0;
    for($i = 0; $i < count($lines); $i++) {
      $line = $lines[$i];
      if (substr($line, 0, 3) == '---') {
        $numLinesWithDashes++;
        continue;
      }
      if ($numLinesWithDashes == 1) {
        list($fieldname, $value) = preg_split("/:/", $line, 2);
        $fieldname = trim($fieldname, "\"'\n\t ");
        $value = trim($value, "\"'\n\t ");
        $this->$fieldname = $value;
      } else if ($numLinesWithDashes == 2) {
        $this->body .= $line . "\n";
      }
    }

  }

}
// TODO: move bootstrap to root
require __DIR__ . "/../../Zero/DocStorageClientBundle/Tests/bootstrap.php";

$path = $_SERVER['argv'][1];

use Zero\DocStorageClientBundle\Client;

$client = new Client("http://localhost/zero/ZeroCms/api/document", "kraken.no" );

$poster = new OctoPressImporter($client,$path );
$poster->run();

//$story= new OctoPressStory($path) ;
//var_dump($story->toDocumentArray());
