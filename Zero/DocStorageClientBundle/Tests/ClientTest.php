<?php

namespace Zero\DocStorageClientBundle;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientTest extends \PHPUnit_Framework_TestCase
{
  private $client;
  private $document;
  private $adapter;

  public function setUp() {
    $this->adapter = new \HTTP_Request2_Adapter_Mock;
    $this->client = new Client("http://localhost/test", "testSource");
    $this->client->getHttpRequest()->setAdapter($this->adapter);

  }

  public function testConstruct() {
    $this->assertEquals($this->client->getApiUrl(), "http://localhost/test");
    $this->assertInstanceOf("HTTP_Request2", $this->client->getHttpRequest());
    $this->assertEquals("testSource", $this->client->getSourceId());
  }


  public function testDeleteDocument() {
    $this->createResponse("", 200);
    $res = $this->client->delete(2, "midgardCMS");
    $this->assertTrue($res);
  }
  public function testDeleteDocumentReturns404() {
    $this->createResponse("", 404);
    $res = $this->client->delete(2, "midgardCMS");
    $this->assertFalse($res);
  }


  public function testAddDocument() {
    $this->createResponse("{}", 200, 'application/json',
        array("X-ZeroCMS-docId: 2-midgardCMS"));
    $res = $this->client->add(array(
            "url" => "http://www.nu.no",
            'documentId' => "2",
            "source" => "midgardCMS",
            "title" => "Testing again",
            "type" => "article",
            "body" => "somebody",
            "author" => "Tarjei",
            "dateCreated"=> "1976-03-06T23:59:59.999Z",
            "dateUpdated"=> "2011-03-06T23:59:59.999Z",
          ));

    $this->assertEquals($res, "2-midgardCMS");
  }


  private function createResponse($body, $code= 200,
      $contentType="application/json", 
      $headers = array()) 
  {

      $phrases = array(
        400 => 'Bad Request',
        404 => 'Not found',
        200 => 'OK',
        500 => 'Internal Server Error',
        401 => 'Unauthorized',
        );

    $response = new \HTTP_Request2_Response("HTTP/1.1 {$code} {$phrases[$code]}", false);
    if ($contentType)  $response->parseHeaderLine("Content-Type: " . $contentType);
    foreach ($headers as $header) {
      $response->parseHeaderLine($header) ;
    }
    $response->appendBody($body);
    $this->adapter->addResponse($response);
  }

  public function testAddBadDocument()
  {
    $requiredVars =array("url" ,"title" ,"source" ,"type" ,"body" ,"author",
        "documentId","dateCreated" ,"dateUpdated");

    $document = array();
    foreach($requiredVars as $var) {
      $this->assertDocumentNotOk($document);
      $document[$var] = $var;
    }
    $this->assertDocumentOk($document);
  }

  function assertDocumentNotOk($document) {
    try {
      $this->client->validateDocument($document);
    } catch (\Exception $e) {
      $this->assertEquals($e->getCode(), -2);
      return;
    }
    $this->fail("This document should not validate: " .
        var_export($document, true));

  }
  function assertDocumentOk($document) {
    $this->client->validateDocument($document);

  }
}

