<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Scrittura accesso al sito
           $accessdate=date("d-m-Y");
           $accesstime=date("h:i:sa");
           $accessip=$_SERVER['REMOTE_ADDR'];
           $scrivifile="Accesso giorno: ".$accessdate." Alle ore: ".$accesstime." Indirizzo ip: ".$accessip;
           $file = fopen("access.txt","a+");
           fputs($file,$scrivifile."\n");
           fclose($file);
           //fine scrittura file access.txt

        $business = Auth::user()->business;
        $profile = Auth::user()->profile;
        $admin = Auth::user()->admin;
        if ($profile=='1'){
            if($business=='0') {
                return redirect()->route('geocode');
            }
            else {
                if ($admin=='0') {
                    return redirect()->route('employee');
                }
                else {
                    return redirect()->route('admin');
                }
            }
        } else {
            if ($business=='0') {
                return redirect()->route('completeuser');
            }
            else {
                if ($admin=='0') return redirect()->route('employee');
                else return redirect()->route('completebusiness');
            }

        }
    }
}
