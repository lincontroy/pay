<?php

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Models\Usermeta;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Withdrawal;
use Mail;
use App\Mail\WithdrawOtp;
use App\Models\User;


class WithdrawalControler extends Controller
{
    
    public function index(){
        
        return view('merchant.withdraw.withdraw');
    }
    
    public function withdrawals(){
        
        $withdrawls=Withdrawal::where('user_id',Auth::user()->id)
        ->orderBy('id','DESC')
        ->get();
        
        
        return view('merchant.withdraw.withdrawals')->with(compact('withdrawls'));
        
        
        
    }
    
    public function confirm(Request $request){
        $refs=$request->ref;
        
        return view('merchant.withdraw.confirm')->with(compact('refs'));
        
        
    }
    
    public function confirmpost(Request $request){
        
        $ref=$request->ref;
        //mark the withdrawal as confirmed
        
        $checks=Withdrawal::where('reference',$ref)
        ->where('status','!=',1)
        ->get();
        
        if(count($checks)>0){
        foreach($checks as $check){
            $user_id=$check->user_id;
            
            $reference=$check->reference;
            
            if(((Auth::user()->id)==$user_id) && ($reference==$ref)){
                //process the confirmation
                $update=Withdrawal::where('reference',$ref)->update(['status'=>1]);
                
                return redirect()->to('/merchant/withdrawals')->with('success', 'Withdrawal Processing');   
            }else{
                
                
                return redirect()->back()->with('error', 'You are not Authorized to do this action');   
            }
        }
        
        }else{
            return redirect()->back()->with('error', 'An error occured'); 
        }
        
        
    }
    
    public function post(Request $request){
        
        
        
        function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        
        function generateRandomcode($length = 10) {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        $withdrawal=new Withdrawal();
        
        $users=User::where('id',Auth::user()->id)->get();
        
        foreach($users as $user){
            
            
            //check the user balance
            
            $balance=$user->balance;
            if($balance>$request->amount){
                
            $amt=(($request->amount)-($request->amount*0.01));
            
            $ref=generateRandomString($length = 10);
            $code=generateRandomcode($length = 5);
            
            
            $withdrawal->user_id=Auth::user()->id;
            
            $withdrawal->amount=$amt;
            $withdrawal->fee=($request->amount*(1/100));
            $withdrawal->bankname=$request->bankname;
            $withdrawal->bankaccount=$request->bankaccount;
            $withdrawal->reference=$ref;
            $withdrawal->code=$code;
            //$withdrawal->bankaccount=$request->bankaccount;
            
           
            
            $Newbalance=$balance-($request->amount+$amt);
            $update=User::where('id',Auth::user()->id)->update(['balance'=>$Newbalance]);
            
            
              if(($withdrawal->save()) && ($update)){
            
            
               Mail::to(Auth::user()->email)->send(new WithdrawOtp($code,$ref,$amt));
               
               return redirect()->to('https://pay.lifegeegs.com/merchant/confirm/'.$ref)->with('success', 'Please check your email for further instructions');   
                
             }
        
                }else
                
                {
                    
                   return redirect()->back()->with('error', 'insufiicient balance');  
                    
                    
                }
        
        }
 
    }
    
    
  
}
