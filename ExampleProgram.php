<?php

require_once  "src/Snap.php";

$clientKey = 'BRN-0248-1674717085445'; // It is actually a client ID
$privateKey = "-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCH2013jxHy1agi5nueS2D8pH5y
CHplzIj93xWYhxeNDIguBN6XZRuauHG3rfRRGH/ALohIY5b9lonUQBTwvgfGO4tnwai6VsdetH5a
GUcGwa59iZh5TIgUdqp187CDAqJDYu0ere2jxYMzTAJZKpTrSfe/ifhCVB1ACM7b0aQ8dE3FeUhc
+aVKonh8XWlcxEooRPjxLltWH2jzt85ldYDCdArHvRb9rQdicBfuepvrHJl/cTxlUxd3tXy5vzGz
EqJTq562YuyVZtX57gC4ZZrxYfe0wK9i8QZkBrxGTkdBFOevMgAQzhVmQR418E90XkK+uj6TJlYi
XsVxT7uWfKVzAgMBAAECggEBAIbuFniCTi9CaKWRCIHlF8SUk0kqhnYIuJ58LHS653cdVTtvdqwi
rVHzkm39hUPt8yOqk4xh7RqbovR9WM7pzcriZMh+HNhFS+oRldRiepqJToY8XIVMr3KzkQVpLIxR
11raK+tmjzky9+XAvixVEGbHphpEK5+k7xAkL18/TcEDxYQu3RmLNvLONGchZUXPSYbauspyBuf/
FJbH+gqBX+SAYKzJJSu9VtrHmAERDvtaNOliwQvT1WDgHLwpMvXTTEKhaou6qYR7AG2hIPQT+nHa
sTsLXN+9DC/tEi8Opb3OHyvw9SMC5gJbarwNAHgRPVc7qZGzVXrEea1kdu8hYdECgYEAz86ZW56F
7lTxs0vuhPzFXW2cQZvDoDi5IwL2o9MoCklzwzJZeGD7N1rfoDK9mAGQuLC0ap7iXZy6AuhV0q97
o9UJpydpy5Dh25vH3UjDYxBwfVBAt6y/TgKcQe7CEHl05MdDJu//bJanwZSxbvq9NsGyzB5k1Z7L
ZboqISwJPQ0CgYEAp10Li3SMpef1sg/GMhImvsRxgYB05XksLCHuRlYxUw1qk5infOURY/MmdEEE
JcooAIpuJkkXdyQLRbpjU1xDOHWwthG7us1MQKHurDi7o6TbqUx7iBCVReT8cIZQ3JQGQAxkPJAS
d+0td5C0UpFUoXLx4BJdLo6wyIHYmfAFzH8CgYAftvYsx2rFTu18YbBLV5B/i8T3NmCKyV1n/IHL
yuQnfcJPHhYNiy+L6TCL8HKDCmod5coDI7CEfPDelLrUZrfF7zOD8T3yNXBi5cmA+iPnsJCab28R
GSoxK7DRVzEC9qZibA7RmHsxBWUg5CKYP2g1PSaehFz7RTrhkaHwYhoe2QKBgQCH44hoJq28V2aq
uRwXs505746ps382gvhWrQYmnf1WjeInDR+QzP0dxmNGqTOQ618ncT6WX2pqFh4A86GKIbOCuCxO
6H8g4Wg0YkbEFxxjdovUHoF+rNhG8/Hz+1rUfmvEvUr10ZTtQupT1m5TTCUHIak6Yi6+iqUHaEZS
VwyeSQKBgCZXrvUPIQAHNtfi58EVCDYBhZK/e0KhZIZxiNSW7aGPYUCt+WundSgNZE2In45AnmbB
PIlKe0aPF/zgMzRJoi2vnfNzG8Lo6kd6ACP9UGs763VZ96M2b3fqahpZIcui6FaF+6XdK41Kls8t
Mxz+iuYBDeqRKo3q9Du8lzaaEzXu
-----END PRIVATE KEY-----
";
$isProduction = false;

$Doku = new DokuSnap($privateKey, $clientKey, $isProduction);


$clientSecret = 'SK-PFACMl2FMX2WltdcuZes';
$clientKeyBCAInquiry = 'MCH-0776-1634272777674';
$clientSecret = $clientKeyBCAInquiry;

$createVAResponse = $Doku->createVa();

if (isset($createVAResponse['error'])) {
  echo "Error creating VA: " . $createVAResponse['error'] . "\n\n";
  exit;
}

$vaNumber = $createVAResponse['virtualAccountData']['virtualAccountNo'];
echo "Successfully created VA with number: " . $vaNumber;