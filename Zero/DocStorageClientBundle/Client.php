<?php

namespace Zero\DocStorageClientBundle;

/**
 * Client to the DocumentStore
 *
 * The client adds documents to the document store. Its input is basic arrays
 *  that describe a single document ( a bulk api will follow ).
 *
 *  The document array has the following compulsory fields:
 *  dokumentID
 *  url
 *  title
 *  source 
 *  type
 *  body
 *  author
 *  date_created
 *  date_updated
 *
 * @package Zero
 * @version 1.0
 * @copyright (c) 2011 Zero.no
 * @author Tarjei Huse (tarjei@scanmine.com)
 * @license Apache: http://www.apache.org/licenses/LICENSE-2.0.html
 */
class Client
{

    /**
     * apiUrl
     *
     * @var string http url to api
     */
    private $apiUrl;

    /**
     * httpRequest
     *
     * @var object HTTP_Request2
     */
    private $httpRequest;

    /**
     * sourceId id of the source CMS - f.x zero.no.
     *
     * @var string 
     */
    private $sourceId;

    public function __construct($apiUrl, $sourceId)
    {
        $this->apiUrl = $apiUrl;
        $this->httpRequest = new \HTTP_Request2(null);
        $this->sourceId = $sourceId;
    }


    /**
     * add a document.
     *
     * @param array $document adday of document fields.
     * @return void
     */
    public function add($document)
    {
        if (is_object($document) && $document instanceof \Traversable) {
            $document = iterator_to_array($document);
        }
        if (!is_array($document)) {
            throw new Exception("Document must be array!");
        }
        $document['source'] = $this->sourceId;
        $this->validateDocument($document);

        $url = $this->apiUrl . "/add";
        $this->httpRequest->setUrl($url);
        $this->httpRequest->setHeader("Content-Type: application/json");
        $this->httpRequest->setMethod(\HTTP_Request2::METHOD_POST);
        $requestBody = json_encode($document);
        //var_dump($requestBody);
        $this->httpRequest->setBody($requestBody);
        $this->response = $this->httpRequest->send();
        $this->code = $this->response->getStatus();
        if ($this->code == 200) {
            $documentId = $this->response->getHeader("X-ZeroCMS-ID");
            error_log("Document added: $documentId\n" . var_export($document, true));
            return $documentId;
        }

        error_log("Error adding documet: : $documentId\n" . $this->code);
        throw new Exception("Error in storing document " . $document['documentId']
                . " http errorcode: " . $this->code, $this->code);
    }


    public function delete($id)
    {
        $url = sprintf("%s/%s/%s", $this->apiUrl, $this->sourceId, $id);
        $this->httpRequest->setUrl($url);
        $this->httpRequest->setMethod(\HTTP_Request2::METHOD_DELETE);
        $this->httpRequest->setHeader("Content-Type: application/json");
        $response = $this->httpRequest->send();
        if ($response->getStatus() != 200) {
            return false;
        }
        return true;
    }


    public function validateDocument(array $document)
    {
        $vars = array("url", "title", "source", "type", "body", "author"
            , "dateCreated", "dateUpdated", "documentId");

        foreach ($vars as $key) {
            if (!isset($document[$key])) {
                throw new \Exception("Missing required field: $key", -2);
            }
        }
    }


    public function getApiUrl()
    {
        return $this->apiUrl;
    }


    public function getHttpRequest()
    {
        return $this->httpRequest;
    }


    public function getSourceId()
    {
        return $this->sourceId;
    }


}
