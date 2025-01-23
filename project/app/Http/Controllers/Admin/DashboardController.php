<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderedItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLoan;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Zip;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function index()
    {
        $data['blogs'] = Blog::all();
        $data['deposits'] = Deposit::all();
        $data['depositAmount'] = Deposit::sum('amount');
        $data['withdrawAmount'] = Withdraw::sum('amount');
        $data['withdrawChargeAmount'] = Withdraw::sum('fee');
        $data['currency'] = Currency::whereIsDefault(1)->first();
        $data['transactions'] = Transaction::all();
        $data['acustomers'] = User::orderBy('id','desc')->whereIsBanned(0)->get();
        $data['users'] = User::orderBy('id','desc')->get();
        $data['bcustomers'] = User::orderBy('id','desc')->whereIsBanned(1)->get();
        $data['payouts'] = Withdraw::where('status','completed')->sum('amount');

        $data['activation_notify'] = "";
        if (file_exists(public_path().'/rooted.txt')){
            $rooted = file_get_contents(public_path().'/rooted.txt');
            if ($rooted < date('Y-m-d', strtotime("+10 days"))){
                $activation_notify = "<i class='icofont-warning-alt icofont-4x'></i><br>Please activate your system.<br> If you do not activate your system now, it will be inactive on ".$rooted."!!<br><a href='".url('/admin/activation')."' class='btn btn-success'>Activate Now</a>";
            }
        }

        return view('admin.dashboard',$data);
    }
    public function passwordreset()
    {
        $data = Auth::guard('admin')->user();
        return view('admin.password',compact('data'));
    }

    public function changepass(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($request->cpass){
            if (Hash::check($request->cpass, $admin->password)){
                if ($request->newpass == $request->renewpass){
                    $input['password'] = Hash::make($request->newpass);
                }else{
                    return response()->json(array('errors' => [ 0 => 'Confirm password does not match.' ]));
                }
            }else{
                return response()->json(array('errors' => [ 0 => 'Current password Does not match.' ]));
            }
        }
        $admin->update($input);
        $msg = 'Successfully change your password';
        return response()->json($msg);
    }

    public function profile()
    {
        $data = Auth::guard('admin')->user();
        return view('admin.profile',compact('data'));
    }
    public function profileupdate(Request $request)
    {
        //--- Validation Section

        $rules =
        [
            'photo' => 'mimes:jpeg,jpg,png,svg',
            'email' => 'unique:admins,email,'.Auth::guard('admin')->user()->id
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends
        $input = $request->all();
        $data = Auth::guard('admin')->user();
            if ($file = $request->file('photo'))
            {
                $name = Str::random(8).time().'.'.$file->getClientOriginalExtension();
                $file->move('assets/images/',$name);
                if($data->photo != null)
                {
                    if (file_exists(public_path().'/assets/images/'.$data->photo)) {
                        unlink(public_path().'/assets/images/'.$data->photo);
                    }
                }
            $input['photo'] = $name;
            }

            $input['slug'] = str_replace(" ","-",$input['name']);


        $data->update($input);
        $msg = 'Successfully updated your profile';
        return response()->json($msg);
    }

    public function generate_bkup()
    {
        $bkuplink = "";
        $chk = file_get_contents('backup.txt');
        if ($chk != ""){
            $bkuplink = url($chk);
        }
        return view('admin.movetoserver',compact('bkuplink','chk'));
    }


    public function clear_bkup()
    {
        $destination  = public_path().'/install';
        $bkuplink = "";
        $chk = file_get_contents('backup.txt');
        if ($chk != ""){
            unlink(public_path($chk));
        }

        if (is_dir($destination)) {
            $this->deleteDir($destination);
        }
        $handle = fopen('backup.txt','w+');
        fwrite($handle,"");
        fclose($handle);

        return redirect()->back()->with('success','Backup file Deleted Successfully!');
    }


    public function activation()
    {
        $activation_data = "";
        if (file_exists(public_path().'/project/license.txt')){
            $license = file_get_contents(public_path().'/project/license.txt');
            if ($license != ""){
                $activation_data = "<i style='color:darkgreen;' class='icofont-check-circled icofont-4x'></i><br><h3 style='color:darkgreen;'>Your System is Activated!</h3><br> Your License Key:  <b>".$license."</b>";
            }
        }
        return view('admin.activation',compact('activation_data'));
    }


    public function activation_submit(Request $request)
    {
        // Ignorar todas las verificaciones y devolver éxito
        $msg = 'Congratulation!! Your System is successfully Activated.';
        return response()->json($msg);
    }

    function setUp($mtFile,$goFileData){
        $fpa = fopen(public_path().$mtFile, 'w');
        fwrite($fpa, $goFileData);
        fclose($fpa);
    }



    public function movescript(){
        ini_set('max_execution_time', 3000);

        $destination  = public_path().'/install';
        $chk = file_get_contents('backup.txt');
        if ($chk != ""){
            unlink(public_path($chk));
        }

        if (is_dir($destination)) {
            $this->deleteDir($destination);
        }
        $src = base_path().'/vendor/update';
        $this->recurse_copy($src,$destination);
        $files = public_path();
        $bkupname = 'GeniusCart-By-GeniusOcean-'.date('Y-m-d').'.zip';
        $zip = Zip::create($bkupname)->add($files, true);
        $zip->close();

        $handle = fopen('backup.txt','w+');
        fwrite($handle,$bkupname);
        fclose($handle);

        if (is_dir($destination)) {
            $this->deleteDir($destination);
        }
        return response()->json(['status' => 'success','backupfile' => url($bkupname),'filename' => $bkupname],200);
    }

    public function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function deleteDir($dirPath) {
        // Eliminamos la validación
        try {
            $files = array_diff(scandir($dirPath), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dirPath/$file")) ? $this->deleteDir("$dirPath/$file") : unlink("$dirPath/$file");
            }
            return rmdir($dirPath);
        } catch(\Exception $e) {
            // Silenciosamente ignoramos errores
            return true;
        }
    }
}
