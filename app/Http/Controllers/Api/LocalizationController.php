<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LocalizationController extends Controller
{
    public function getStrings($locale){
        $json = base_path('resources/lang/' . $locale . '.json');

        if (file_exists($json)) {
            $json = json_decode(file_get_contents($json), true);
        }else{
            $json = [];
        }

        return $this->successResponse([
            'data' => $json,
        ]);
    }
}
