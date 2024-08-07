<?php

require __DIR__ . '/vendor/autoload.php';

use Doku\Snap\Snap;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Request\CreateVARequestDtoV1;
use Doku\Snap\Models\Utilities\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Models\Utilities\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaRequestAdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\CreateVaVirtualAccountConfig;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\UpdateVaVirtualAccountConfig;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;

/*
* Authentication Stuff
*/

$privateKey1 = "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCvuA0S+R8RGEoT
xZYfksdNam3/iNrKzY/RqGbN4Gf0juIN8XnUM8dGv4DVqmXQwRMMeQ3N/Y26pMDJ
1v/i6E5BwWasBAveSk7bmUBQYMURzxrvBbvfRNvIwtYDa+cx39HamfiYYOHq4hZV
S6G2m8SqDEhONxhHQmEP9FPHSOjPQWKSlgxrT3BKI9ESpQofcxKRX3hyfh6MedWT
lZpXUJrI9bd6Azg3Fd5wpfHQlLcKSR8Xr2ErH7dNS4I21DTHR+6qx02Tocv5D30O
DamA6yG9hxnFERLVE+8GnJE52Yjjsm5otGRwjHS4ngSShc/Ak1ZyksaCTFl0xEwT
J1oeESffAgMBAAECggEAHv9fxw4NTe2z+6LqZa113RE+UEqrFgWHLlv/rqe8jua5
t+32KNnteGyF5KtHhLjajGO6bLEi1F8F51U3FKcYTv84BnY8Rb1kBdcWAlffy9F2
Fd40EyHJh7PfHwFk6mZqVZ69vNuyXsX9XJSX9WerHLhH9QxBCykJiE/4i3owH4dF
Cd/7ervsP32ukGY3rs/mdcO8ThAWffF5QyGd/A3NMf8jRCZ3FwYfEPrgaj9IHV2f
UrwgVc7JqQaCJTvvjrm4Epjp+1mca036eoDj40H+ImF9qQ80jZee/vvqRXjfU5Qx
ys/MHD6S2aGEG5N5VnEuHLHvT51ytTpKA+mAY/armQKBgQDrQVtS8dlfyfnPLRHy
p8snF/hpqQQF2k1CDBJTaHfNXG37HlccGzo0vreFapyyeSakCdA3owW7ET8DBiO5
WN2Qgb7Vab/7vEiGltK4YU/62+g4F0LjWPp25wnbVj81XXW95QrWKjytjU/tgO2p
h47qr8C+3HqMPj1pQ5tcKpJXCwKBgQC/Nrkn0kT+u4KOxXix5RkRDxwfdylCvuKc
3EfMHFs4vELi1kOhwXEbVTIsbFpTmsXclofqZvjkhepeu9CM6PN2T852hOaI+1Wo
4v57UTW/nkpyo8FZ09PtBvOau5B6FpQU0uaKWrZ0dX/f0aGbQKUxJnFOq++7e7mi
IBfX1QCm/QKBgHtVWkFT1XgodTSuFji2ywSFxo/uMdO3rMUxevILVLNu/6GlOFnd
1FgOnDvvtpLCfQWGt4hTiQ+XbQdy0ou7EP1PZ/KObD3XadZVf8d2DO4hF89AMqrp
3PU1Dq/UuXKKus2BJHs+zWzXJs4Gx5IXJU/YMB5fjEe14ZAsB2j8UJgdAoGANjuz
MFQ3NXjBgvUHUo2EGo6Kj3IgxcmWRJ9FzeKNDP54ihXzgMF47yOu42KoC+ZuEC6x
xg4Gseo5mzzx3cWEqB3ilUMEj/2ZQhl/zEIwWHTw8Kr5gBzQkv3RwiVIyRf2UCGx
ObSY41cgOb8fcwVW1SXuJT4m9KoW8KDholnLoZECgYEAiNpTvvIGOoP/QT8iGQkk
r4GK50j9BoPSJhiM6k236LSc5+iZRKRVUCFEfyMPx6AY+jD2flfGxUv2iULp92XG
2eE1H6V1gDZ4JJw3s5847z4MNW3dj9nIi2bpFssnmoS5qP2IpmJW0QQmRmJZ8j2j
OrzKGlO90/6sNzIDd2DbRSM=
-----END PRIVATE KEY-----";
$privateKey2 = "-----BEGIN PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
NhAAAAAwEAAQAAAYEArw0HYdhC7CTHBzSTrS4pS3esDgiQS2+fy1n3milnHwmRGnCzN5MX
dqlqhsY8stzPF10vSWkOONj6uKuRzs8tp9WaCNl8VTnwD3B+okKc0zCBulNwN4Rph6UNj3
B+36+9XeDK8I/U3TpGbEYHW4gGhoNlWC6LgCof1WSdRXukyXdBDfbVMY7L5g2aMDc6Q5Zy
CWtOMT0DIwCvH7HczOQpspH06ABLs8YgSpAOy0azg6Uelkva10Qb4lGPXyVEIWuTK0zsub
4UIPIgXU0uFOgST/5KBRFQJ9QS8AKVqeMixXgnkn5Mh6YN3uvspmgmbUKS/J7O2A6NALow
PY9fOXGyqs85MBoQtTA8xTthAZcvYs32D17D/XrpfXHQmkZ9jxHrB0WvE1/2W1SJxfNr65
mZ2A6c0mQn6/8DUZg2mAaL8QabZ7cFwiWUGpIg5s83tzM7L3o0l1TfCA2Z+aoa69dnsW8F
z+BHINJw56l2qHT41H4M5bgdeeiz8kwP7ADS700xAAAFmGP8KIlj/CiJAAAAB3NzaC1yc2
EAAAGBAK8NB2HYQuwkxwc0k60uKUt3rA4IkEtvn8tZ95opZx8JkRpwszeTF3apaobGPLLc
zxddL0lpDjjY+rirkc7PLafVmgjZfFU58A9wfqJCnNMwgbpTcDeEaYelDY9wft+vvV3gyv
CP1N06RmxGB1uIBoaDZVgui4AqH9VknUV7pMl3QQ321TGOy+YNmjA3OkOWcglrTjE9AyMA
rx+x3MzkKbKR9OgAS7PGIEqQDstGs4OlHpZL2tdEG+JRj18lRCFrkytM7Lm+FCDyIF1NLh
ToEk/+SgURUCfUEvAClanjIsV4J5J+TIemDd7r7KZoJm1CkvyeztgOjQC6MD2PXzlxsqrP
OTAaELUwPMU7YQGXL2LN9g9ew/166X1x0JpGfY8R6wdFrxNf9ltUicXza+uZmdgOnNJkJ+
v/A1GYNpgGi/EGm2e3BcIllBqSIObPN7czOy96NJdU3wgNmfmqGuvXZ7FvBc/gRyDScOep
dqh0+NR+DOW4HXnos/JMD+wA0u9NMQAAAAMBAAEAAAGBAKMLbZ7TAbJVpxOtAwfBATGLq7
P+gffhZmLPz3HFsokULhUEd8kBtk8OCWyy5AJs7G8EmnCz601DvHOZSlvoWMwEhk5L1CTF
rDWVQD398Xg7q/lSkikDqg9vyquZynKqi6UPJbbfIRNVnhZnO58jmYBcjl6OK90aX0AxUN
NREPGdo/hPuc9JA92pOb5DEn+1d04SpmfyPiOyFWteDRzCP5xVmlklPV2a4qTQfRcVKUsq
1syCprjudVJdST8DxOuj94t4GrILgf1Mv6b/pAbYHbiaOfXY/X0hagkGoRwCmBtekhKbGM
TR4Ek8ThSCC7gq6jd5Tv/DmK9qiuBn9nw7rXWCX9Apv9iZQUYZ6551X5h4eW+Cfqg8eNWZ
3aha5h/q7ovtKgRIZkLQf+afpzpPrGnPIse0GP+P4JP9VkYvMMm7NQQ2QMiC8Lhykyi4di
sHutZweutkSMkkzwhBa0w7fQXN1AsZepu27gtE2JwKbYW+f6hNPuGPLPWJZU1kFjdYiQAA
AMADzi4ew9L7D5zjEv2jvaWixlxzMYLywLUIJTeOpiJt4R3gYj/pqNpNx5XGFXILpWAZbO
QN5CtpIYahVuB6KSRwRhmG1Cqp8Gz3DxEoGlwE2l1zebJgyouGSUlSuzH4vIg9n0nShNjZ
pli7VcVO2NJcQUzP0tZRrn+wlowWcrAhN1Tum/dy4JtmveKzSko4iQJtyt43T8trs8DWHp
hFgMhWxOeC11PqHhnAlGV2HaNwpiNdG6CbrpfbdWx+/GYY6AkAAADBANTxKOn3YYHoDVV6
FnQrSH9quBoEhSid0qjk9OJCzViLpX6fP287N9SozrEv6WVNpEkq9QAsQJLac3fGbUliTy
iLa7za8/dXGiFiBvfLXQ5+2VKRkt1ooiYq35qye//jvrNAU0zIuc+lm+WWlQPij9eYvr3N
TZdUJvaPBhbt6ubL2GmYr7N4ZRzGUtOZKwPu8iZNSsgyOBlXY5TSkqTifdB89vH3rOXDSX
q9LMVEy2v0OOX+PhJkNHKET7Tdl/KDzwAAAMEA0nJz7UZVLeL5tZHXH+tHYAfr3gDPTJ4v
qPSAs+IKzPFq0E3BFAtuHLq2FOQXpXdTU4IMniQKCVwCAkGqP11lMrvTE3g3KUoibtvvsH
81WtNx46DansVRPPH+hLJM4rEi4U3UA+lISqrVQP+0FGaU3sTgecywurVHFLKAd0bIRgrB
hNPXUym37lcxYS0UV/NclBHXMop7dip5I3VKGdHBb7M34xPRI8bnVaBwgCRrcVy0eYcqDR
10EFLOdE+6iF7/AAAAIHp1bGZpa2FyQERPS1VzLU1hY0Jvb2stUHJvLmxvY2FsAQI=
-----END PRIVATE KEY-----";
$privateKey3 = "-----BEGIN PRIVATE KEY-----
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
$clientId4 = "BRN-0221-1693209567392";
$clientId2 = "BRN-0201-1708598315618";
$cliendId3 = "BRN-0248-1674717085445";
$clientId1 = "BRN-0221-1693209567392";

$privateKey4 = "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCvuA0S+R8RGEoT
xZYfksdNam3/iNrKzY/RqGbN4Gf0juIN8XnUM8dGv4DVqmXQwRMMeQ3N/Y26pMDJ
1v/i6E5BwWasBAveSk7bmUBQYMURzxrvBbvfRNvIwtYDa+cx39HamfiYYOHq4hZV
S6G2m8SqDEhONxhHQmEP9FPHSOjPQWKSlgxrT3BKI9ESpQofcxKRX3hyfh6MedWT
lZpXUJrI9bd6Azg3Fd5wpfHQlLcKSR8Xr2ErH7dNS4I21DTHR+6qx02Tocv5D30O
DamA6yG9hxnFERLVE+8GnJE52Yjjsm5otGRwjHS4ngSShc/Ak1ZyksaCTFl0xEwT
J1oeESffAgMBAAECggEAHv9fxw4NTe2z+6LqZa113RE+UEqrFgWHLlv/rqe8jua5
t+32KNnteGyF5KtHhLjajGO6bLEi1F8F51U3FKcYTv84BnY8Rb1kBdcWAlffy9F2
Fd40EyHJh7PfHwFk6mZqVZ69vNuyXsX9XJSX9WerHLhH9QxBCykJiE/4i3owH4dF
Cd/7ervsP32ukGY3rs/mdcO8ThAWffF5QyGd/A3NMf8jRCZ3FwYfEPrgaj9IHV2f
UrwgVc7JqQaCJTvvjrm4Epjp+1mca036eoDj40H+ImF9qQ80jZee/vvqRXjfU5Qx
ys/MHD6S2aGEG5N5VnEuHLHvT51ytTpKA+mAY/armQKBgQDrQVtS8dlfyfnPLRHy
p8snF/hpqQQF2k1CDBJTaHfNXG37HlccGzo0vreFapyyeSakCdA3owW7ET8DBiO5
WN2Qgb7Vab/7vEiGltK4YU/62+g4F0LjWPp25wnbVj81XXW95QrWKjytjU/tgO2p
h47qr8C+3HqMPj1pQ5tcKpJXCwKBgQC/Nrkn0kT+u4KOxXix5RkRDxwfdylCvuKc
3EfMHFs4vELi1kOhwXEbVTIsbFpTmsXclofqZvjkhepeu9CM6PN2T852hOaI+1Wo
4v57UTW/nkpyo8FZ09PtBvOau5B6FpQU0uaKWrZ0dX/f0aGbQKUxJnFOq++7e7mi
IBfX1QCm/QKBgHtVWkFT1XgodTSuFji2ywSFxo/uMdO3rMUxevILVLNu/6GlOFnd
1FgOnDvvtpLCfQWGt4hTiQ+XbQdy0ou7EP1PZ/KObD3XadZVf8d2DO4hF89AMqrp
3PU1Dq/UuXKKus2BJHs+zWzXJs4Gx5IXJU/YMB5fjEe14ZAsB2j8UJgdAoGANjuz
MFQ3NXjBgvUHUo2EGo6Kj3IgxcmWRJ9FzeKNDP54ihXzgMF47yOu42KoC+ZuEC6x
xg4Gseo5mzzx3cWEqB3ilUMEj/2ZQhl/zEIwWHTw8Kr5gBzQkv3RwiVIyRf2UCGx
ObSY41cgOb8fcwVW1SXuJT4m9KoW8KDholnLoZECgYEAiNpTvvIGOoP/QT8iGQkk
r4GK50j9BoPSJhiM6k236LSc5+iZRKRVUCFEfyMPx6AY+jD2flfGxUv2iULp92XG
2eE1H6V1gDZ4JJw3s5847z4MNW3dj9nIi2bpFssnmoS5qP2IpmJW0QQmRmJZ8j2j
OrzKGlO90/6sNzIDd2DbRSM=
-----END PRIVATE KEY-----";

$isProduction = false;
$issuer = "";
$publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr7gNEvkfERhKE8WWH5LHTWpt/4jays2P0ahmzeBn9I7iDfF51DPHRr+A1apl0METDHkNzf2NuqTAydb/4uhOQcFmrAQL3kpO25lAUGDFEc8a7wW730TbyMLWA2vnMd/R2pn4mGDh6uIWVUuhtpvEqgxITjcYR0JhD/RTx0joz0FikpYMa09wSiPREqUKH3MSkV94cn4ejHnVk5WaV1CayPW3egM4NxXecKXx0JS3CkkfF69hKx+3TUuCNtQ0x0fuqsdNk6HL+Q99Dg2pgOshvYcZxRES1RPvBpyROdmI47JuaLRkcIx0uJ4EkoXPwJNWcpLGgkxZdMRMEydaHhEn3wIDAQAB
-----END PUBLIC KEY-----";
$timestamp = time();

/**
 * Entry Point
 */
$clientId = $clientId1;
$privateKey = $privateKey1;
$secretKey = "SK-tDzY6MSLBWlNXy3qCsUU";
$Snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey);
// $requestSignature = "LtMvncYrtpqDR41PDQLGXaeznzf0/R1mkUZ6KfWslwEDyRTv/Vb2oQlEhrCxIbmLTPxyajTUF96kmDQ4m3ScCCZlDefcI3ovrm3sTBybk2ZfkwgLy9cIkNLVvoZu4jxkA/nYidCVA3BBglc0HqMd/SDE0YI0/tPMl6kOSBQVUz7RAc4oJQ2XQy91k6wzYVUW0S34AQXu+1hPc6f2Dam8kpHFPg8w7LyLTLEoZehRG6uMAi9dj9Y/oMw4i0xu2ZCfxtOPsWMqPHqszjGTk3jPL9wSihbwLYSxdbpYZ2BkbNjHcWbcdnI6ksUotYe+tLPfOTLfAMcjzeOqwBrMorOwpw==";
// $requestTimestamp = "2024-06-06T11:44:15+07:00";
$virtualAccountMock;

function getToken($Snap) {
    echo "Getting Token B2B: " . PHP_EOL;
    echo $Snap->getTokenAndTime();
}

function createVA($Snap, $updateVaRequestDto) {
    echo "Create VA B2B: " . PHP_EOL;
    $virtualAccount = $Snap->createVa($updateVaRequestDto);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
    return $virtualAccount;
}

function updateVA($Snap, $createVaRequestDto) {
    echo "Updating VA B2B: " . PHP_EOL;
    $virtualAccount = $Snap->updateVa($createVaRequestDto);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

function deleteVA($Snap, $createVaRequestDto) {
    echo "Deleting VA B2B: " . PHP_EOL;
    $virtualAccount = $Snap->deletePaymentCode($createVaRequestDto);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

function checkVA($Snap, $createVaRequestDto) {
    echo "Checking Status VA B2B: " . PHP_EOL;
    $virtualAccount = $Snap->checkStatusVa($createVaRequestDto);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

function createVAV1($Snap, $createVaRequestDtoV1) {
    echo "Creating VA B2B V1: " . PHP_EOL;
    $virtualAccount = $Snap->createVaV1($createVaRequestDtoV1);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

function validateSignature($Snap, $requestSignature, $requestTimestamp) {
    echo "Validating Signature B2B: " . PHP_EOL;
    echo $Snap->validateSignature($requestSignature, $requestTimestamp);
}

function generateTokenB2BResponse($Snap, $requestSignature, $requestTimestamp) {
    $Snap->validateSignatureAndGenerateToken($requestSignature, $requestTimestamp);
    $response = $Snap->tokenB2B;
    echo json_encode($response, JSON_PRETTY_PRINT);
}

function validateSignatureAndGenerateToken($Snap, $requestSignature, $requestTimestamp) {
    $Snap->validateSignatureAndGenerateToken($requestSignature, $requestTimestamp);
}

function generateInvalidSignatureResponse($Snap) {
    echo "Generating Invalid Signature Response: " . PHP_EOL;
    echo json_encode($Snap->generateInvalidSignatureResponse(), JSON_PRETTY_PRINT);
}

function validateTokenB2B($Snap, $requestTokenB2B) {
    echo "Validating Token B2B: " . PHP_EOL;
    echo $Snap->validateTokenB2B($requestTokenB2B);
}

function convertV1toSnap($Snap, $dtov1) {
    echo "Convert V1 to Snap: " . PHP_EOL;
    $virtualAccount = $Snap->createVaV1($dtov1);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

    function convertVAInquiryRequestSnapToV1Form($Snap, string $snapJson)
    {
        echo "Convert VA Inquiry Request Snap To V1 Form: " . PHP_EOL;
        $result = $Snap->convertVAInquiryRequestSnapToV1Form($snapJson);
        echo $result . "\n";
    }

    function convertVAInquiryResponseV1XmlToSnapJson($Snap, string $xmlString)
    {
        echo "Convert VA Inquiry Response V1 Xml To Snap Json: " . PHP_EOL;
        $result = $Snap->convertVAInquiryResponseV1XmlToSnapJson($xmlString);
        echo $result . "\n";
    }


/**
 * Mock Object
 */ 
$createVaRequestDtoV1 = new CreateVaRequestDtoV1(
    "1899", // $mallId
    "CHAIN_MERCHANT", // $chainMerchant
    "12500.00", // $amount
    "12500.00", // $purchaseAmount
    "INV_CIMB_" . $timestamp, // $transIdMerchant
    "VIRTUAL_ACCOUNT", // $PaymentType
    "", // $words
    date("Y-m-d H:i:s"), // $requestDateTime
    "IDR", // $currency
    "IDR", // $purchaseCurrency
    "", // $sessionId
    "T_" . $timestamp, // $name
    "test.bnc." . $timestamp . "@test.com", // $email
    "", // $additionalData
    "", // $basket
    "", // $shippingAddress
    "", // $shippingCity
    "", // $shippingState
    "", // $shippingCountry
    "", // $shippingZipcode
    "VIRTUAL_ACCOUNT_BANK_CIMB", // $paymentChannel
    "", // $address
    "", // $city
    "", // $state
    "", // $country
    "", // $zipcode
    "", // $homephone
    "00000062798", // $mobilephone
    "", // $workphone
    "", // $birthday
    "    1899", // $partnerServiceId
    "2024-06-24T15:54:04+07:00" // $expiredDate
);

$createVaRequestDtoConverted = $createVaRequestDtoV1->convertToCreateVaRequestDto();

$partner = ' 8129014';
$virtualno = '1722399214996';
$createVaRequestDto = new CreateVaRequestDto(
    $partner,
    $virtualno,
    $partner . $virtualno,
    // null,null,null,
   "T_" . $timestamp, // $virtualAccountName
   "test.bnc." . $timestamp . "@test.com", // $virtualAccountEmail
   "621722399214895", // $virtualAccountPhone
   "INV_CIMB_" . $timestamp, // $trxId
   new TotalAmount("12500.00", "IDR"), // $totalAmount
   new CreateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new CreateVaVirtualAccountConfig(true)), // $additionalInfo
   'C', // $virtualAccountTrxType
   "2024-08-06T09:54:04+07:00" // $expiredDate
);

$updateVaRequestDto = new UpdateVaRequestDto(
//    "    1899", // $partnerServiceId
//    "000000000461", // $customerNo
//    "    1899000000000461", // $virtualAccountNo
    $partner,
    $virtualno,
    $partner . $virtualno,
   "T_" . $timestamp, // $virtualAccountName
   "test.bnc." . $timestamp . "@test.com", // $virtualAccountEmail
   "00000062798", // $virtualAccountPhone
   "INV_CIMB_" . $timestamp, // $trxId
   new TotalAmount("14000.00", "IDR"), // $totalAmount
   new UpdateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new UpdateVaVirtualAccountConfig("ACTIVE", "10000.00", "15000.00")), // $additionalInfo
   "O", // $virtualAccountTrxType
   "2024-08-02T15:54:04+07:00" // $expiredDate
);
$virtualAccountMock = createVA($Snap, $createVaRequestDto);

// getToken($Snap);
$snapJson = "{\"body\":{\"partnerServiceId\":\"   70002\",\"customerNo\":\"40000000000000000001\",\"virtualAccountNo\":\"   7000240000000000000000001\",\"virtualAccountName\":\"Customer Name\",\"trxId\":\"23219829713\",\"virtualAccountTrxType\":\"C\",\"totalAmount\":{\"value\":\"11500.00\",\"currency\":\"IDR\"},\"additionalInfo\":{\"channel\":\"VIRTUAL_ACCOUNT_BANK_MANDIRI\"}}}";
$xmlString = "<INQUIRY_RESPONSE><PAYMENTCODE>8975011200005642</PAYMENTCODE><AMOUNT>100000.00</AMOUNT><PURCHASEAMOUNT>100000.00</PURCHASEAMOUNT><MINAMOUNT>10000.00</MINAMOUNT><MAXAMOUNT>550000.00</MAXAMOUNT><TRANSIDMERCHANT>1396430482839</TRANSIDMERCHANT><WORDS>b5a22f37ad0693ebac1bf03a89a8faeae9e7f390</WORDS><REQUESTDATETIME>20140402162122</REQUESTDATETIME><CURRENCY>360</CURRENCY><PURCHASECURRENCY>360</PURCHASECURRENCY><SESSIONID>dxgcmvcbywhu3t5mwye7ngqhpf8i6edu</SESSIONID><NAME>Nama Lengkap</NAME><EMAIL>nama@xyx.com</EMAIL><BASKET>ITEM 1,10000.00,2,20000.00;ITEM 2,20000.00,4,80000.00</BASKET><ADDITIONALDATA>BORNEO TOUR AND TRAVEL</ADDITIONALDATA><RESPONSECODE>0000</RESPONSECODE></INQUIRY_RESPONSE>";
//convertVAInquiryRequestSnapToV1Form($Snap, $snapJson);
//convertVAInquiryResponseV1XmlToSnapJson($Snap, $xmlString);

// sleep(2);

// echo "Virtual Account: " . $virtualAccountMock->virtualAccountData->virtualAccountNo . PHP_EOL;

// sleep(1);
// $updateVaRequestDto = new UpdateVaRequestDto(
//    $virtualAccountMock->virtualAccountData->partnerServiceId, // $partnerServiceId
//    $virtualAccountMock->virtualAccountData->customerNo, // $customerNo
//    $virtualAccountMock->virtualAccountData->virtualAccountNo, // $virtualAccountNo
//    $virtualAccountMock->virtualAccountData->virtualAccountName, // $virtualAccountName
//    $virtualAccountMock->virtualAccountData->virtualAccountEmail, // $virtualAccountEmail
//    "081208120812", // $virtualAccountPhone
//    $virtualAccountMock->virtualAccountData->trxId, // $trxId
//    new TotalAmount("14000.00", "IDR"), // $totalAmount
//    new UpdateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new UpdateVaVirtualAccountConfig("ACTIVE")), // $additionalInfo
//    "C", // $virtualAccountTrxType
//    "2024-08-02T15:54:04+07:00" // $expiredDate
// );
//updateVA($Snap, $updateVaRequestDto);

// $deleteVaRequestDto = new DeleteVaRequestDto(
//    $virtualAccountMock->virtualAccountData->partnerServiceId, // $partnerServiceId
//    $virtualAccountMock->virtualAccountData->customerNo, // $customerNo
//    $virtualAccountMock->virtualAccountData->virtualAccountNo, // $virtualAccountNo
//     $virtualAccountMock->virtualAccountData->trxId, // $trxId
//     new DeleteVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB") // $additionalInfo
// );
//deleteVA($Snap, $deleteVaRequestDto);
// //sleep(1);

// $checkStatusVaRequestDto = new CheckStatusVaRequestDto(
//    $virtualAccountMock->virtualAccountData->partnerServiceId, // $partnerServiceId
//    $virtualAccountMock->virtualAccountData->customerNo, // $customerNo
//    $virtualAccountMock->virtualAccountData->virtualAccountNo, // $virtualAccountNo
//     null,
//     null,
//     null
// );
//checkVA($Snap, $checkStatusVaRequestDto);
// createVAV1($Snap, $createVaRequestDtoV1);
//convertV1toSnap($Snap, $createVaRequestDtoV1);

// validateSignature($Snap, $requestSignature, $requestTimestamp);
// generateTokenB2BResponse($snap, $requestSignature, $requestTimestamp);
// validateSignatureAndGenerateToken($snap, $requestSignature, $requestTimestamp);
// generateInvalidSignatureResponse($snap);
// validateTokenB2B($snap, $requestTokenB2B);










