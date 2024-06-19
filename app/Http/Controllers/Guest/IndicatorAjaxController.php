<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Indicator;

class IndicatorAjaxController extends Controller
{
    public function show(Indicator $indicator)
    {
        $indicator = Indicator::find(43);
        $response = ['data' => $indicator->data, 'layout' => $indicator->layout];
        $response['data'][0]['x'] = [
            "Banjul",
            "Basse",
            "Brikama",
            "Janjanbureh",
            "Kanifing",
            "Kerewan",
            "Kuntaur",
            "Mansakonko",
            "All Lgas"
        ];
        $response['data'][0]['y'] = [
            4.46,
            11.507897934386392,
            7.701668806161746,
            11.393939393939394,
            5.793918918918919,
            9.041284403669724,
            9.764705882352942,
            8.830508474576272,
            8.561740476750675
        ];
        return $response;
    }
}
