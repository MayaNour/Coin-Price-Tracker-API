<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

class PricesController extends Controller
{

    public function getPrice(){

        $coin = request('coin');

        if($coin)
        {
        // Retrieve information about the bitcoin currency
        $currencyIdInfo = $this->getCryptoCurrencyInformation($coin);

        return response()->json([ $coin . ' Price' => $currencyIdInfo['price_usd'] ]);

        }else{
            return 'please pass a currency name';
        }
    }

    public function postPrice(Request $request){

        $currencyIdInfo = $this->getCryptoCurrencyInformation($request['coin']);

        return response()->json([ $request['coin'] . ' Price' => $currencyIdInfo['price_usd'] ]);
    }

    public function index($currencyId){
    
        // Retrieve information about the bitcoin currency
        $currencyIdInfo = $this->getCryptoCurrencyInformation($currencyId);

        return response(403)->json([ $currencyId . ' Price' => $currencyIdInfo['price_usd'] ]);

        // return view('show', [
        //     'bitcoinInfo' => $bitcoinInfo
        // ]);
    }

    private function getCryptoCurrencyInformation($currencyId, $convertCurrency = "USD"){
        // Create a new Guzzle Plain Client
        $client = new Client();

        // Define the Request URL of the API with the providen parameters
        $requestURL = "https://api.coinmarketcap.com/v1/ticker/$currencyId/?convert=$convertCurrency";

        // Execute the request
        $singleCurrencyRequest = $client->request('GET', $requestURL);
        
        // Obtain the body into an array format.
        $body = json_decode($singleCurrencyRequest->getBody() , true)[0];

        // If there were some error on the request, throw the exception
        if(array_key_exists("error" , $body)){
            throw $this->createNotFoundException(sprintf('Currency Information Request Error: $s', $body["error"]));
        }

        // Returns the array with information about the desired currency
        return $body;
    }

}
