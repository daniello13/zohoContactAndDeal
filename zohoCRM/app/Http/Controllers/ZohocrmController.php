<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use com\zoho\api\authenticator\store\FileStore;
use Illuminate\Http\Request;
use com\zoho\api\logger\Logger;
use App\ZohoInitializer;
use Exception;
use Illuminate\Support\Facades\Response;

class ZohocrmController extends Controller
{
    /**
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        if($request->pin == '1997'){
            $zohoInitialize = new ZohoInitializer();
            $access_token = $zohoInitialize->initialize();
            return view('zohoCRM')->with('access_token', $access_token);
        }
        else{
            return view('welcome');
        }
    }
    public function sendData(Request $request)
    {
        try
        {
            $responseTxt = "";
            
            $responseContact_str = $this->sendContact($request)->content();
            $responseTxt .= $responseContact_str;
            $responseObj = array
            (
                "responseContact" => $responseContact_str
            );
            $responseContact = json_decode($responseContact_str, true);
            $contactIDgenerated = $responseContact['data'][0]['details']['id'];  
            if($request->to_create_Deal === "true"){
                $DealName=$request->Deal_Name;
                $contact = array
                (
                    "Contact_Name" => $request->Contact_Last_Name,
                    "Contact_ID" => $contactIDgenerated
                );
                $responseDeal_str = $this->sendDeal($request, $DealName, $contact)->content();
                $responseTxt .= "\n\n".$responseDeal_str;
                $responseObj['responseDeal'] =  $responseDeal_str;
            }
            $response = Response::make(json_encode($responseObj), 200);
            $response->header('Content-Type', 'text/json');
            return $response;
        }
        catch(Exception $ex){
            $response = Response::make($responseContact_str."\n\n"."", 200);
            $response->header('Content-Type', 'text/json');
            return $response;
        }
    }
    public function sendContact(Request $request)
    {
        // $zohoInitialize = new ZohoInitializer();
        // $access_token = $zohoInitialize->initialize();
        // $zohoInitialize->
        $zohoInitialize = new ZohoInitializer();
        $access_token = $zohoInitialize->getAccessTokenFromFile();
        $responseAddContact=$zohoInitialize->addContactNative($access_token, $request->Contact_Last_Name);
        $response_str = $responseAddContact;
        $response = Response::make($responseAddContact, 200);
        $response->header('Content-Type', 'text/json');
        return $response;

    }
    public function sendDeal(Request $request, $DealName, $contact)
    {
        // $zohoInitialize = new ZohoInitializer();
        // $access_token = $zohoInitialize->initialize();
        // $zohoInitialize->
        $zohoInitialize = new ZohoInitializer();
        $access_token = $zohoInitialize->getAccessTokenFromFile();
        // $DealName = $request->Deal_Name;
        // $contact = array
        // (
        //     "Contact_Name" => $request->Contact_Last_Name,
        //     "Contact_ID" => $request->Contact_ID
        // );
        $responseAddContact=$zohoInitialize->addDealNative($access_token, $DealName, $contact);
        $response_str = $responseAddContact . "\n\n";


        $response = Response::make($responseAddContact, 200);
        $response->header('Content-Type', 'text/json');
        return $response;

    }
}