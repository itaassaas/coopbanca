<?php

namespace App\Http\Controllers\Subscription;

use App\Repositories\SubscriptionRepository;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StripeController extends Controller
{
    public $subscriptionRepositorty;

    public function __construct(SubscriptionRepository $subscriptionRepositorty)
    {
        $data = PaymentGateway::whereKeyword('Stripe')->first();
        $paydata = $data->convertAutoData();

        \Stripe\Stripe::setApiKey($paydata['secret']);
        $this->subscriptionRepositorty = $subscriptionRepositorty;
    }

    public function store(Request $request){
        $settings = Generalsetting::findOrFail(1);
        $item_name = $settings->title." Subscription";
        $gs = Generalsetting::findOrFail(1);
        
        $item_amount = $request->price;
        $item_number = Str::random(4).time();

        $support = ['USD'];
        if(!in_array($request->currency_code,$support)){
            return redirect()->back()->with('warning','Please Select USD Or EUR Currency For Paypal.');
        }

        
        Session::put('request',$request->all());
      

        $session = \Stripe\Checkout\Session::create([
            "line_items" => [
                [
                    "quantity" => 1,
                    "price_data" => [
                        "currency" => $request->currency_code,
                        "unit_amount" =>$item_amount*100,
                        "product_data" => [
                            "name" => $gs->title . 'Plan Subscription',
                        ]
                    ]
                ]
                ],
            'mode' => 'payment',
            "locale" => "auto",
            'success_url' => route('subscription.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('subscription.paypal.cancle', [], true),
          ]);
          return redirect($session->url);
           
    }

    public function success(Request $request)
    {
        $user= Auth::user();
        $gs= Generalsetting::first();
        
        $sessionId = $request->get('session_id');

        try{
           
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

        
            if (!$session) {
                throw new NotFoundHttpException;
            }

            $item_number = Str::random(4).time();
            $request = Session::get('request');

            if ($session->payment_status == 'paid'  && $session->status=='complete') {
                $addionalData = ['subscription_number'=>$item_number,'txnid'=>$session->payment_intent];
                $this->subscriptionRepositorty->order($request,'complete',$addionalData);

                return redirect()->route('user.dashboard')->with('message','Bank Plan Updated');
            }
            
        }catch (Exception $e){
            return back()->with('unsuccess', $e->getMessage());
        }

    }
}
