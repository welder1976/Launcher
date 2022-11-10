<?php
header('Content-Type: application/json; charset=UTF-8');

class SoapHandler
{
    public static function SendRequest($user, $pass, $command, $realmId)
    {
        global $config;

        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = 'No response';
        $jsonObj->success = false;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $config['soap'][$realmId]['address'].':'.$config['soap'][$realmId]['port'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="'.$config['soap'][$realmId]['uri'].'" xmlns:xsd="http://www.w3.org/1999/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <SOAP-ENV:Body>
                <ns1:executeCommand>
                    <command>'.$command.'</command>
                </ns1:executeCommand>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($user.':'.$pass),
            'Content-Type: application/xml'
        ),
        ));

        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        curl_close($curl);

        switch ($info['http_code']) 
        {
            case 0:
                $jsonObj->responseMsg = 'No response';
                $jsonObj->success = false;
                break;
            case 200:
                self::LogSoapCommand($user, $command, $realmId);
                $jsonObj->responseMsg = 'Successfull request ("'.$command.'").';
                $jsonObj->success = true;
                break;
            case 401:
                $jsonObj->responseMsg = 'Unauthorized request.';
                $jsonObj->success = false;
                break;
            case 403:
                $jsonObj->responseMsg = 'Forbidden command.';
                $jsonObj->success = false;
                break;
            case 500:
                $jsonObj->responseMsg = 'Unrecognized command.';
                $jsonObj->success = false;
                break;
            default:
                $jsonObj->responseMsg = 'Status code error: '.$info['http_code'];
                $jsonObj->success = false;
                break;
        }

        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
        
        return json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    private static function LogSoapCommand($user, $command, $realmId)
    {
        $accountId = Auth::GetAccountId($user);

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            $query = $mysqli->prepare('INSERT INTO `soap_logs` (`account_id`, `account_name`, `realm_id`, `command`) VALUES (?, ?, ?, ?)');

            assert($query);

            $query->bind_param('isis', $accountId, $user, $realmId, $command);
            $query->execute();
        }
        mysqli_close($mysqli);
    }
}
// SoapHandler::SendCommand(base64_encode('username:password'), 'server info', 1);


// https://www.php.net/manual/en/function.curl-getinfo.php

/*HTTP codes   [Curl Status Codes] [Webcron Status codes]
Informational Codes (1xx)
    100 Continue. The client SHOULD continue with its request.
    101 Switching Protocols. The server understands and is willing to comply with the client's request, via the Upgrade message header field, for a change in the application protocol being used on this connection.
Successful Codes (2xx)
    200 OK. The request has succeeded.
    201 Created. The request has been fulfilled and resulted in a new resource being created.
    202 Accepted. The request has been accepted for processing, but the processing has not been completed.
    203 Non-Authoritative Information. The returned metainformation in the entity-header is not the definitive set as available from the origin server, but is gathered from a local or a third-party copy.
    204 No Content. The server has fulfilled the request but does not need to return an entity-body, and might want to return updated metainformation.
    205 Reset Content. The server has fulfilled the request and the user agent SHOULD reset the document view which caused the request to be sent.
    206 Partial Content. The server has fulfilled the partial GET request for the resource.
Redirection Codes (3xx)
    300 Multiple Choices. The requested resource corresponds to any one of a set of representations, each with its own specific location, and agent-driven negotiation information is being provided so that the user (or user agent) can select a preferred representation and redirect its request to that location.
    301 Moved Permanently. The requested resource has been assigned a new permanent URI and any future references to this resource SHOULD use one of the returned URIs.
    302 Found. The requested resource resides temporarily under a different URI.
    303 See Other. The response to the request can be found under a different URI and SHOULD be retrieved using a GET method on that resource.
    304 Not Modified. If the client has performed a conditional GET request and access is allowed, but the document has not been modified, the server SHOULD respond with this status code.
    305 Use Proxy. The requested resource MUST be accessed through the proxy given by the Location field. The Location field gives the URI of the proxy.
    307 Temporary Redirect. The requested resource resides temporarily under a different URI.
Client Error Codes (4xx)
    400 Bad Request. The request could not be understood by the server due to malformed syntax.
    401 Unauthorized. The request requires user authentication.
    403 Forbidden. The server understood the request, but is refusing to fulfill it.
    404 Not Found. The server has not found anything matching the Request-URI.
    405 Method Not Allowed. The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.
    406 Not Acceptable. The resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headers sent in the request.
    407 Proxy Authentication Required. This code is similar to 401 (Unauthorized), but indicates that the client must first authenticate itself with the proxy.
    408 Request Timeout. The client did not produce a request within the time that the server was prepared to wait.
    409 Conflict. The request could not be completed due to a conflict with the current state of the resource.
    410 Gone. The requested resource is no longer available at the server and no forwarding address is known.
    411 Length Required. The server refuses to accept the request without a defined Content-Length.
    412 Precondition Failed. The precondition given in one or more of the request-header fields evaluated to false when it was tested on the server.
    413 Request Entity Too Large. The server is refusing to process a request because the request entity is larger than the server is willing or able to process.
    414 Request-URI Too Long. The server is refusing to service the request because the Request-URI is longer than the server is willing to interpret.
    415 Unsupported Media Type. The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method.
    416 Requested Range Not Satisfiable. A server SHOULD return a response with this status code if a request included a Range request-header field, and none of the range-specifier values in this field overlap the current extent of the selected resource, and the request did not include an If-Range request-header field.
    417 Expectation Failed. The expectation given in an Expect request-header field could not be met by this server, or, if the server is a proxy, the server has unambiguous evidence that the request could not be met by the next-hop server.
    Server Error Codes (5xx)
    500 Internal Server Error. The server encountered an unexpected condition which prevented it from fulfilling the request.
    501 Not Implemented. The server does not support the functionality required to fulfill the request.
    502 Bad Gateway. The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.
    503 Service Unavailable. The server is currently unable to handle the request due to a temporary overloading or maintenance of the server.
    504 Gateway Timeout. The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server specified by the URI (e.g. HTTP, FTP, LDAP) or some other auxiliary server (e.g. DNS) it needed to access in attempting to complete the request.
    505 HTTP Version Not Supported. The server does not support, or refuses to support, the HTTP protocol version that was used in the request message.
    525 SSL Handshake Failed. The SSL handshake between Webcron and the server that hosts the domain failed.
Curl Status Code
    0 No Response
    1 Protocol not supported
    2 initialisation failed
    3 Invalid URL
    5 Could not resolve proxy
    6 Could not resolve host
    7 Could not Connect
    8 FTP Weird Server Reply
    9 Script Time Out
    10 Too much data received (> 500kb), connection dropped.
Webcron status Code    [top]
    900 Scheduled for execution
    901 Assigned to server
    902 No credit
    909 Network error
    911 Not executed
    920 Content does not match
    921 Content changed
    922 Akeeba Backup error*/