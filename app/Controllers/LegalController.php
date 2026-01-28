<?php

namespace App\Controllers;

class LegalController extends BaseController
{
    public function privacy()
    {
        return view('legal/privacy', [
            'title' => 'Privacy Policy - Threadline',
        ]);
    }

    public function terms()
    {
        return view('legal/terms', [
            'title' => 'Terms of Service - Threadline',
        ]);
    }

    public function guidelines()
    {
        return view('legal/guidelines', [
            'title' => 'Community Guidelines - Threadline',
        ]);
    }
}
