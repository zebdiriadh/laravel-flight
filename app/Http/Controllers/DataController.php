<?php

namespace App\Http\Controllers;


class DataController extends Controller
{
    public function index()
    {
        $jsonFilePath = public_path('/Files/listFlights.json');

        if (file_exists($jsonFilePath)) {
            $jsonData = file_get_contents($jsonFilePath);
            $data = json_decode($jsonData, true);
            return response()->json($data);
        } else {
            return response()->json(['error' => 'JSON file not found'], 404);
        }
    }
}
