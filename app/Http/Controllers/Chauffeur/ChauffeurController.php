<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    public function index()
    {
        return view('chauffeur.dashboard');
    }
}