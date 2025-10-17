<?php

namespace App\Interfaces\services;

use Illuminate\Http\Request;

interface iapifilterService
{
    public function transform(Request $request);
}
