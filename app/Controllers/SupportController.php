<?php

namespace App\Controllers;

class SupportController extends BaseController
{
    public function index()
    {
        return view('support', [
            'title' => 'Support',
            'noContainer' => true,
        ]);
    }
}

