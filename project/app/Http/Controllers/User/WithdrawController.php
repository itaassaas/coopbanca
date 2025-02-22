<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankPlan;
use Auth;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\WithdrawMethod;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Support\Facades\Storage;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

  	public function index()
    {
        $withdraws = Withdraw::whereUserId(auth()->id())->orderBy('id','desc')->paginate(10);     
        return view('user.withdraw.index',compact('withdraws'));
    }

    public function create()
    {
        $data['sign'] = Currency::whereIsDefault(1)->first();
        $data['methods'] = WithdrawMethod::whereStatus(1)->orderBy('id','desc')->get();
        return view('user.withdraw.create' ,$data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|gt:0',
            'comporbante' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create storage directory if it doesn't exist
        if (!Storage::disk('public')->exists('withdraws')) {
            Storage::disk('public')->makeDirectory('withdraws');
        }

        $user = auth()->user();

        if($user->bank_plan_id === null){
            return redirect()->back()->with('unsuccess','You have to buy a plan to withdraw.');
        }

        if(now()->gt($user->plan_end_date)){
            return redirect()->back()->with('unsuccess','Plan Date Expired.');
        }

        $bank_plan = BankPlan::whereId($user->bank_plan_id)->first();
        $dailyWithdraws = Withdraw::whereDate('created_at', '=', date('Y-m-d'))->whereStatus('completed')->sum('amount');
        $monthlyWithdraws = Withdraw::whereMonth('created_at', '=', date('m'))->whereStatus('completed')->sum('amount');

        if($dailyWithdraws > $bank_plan->daily_withdraw){
            return redirect()->back()->with('unsuccess','Daily withdraw limit over.');
        }

        if($monthlyWithdraws > $bank_plan->monthly_withdraw){
            return redirect()->back()->with('unsuccess','Monthly withdraw limit over.');
        }
        
        if($request->amount > $user->balance){
            return redirect()->back()->with('unsuccess','Insufficient Account Balance.');
        }

        $withdrawcharge = WithdrawMethod::whereMethod($request->methods)->first();
        $charge = $withdrawcharge->fixed;

        $messagefee = (($withdrawcharge->percentage / 100) * $request->amount) + $charge;
        $messagefinal = $request->amount - $messagefee;

        $currency = Currency::whereId($request->currency_id)->first();
        $amountToAdd = $request->amount/$currency->value;

        $amount = $amountToAdd;
        $fee = (($withdrawcharge->percentage / 100) * $amount) + $charge;
        $finalamount = $amount - $fee;
        
        if($finalamount < 0){
            return redirect()->back()->with('unsuccess','Request Amount should be greater than this '.$amountToAdd.' (USD)');
        }

        if($finalamount > $user->balance){
            return redirect()->back()->with('unsuccess','Insufficient Balance.');
        }



        $finalamount = number_format((float)$finalamount,2,'.','');

        $user->balance = $user->balance - $amount;
        $user->update();

        $txnid = Str::random(12);


        $newwithdraw = new Withdraw();



        if ($request->hasFile('comporbante')) {
            try {
                $image = $request->file('comporbante');
                $fileName = time() . '_' . $txnid . '.' . $image->getClientOriginalExtension();
                
                // Asegurar que el directorio existe
                $path = public_path('assets/images');
                if (!file_exists($path)) {
                    mkdir($path, 0775, true);
                }
                
                // Verificar permisos del directorio
                if (!is_writable($path)) {
                    throw new \Exception('Directory is not writable');
                }
                
                // Mover el archivo con verificación
                if (!$image->move($path, $fileName)) {
                    throw new \Exception('Failed to move uploaded file');
                }
                
                // Verificar que el archivo existe después de moverlo
                if (!file_exists($path . '/' . $fileName)) {
                    throw new \Exception('File was not saved correctly');
                }
                
                $newwithdraw->comporbante = 'assets/images/' . $fileName;
                
            } catch (\Exception $e) {
                \Log::error('Error uploading file: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Error al cargar la imagen: ' . $e->getMessage());
            }
        }

        $newwithdraw['user_id'] = auth()->id();
        $newwithdraw['method'] = $request->methods;
        $newwithdraw['txnid'] = $txnid;

        $newwithdraw['amount'] = $finalamount;
        $newwithdraw['fee'] = $fee;
        $newwithdraw['details'] = $request->details;
        $newwithdraw->save();

        $total_amount = $newwithdraw->amount + $newwithdraw->fee;

        $trans = new Transaction();
        $trans->email = $user->email;
        $trans->amount = $finalamount;
        $trans->type = "Payout";
        $trans->profit = "minus";
        $trans->txnid = $txnid;
        $trans->user_id = $user->id;
        $trans->save();

        return redirect()->back()->with('success','Withdraw Request Amount : '.$request->amount.' Fee : '.$messagefee.' = '.$messagefinal.' ('.$currency->name.') Sent Successfully.');

    }

    public function details(Request $request, $id){
        $data['data'] = Withdraw::findOrFail($id);
        $data['currency'] = Currency::whereIsDefault(1)->first();
        
        return view('user.withdraw.details',$data);
    }
}
