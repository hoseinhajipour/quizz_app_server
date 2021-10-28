<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use http\Exception;
use Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use nusoap_client;

class PaymentController extends Controller
{
    public function packages()
    {
        $packages = Package::all();
        return ["status" => "ok", "packages" => $packages];
    }

    public function buy(Request $request)
    {


        $package_id = $request->input('package_id');
        $Package = Package::where('id', $package_id)->first();
        $user = auth()->user();
        $MerchantID = setting('payment.zarinpal_merchent_code');
        $zarinpalSandbox = boolval(setting('payment.zarinpal_merchent_code_sandbox'));
        if ($zarinpalSandbox) {
            $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        } else {
            $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        }

        $client->soap_defencoding = 'UTF-8';

        $newPay = Payment::create();
        $newPay->user_id = $user->id;
        $newPay->package_id = $Package->id;
        $newPay->amount = $Package->price;
        $newPay->status = "pending";
        $newPay->save();
        $CallbackURL = url('/order?payment_id=' . $newPay->id); // Required


        $result = $client->call('PaymentRequest', [
            [
                'MerchantID' => $MerchantID,
                'Amount' => $Package->price,
                'Description' => $Package->description,
                'Email' => $user->email,
                'Mobile' => $user->mobile,
                'CallbackURL' => $CallbackURL,
            ],
        ]);

        //Redirect to URL You can do it also by creating a form
        if ($result['Status'] == 100) {
            $newPay->authority = $result['Authority'];
            $newPay->save();
            if ($zarinpalSandbox) {
                $result['url'] = 'https://sandbox.zarinpal.com/pg/StartPay/' . $result['Authority'];
            } else {
                $result['url'] = 'https://www.zarinpal.com/pg/StartPay/' . $result['Authority'];
            }

            return $result;
        } else {
            return false;
        }

    }

    public function verify(Request $request)
    {

        $Authority = $request->get('Authority');
        $payment_id = $request->get('payment_id');
        $Payment = Payment::where('id', $payment_id)->first();
        $MerchantID = setting('payment.zarinpal_merchent_code');
        $Amount = $Payment->price;
        if ($request->get('Status') == 'OK') {

            $zarinpalSandbox = boolval(setting('payment.zarinpal_merchent_code_sandbox'));
            if ($zarinpalSandbox) {
                $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
            } else {
                $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
            }

            $client->soap_defencoding = 'UTF-8';
            $result = $client->call('PaymentVerification', [
                [
                    'MerchantID' => $MerchantID,
                    'Authority' => $Authority,
                    'Amount' => $Amount,
                ],
            ]);
            if ($zarinpalSandbox) {
                $result['Status'] = 100;
            }
            if ($result['Status'] == 100) {

                $Payment->status = "complete";
                $Payment->refid = $result['RefID'];
                $Package = Package::where('id', $Payment->package_id)->first();
                $user = User::where('id', $Payment->user_id)->first();
                $user->coin += $Package->coin;
                $Payment->save();
                $user->save();
                $message = 'پرداخت با موفقیت انجام شد.';
            } else {
                $Payment->status = "error";
                $Payment->save();

                $message = 'خطا در انجام عملیات';
            }
        } else {
            $Payment->status = "canceled";
            $Payment->save();

            $message = 'سفارش لغو گردید.';
        }

        return view('order', [
            'message' => $message,
            'refid' => $Payment->refid,
            'Payment' => $Payment,
            'status' => $Payment->status]);

    }

}
