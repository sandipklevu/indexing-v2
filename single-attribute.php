<?php
ini_set('display_errors', '1');

$attributeName = 'color'; //TODO REPLACE
$restKey = 'a2xldnUtMTcwODQ5NTM0OTgxxxxxxxxxxxxxxxxxxxxxxx='; //TODO REPLACE
$method = 'put';
$requestUri = '/v2/attributes/'.$attributeName;
$requestQuery = null;
$apiKey = 'klevu-xxxxxxxxxxxx';  //TODO REPLACE
$indexingEndPoint = 'https://indexing.ksearchnet.com'.$requestUri;

$payload = file_get_contents('attribute.json');
$timeStamp = generateUTCTime() . ':00.000Z';

$commonHeaders = [
    'X-KLEVU-TIMESTAMP: ' . $timeStamp,
    'X-KLEVU-APIKEY: ' . $apiKey,
    'X-KLEVU-AUTH-ALGO: HmacSHA384',
    'Content-Type: application/json',
];
function triggerCurl($url, $headers, $payload)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Request Error:' . curl_error($ch);
    }
    curl_close($ch);
    echo $response;
}

function generateBearToken($requestString, $secretKey)
{
    return base64_encode(
        hash_hmac(
            'sha384',
            $requestString,
            $secretKey,
            true,
        ),
    );
}

function generateUTCTime()
{
    $utcTime = new DateTime(
        'now',
        new DateTimeZone('UTC')
    );

    return $utcTime->format('Y-m-d\TH:i');
}

$requestTokenPayload = [
    strtoupper($method),
    rtrim($requestUri, ' /'),
    $requestQuery
        ? '?' . trim($requestQuery)
        : '',
    'X-KLEVU-TIMESTAMP' . '=' . $timeStamp,
    'X-KLEVU-APIKEY' . '=' . $apiKey,
    'X-KLEVU-AUTH-ALGO' . '=' . 'HmacSHA384',
    'Content-Type' . '=' . 'application/json',
    $payload,
];
$requestString = implode(PHP_EOL,$requestTokenPayload);
echo $requestString;
$token = generateBearToken($requestString, $restKey);


array_push(
    $commonHeaders,
    'Authorization: Bearer ' . $token,
);


echo triggerCurl($indexingEndPoint, $commonHeaders, $payload);
