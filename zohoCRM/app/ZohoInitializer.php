<?php
namespace com\zoho\crm\sample\initializer;
namespace App;
use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\api\authenticator\OAuthToken;
use com\zoho\api\authenticator\store\DBBuilder;

use com\zoho\api\authenticator\store\FileStore;

use com\zoho\crm\api\InitializeBuilder;

use com\zoho\crm\api\UserSignature;

use com\zoho\crm\api\dc\EUDataCenter;

use com\zoho\api\logger\LogBuilder;

use com\zoho\api\logger\Levels;
use com\zoho\crm\api\dc\USDataCenter;
use com\zoho\crm\api\Header;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\Initializer;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\SDKConfigBuilder;

use com\zoho\crm\api\ProxyBuilder;
use com\zoho\crm\api\record\BodyWrapper;
use com\zoho\crm\api\record\Contacts;
use com\zoho\crm\api\record\GetRecordsHeader;
use com\zoho\crm\api\record\GetRecordsParam;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\util\APIHTTPConnector;

class ZohoInitializer
{
  private $USER_MAIL = "daniel.artemiev@outlook.com";
  public $user=null;
  public $access_token=null;
  public function initialize()
  {
    $logger = (new LogBuilder())
    ->level(Levels::INFO)
    ->filePath("php_sdk_log.log")
    ->build();
    $user = new UserSignature($this->USER_MAIL);
    $environment = USDataCenter::PRODUCTION();
    $tokenstore = new FileStore("php_sdk_token.txt");
    $token = $tokenstore->getTokens()[0];
    $authConnector = new APIHTTPConnector();
    $authConnector -> setUrl('https://accounts.zoho.com/oauth/v2/');
    
    $autoRefreshFields = true;

    $pickListValidation = false;

    $connectionTimeout = 2;//The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.

    $timeout = 2;//The maximum number of seconds to allow cURL functions to execute.

    $target = (new InitializeBuilder())
    ->user($user)
    ->environment($environment)
    ->token($token)
    ->store($tokenstore)
    ->logger($logger)
    ->initialize();
    // $token->generateAccessToken($user, $tokenstore);
    // var_dump($target);
    //$this->getRecord('Contacts');
    $token->authenticate($authConnector); 
    $this->access_token=$token->getAccessToken();
    // $resultAddContactNative = $this->addContactNative($token->getAccessToken(), "ponomarova");
    $tokenstore->saveToken($user, $token);
    return $this->access_token;
    
  }
  public function getAccessTokenFromFile()
  {
    $tokenstore = new FileStore("php_sdk_token.txt");
    $token = $tokenstore->getTokens()[0];
    return $token->getAccessToken();
  }
  public function addContactNative($access_token, $last_name)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.zohoapis.com/crm/v2/Contacts',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "data": [
        {
        "Last_Name": "'.$last_name.'"
        }
    ]
    }',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Zoho-oauthtoken '.$access_token,
        'Content-Type: application/json',
        'Cookie: 1a99390653=b3ca2b20212b8349d89b45b959e9475d; 1ccad04dca=fbdd7144da83fd7b06d98c75cf0dcabc; _zcsr_tmp=8d9e8eff-6d15-4870-9720-e26a08b38927; crmcsr=8d9e8eff-6d15-4870-9720-e26a08b38927'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
  }
  public function addDealNative($access_token, $Deal_Name, $contact){
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.zohoapis.com/crm/v2/Deals',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "data": [
        {
        "Deal_Name": "'.$Deal_Name.'",
        "Contact_Name":{
            "name":"'.$contact['Contact_Name'].'",
            "id":"'.$contact['Contact_ID'].'"
        }
        }
    ]
    }',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Zoho-oauthtoken '.$access_token,
        'Content-Type: application/json',
        'Cookie: 1a99390653=b3ca2b20212b8349d89b45b959e9475d; 1ccad04dca=fbdd7144da83fd7b06d98c75cf0dcabc; _zcsr_tmp=8d9e8eff-6d15-4870-9720-e26a08b38927; crmcsr=8d9e8eff-6d15-4870-9720-e26a08b38927'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
  }
  /* returns error in get_class of Converter, 95 string */
  public function addRecord($moduleAPIName, $LastName){
    $recordOperations = new RecordOperations();

    $paramInstance = new ParameterMap();

    $paramInstance->add(GetRecordsParam::approved(), "false");
    $headerInstance = new HeaderMap();
    $request_body = new BodyWrapper();
    $contact = array(
      'Last_Name' => $LastName
    );
    $Contact_Obj = new Contacts();
    $Contact_Obj->LastName();
    $request_body->setData($contact);
    $response = $recordOperations->createRecords($moduleAPIName, $request_body);
  }
  public function getRecord($moduleAPIName)
  {
    try
    {
      $recordOperations = new RecordOperations();

      $paramInstance = new ParameterMap();

      $paramInstance->add(GetRecordsParam::approved(), "false");
      $headerInstance = new HeaderMap();

      //Call getRecord method that takes paramInstance, moduleAPIName as parameter
      $response = $recordOperations->getRecord("5661264000000413337", $moduleAPIName);

      echo($response->getStatusCode() . "\n");

      print_r($response->getObject());

      echo("\n");
    }
    catch (\Exception $e)
    {
      print_r($e);
    }
  }
}

