#!/usr/bin/php
<?php

/*
 This is an example of adding documents to a local instance of the document
 storage backend.
 */

require __DIR__ . "/../Zero/DocStorageClientBundle/Tests/bootstrap.php";

//require __DIR__ . "/../Zero/DocStorageClientBundle/Client.php";

use Zero\DocStorageClientBundle\Client;

$client = new Client("http://localhost/zero/ZeroCms/api/document", "testStore" );
$document = array(
            "documentId" => 42,
            "type"=> "sample",
            "title" => "The Lightning Thief",
            "author" => "Rick Riordan",
            "body" => "Percy Jackson and the Olympians",
            "dateCreated"=> "1976-03-06T23:59:59.999Z",
            "dateUpdated"=> "2011-03-06T23:59:59.999Z",
            "url"=> "http://www.zero.com"
    );

$res = $client->add($document);
var_dump($res);
