<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Basket;
use App\PayPalTransaction;
use DB;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

use PayPal\Api\PaymentExecution;

class CheckoutController extends Controller
{

    private $apiContext;
    private $client_id;
    private $secret;

	public function __construct()
    {
        $this->middleware('auth', ['except'=>['createPayment', 'executePayment']]);

        // Detect if we are running in live mode or sandbox
        if(config('paypal.settings.mode') == 'live'){
            $this->client_id = config('paypal.live_client_id');
            $this->secret = config('paypal.live_secret');
        } else {
            $this->client_id = config('paypal.sandbox_client_id');
            $this->secret = config('paypal.sandbox_secret');
        }

        // Set the Paypal API Context/Credentials
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    function checkout(){
    	$title = "Checkout";
        $basket = Basket::where('user_id',auth()->user()->id)->get();
        $results = array();
        foreach($basket as $b){
            $results[] = $b->product_id;
        }
    	return view('checkout')
                                ->with("title", $title)
                                ->with('items',Product::find($results))
                                ->with("basket", $basket);
    }

    function removeItem(Request $request, $id){
    	$validator = \Validator::make(array_merge(
            [
              'id'=>$id
            ], 
            $request->all()
        ), [
            "id" => 'required|numeric|exists:products,id'
        ])->validate();

		$basket = Basket::where('user_id',auth()->user()->id)->get();
        $removed = false;
        $newBasket = array();
        foreach($basket as $b){
            if($b->product_id != $id || $removed==true)
                $newBasket[]=array('user_id'=>auth()->user()->id,'product_id'=>$b->product_id);
            else 
                $removed = true;
        }
        Basket::where('user_id',auth()->user()->id)->delete();

        DB::table('basket')->insert($newBasket);

    	return redirect("checkout");
    }

    public function createPayment (Request $request) {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $shipping = 1.2;
        $tax = 1.3;
        $itemList = $this->getItemsListFromDatabase($request);
        $subTotal = $this->getSubTotal($itemList);
        
        $details = new Details();
        $details->setShipping($shipping)
            ->setTax($tax)
            ->setSubtotal($subTotal);

        $total=$subTotal+$shipping+$tax;
        $amount = new Amount();
        $amount->setCurrency("USD")
        ->setTotal($total)
        ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription("Payment description")
        ->setInvoiceNumber(uniqid());

        $baseUrl = \URL::to('/');
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://online-webstore/paypalRedirect?success=true")
        ->setCancelUrl("http://online-webstore/paypalRedirect?success=false");

        $payment = new Payment();
        $payment->setIntent("sale")
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));

        try {
        $payment->create($this->apiContext);
        } catch (Exception $ex) {
            exit(1);
        }

        return $payment;
    }

    public function executePayment (Request $request) {
    	
        $paymentId = $request->paymentID;
        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->payerID);
        
        try {
            $result = $payment->execute($execution, $this->apiContext);
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            die($ex);
        }
        
        return $result;
    }

    private function getItemsListFromDatabase(Request $request){
        $itemList = new ItemList();
        $temp = array();
        $res = DB::table('basket')
                    ->select(DB::raw('products.id, products.name, products.price, count(products.id) as quantity'))
                    ->join('products','products.id','=','basket.product_id')
                    ->where('basket.user_id','=',$request->userId)
                    ->groupBy('basket.product_id')
                    ->get();
            
        foreach($res as $r) {
            $purchase = new Item();
            $purchase->setName($r->name)
            ->setCurrency('USD')
            ->setQuantity($r->quantity)
            ->setSku($r->id) // Similar to `item_number` in Classic API
            ->setPrice($r->price);
            $temp[]=$purchase;
        }

        $itemList->setItems($temp);

        return $itemList;
    }

    private function getSubTotal(ItemList $itemList){
        $subTotal = 0;
        foreach($itemList->getItems() as $item){
            $subTotal+=($item->getPrice()*$item->getQuantity());
        }
        return $subTotal;
    }

    
    public function paypalRedirect(Request $request){
        if ( $request->has('success') &&
            $request->input('success') == true && 
            $request->has('paymentId')){
            
            $paypalTransaction = new PayPalTransaction;

            $paypalTransaction->user_id = auth()->user()->id;
            $paypalTransaction->payment_id = $request->input('paymentId');
            $paypalTransaction->status = 'success';

            $paypalTransaction->save();

            $allItems = Basket::where('user_id', auth()->user()->id)->orderBy('product_id')->get();
            foreach($allItems as $item) {
                $res = Product::find($item->product_id);
                $res->quantity = $res->quantity - 1; 
                $res->save();
            }

            Basket::where('user_id', auth()->user()->id)->delete();

            return redirect("/")->with('success', 'Purchase Complete');
        } else {
            return redirect("/")->with('error', 'Purchase Failed');
        }
    }

}
