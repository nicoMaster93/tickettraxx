<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use App\Models\FscModel;
use App\Models\TicketsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Item;

class QuickBooksController extends Controller
{
    private function config(){
        return array(
            'authorizationRequestUrl' => 'https://appcenter.intuit.com/connect/oauth2',
            'tokenEndPointUrl' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            'client_id' => Config::get('services.quickbooks.client_id'),
            'client_secret' => Config::get('services.quickbooks.client_secret'),
            'oauth_scope' => 'com.intuit.quickbooks.accounting openid profile email phone address',
            'oauth_redirect_uri' => Config::get('services.quickbooks.redirect_uri')
        );
    }
    public function getAuthUrl(){
        $config = $this->config();
        
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $config['client_id'],
            'ClientSecret' =>  $config['client_secret'],
            'RedirectURI' => $config['oauth_redirect_uri'],
            'scope' => $config['oauth_scope'],
            'baseUrl' => "Production"
        ));
        
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        return $authUrl;
    }
    
    public function token(Request $request){
        $config = $this->config();

        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $config['client_id'],
            'ClientSecret' =>  $config['client_secret'],
            'RedirectURI' => $config['oauth_redirect_uri'],
            'scope' => $config['oauth_scope'],
            'baseUrl' => "Production"
        ));

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->input('code'), $request->input('realmId'));
        $dataService->updateOAuth2Token($accessToken);
        session(['sessionAccessToken' => $accessToken]);
    }
    
    public function getTableCostumers($idCustomer){
        if (session()->has('sessionAccessToken')) {
            $accessToken = session('sessionAccessToken');
            $config = $this->config();
            if($accessToken->getAccessTokenExpiresAt() < strtotime("now")){
                $dataService = DataService::Configure(array(
                    'auth_mode' => 'oauth2',
                    'ClientID' => $config['client_id'],
                    'ClientSecret' =>  $config['client_secret'],
                    'RedirectURI' => $config['oauth_redirect_uri'],
                    'baseUrl' => "Production",
                    'refreshTokenKey' => $accessToken->getRefreshToken(),
                    'QBORealmID' => $accessToken->getRealmId(),
                ));
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                $accessToken = $OAuth2LoginHelper->refreshToken();
                $dataService->updateOAuth2Token($accessToken);
                session(['sessionAccessToken' => $accessToken]);
            }

            

            $dataService = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $config['client_id'],
                'ClientSecret' => $config['client_secret'],
                'accessTokenKey' => $accessToken->getAccessToken(),
                'refreshTokenKey' => $accessToken->getRefreshToken(),
                'QBORealmID' => $accessToken->getRealmId(),
                'baseUrl' => "Production"
            ));  
            $dataService->updateOAuth2Token($accessToken);
            // $dataService->setLogLocation("F:\Trabajo\Concrete\Log");
            $dataService->throwExceptionOnError(true);
            $customers = $dataService->FindAll('customer');
            $error = $dataService->getLastError();

            if($error){
                return response()->json([
                    "success" => false,
                    "message" => $error->getResponseBody(),
                    "error" => $error->getOAuthHelperError(), 
                ], $error->getHttpStatusCode());
            }
            
            return response()->json([
                "success" => true,
                "table_customer" => view("customers.ajax.QuickBooksCustomersTable", ["customers" => $customers, "idCustomer" => $idCustomer])->render()
            ]);
        }
        else{
            return response()->json([
                "success" => false,
                "message" => "Quickbooks session has expired"
            ]);
        }
    }

    public function getItems(){
        if (session()->has('sessionAccessToken')) {
            $accessToken = session('sessionAccessToken');
            $config = $this->config();
            if($accessToken->getAccessTokenExpiresAt() < strtotime("now")){
                $dataService = DataService::Configure(array(
                    'auth_mode' => 'oauth2',
                    'ClientID' => $config['client_id'],
                    'ClientSecret' =>  $config['client_secret'],
                    'RedirectURI' => $config['oauth_redirect_uri'],
                    'baseUrl' => "Production",
                    'refreshTokenKey' => $accessToken->getRefreshToken(),
                    'QBORealmID' => $accessToken->getRealmId(),
                ));
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                $accessToken = $OAuth2LoginHelper->refreshToken();
                $dataService->updateOAuth2Token($accessToken);
                session(['sessionAccessToken' => $accessToken]);
            }

            

            $dataService = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $config['client_id'],
                'ClientSecret' => $config['client_secret'],
                'accessTokenKey' => $accessToken->getAccessToken(),
                'refreshTokenKey' => $accessToken->getRefreshToken(),
                'QBORealmID' => $accessToken->getRealmId(),
                'baseUrl' => "Production"
            ));  
            $dataService->updateOAuth2Token($accessToken);
            // $dataService->setLogLocation("F:\Trabajo\Concrete\Log");
            $dataService->throwExceptionOnError(true);
            $items = $dataService->FindAll('Item');
            $error = $dataService->getLastError();

            if($error){
                return array(
                    "success" => false,
                    "message" => $error->getResponseBody(),
                    "error" => $error->getOAuthHelperError(), 
                );
            }
            
            return array(
                "success" => true,
                "items" => $items
            );
        }
        else{
            return array(
                "success" => false,
                "message" => "Quickbooks session has expired"
            );
        }
    }

    public function insertInvoice(Request $request){
        if (session()->has('sessionAccessToken')) {
            $accessToken = session('sessionAccessToken');
            $config = $this->config();
            if($accessToken->getAccessTokenExpiresAt() < strtotime("now")){
                $dataService = DataService::Configure(array(
                    'auth_mode' => 'oauth2',
                    'ClientID' => $config['client_id'],
                    'ClientSecret' =>  $config['client_secret'],
                    'RedirectURI' => $config['oauth_redirect_uri'],
                    'baseUrl' => "Production",
                    'refreshTokenKey' => $accessToken->getRefreshToken(),
                    'QBORealmID' => $accessToken->getRealmId(),
                ));
                $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                $accessToken = $OAuth2LoginHelper->refreshToken();
                $dataService->updateOAuth2Token($accessToken);
                session(['sessionAccessToken' => $accessToken]);
            }

            

            $dataService = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $config['client_id'],
                'ClientSecret' => $config['client_secret'],
                'accessTokenKey' => $accessToken->getAccessToken(),
                'refreshTokenKey' => $accessToken->getRefreshToken(),
                'QBORealmID' => $accessToken->getRealmId(),
                'baseUrl' => "Production"
            ));  
            $dataService->updateOAuth2Token($accessToken);
            $dataService->throwExceptionOnError(true);

            $customer = CustomerModel::find($request->customer_id);

            $lines = array();

            foreach($request->ticket_id as $index_ticket_id => $idTicket){
                $ticket = TicketsModel::find($idTicket);
                $aux = [
                    "DetailType" => "SalesItemLineDetail",
                    "Description" => $ticket->number,
                    "Amount" => round($ticket->rate * $ticket->tonage, 2),
                    "SalesItemLineDetail" => [
                        "ServiceDate" => $ticket->date_gen,
                        "UnitPrice" => $ticket->rate,
                        "Qty" => $ticket->tonage,
                        "ItemRef" => [
                            "value" => $request->item[$index_ticket_id]
                        ]
                    ]                    

                ];
                array_push($lines, $aux);
            }
            $tickets = TicketsModel::whereIn("id",$request->ticket_id)->get();
            $surcharges = [];
            foreach($tickets as $ticket){
                if($ticket->surcharge > 0){
                    $fsc = FscModel::select("surcharge.*")
                    ->where("from","<=",$ticket->date_gen)
                    ->where("to",">=",$ticket->date_gen)
                    ->where("fk_customer","=",$ticket->fk_customer)
                    ->first();

                    if(isset($fsc)){
                        $percentaje = $fsc->percentaje;
                    }
                    else{
                        $percentaje = "-";
                    }
                    $surcharges[$percentaje] = (isset($surcharges[$percentaje]) ? ($surcharges[$percentaje] + $ticket->surcharge) : $ticket->surcharge);
                }
            }
            foreach ($surcharges as $percentaje => $surcharge){
                $aux = [
                    "DetailType" => "SalesItemLineDetail",
                    "Description" => "FUEL SURCHARGE- ".$percentaje."%",
                    "Amount" => round($surcharge, 2),
                    "SalesItemLineDetail" => [
                        "UnitPrice" => round($surcharge, 2),
                        "Qty" => 1,
                        "ItemRef" => [
                            "value" => 147
                        ]
                    ]                    

                ];
                array_push($lines, $aux);
            }

            $invoiceObjArr = [
                "DocNumber" => $request->number,
                "DueDate" => $request->due_date,
                "TxnDate" => $request->invoice_date, 
                "Line" => $lines,
                "CustomerRef"=> [
                    "value"=> $customer->id_quickbooks
                ],
                "CustomField" => [
                    [
                        "DefinitionId" => "1",
                        "Name" => "PO#",
                        "Type" => "StringType",
                        "StringValue" => $request->po_code                    
                    ],
                    [
                        "DefinitionId" => "2",
                        "Name" => "PICKUP LOCATION",
                        "Type" => "StringType",
                        "StringValue" => $request->pickup        
                    ],
                    [
                        "DefinitionId" => "3",
                        "Name" =>  "DROP OFF LOCATION",
                        "Type" => "StringType",
                        "StringValue" => $request->deliver        
                    ]
                ]
            ];
            
            $invoiceObj = Invoice::create($invoiceObjArr);
            $resultingInvoiceObj = $dataService->Add($invoiceObj);
            $invoiceId = $resultingInvoiceObj->Id;   // This needs to be passed in the Payment creation later
            $ticket = TicketsModel::whereIn("id",$request->ticket_id)->update(["isInvoiced" => "1"]);
            return array(
                "success" => true,
                "message" => "Invoice created in quickbooks, with id: ".$invoiceId,
                "invoice_id" => $invoiceId
            );
            
        }
        else{
            return array(
                "success" => false,
                "message" => "Quickbooks session has expired"
            );
        }
    }
}
