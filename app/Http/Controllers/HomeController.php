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

        $business = Auth::user()->business;
        $profile = Auth::user()->profile;
        if ($profile=='1'){
            if($business=='0') {
                return redirect()->route('geocode');
            }
            else {
                $admin = Auth::user()->admin;
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
