<?php

/**
 * Author: Tarjei Huse (tarjei@scanmine.com) http://www.kraken.no
 */
echo "Boootstapping \n";
/* we should be able to work without any other deps  */

@include('Net/URL2.php');
@include('HTTP/Request2.php');
@include "HTTP/Request2/Adapter/Mock.php";
if (!class_exists('Net_URL2')) {
  require __DIR__ . "/../../../vendor/Net/URL2.php";
}
if (!class_exists('HTTP_Request2')) {
  echo "Adding HTTP_Request2\n";
  require __DIR__ . "/../../../vendor/HTTP/Request2.php";
  require __DIR__ . "/../../../vendor/HTTP/Request2/Adapter/Mock.php";
}
require_once __DIR__ . '/../Client.php';
