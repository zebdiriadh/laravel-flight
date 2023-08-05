<?php

namespace App\Http\Controllers;

use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $jsonData = File::get(public_path('/Files/listFlights.json'));
        $flightList = json_decode($jsonData, true);
        $airports = collect($flightList['airports']);



        // Extract the necessary arrays from the data
        $flights = collect($flightList['flights']);
        $airlines = collect($flightList['airlines']);
        $airports = collect($flightList['airports']);

        // Get the user input from the search form
        $originAirportCode = $request->input('origin');
        $destinationAirportCode = $request->input('destination');
        $departureTime = strtotime($request->input('departure_time'));
        $returnTime = strtotime($request->input('return_time'));

        // Calculate the arrival_time for one-way flights and update the $flights collection
        $flights = $flights->map(function ($flight) use ( $airports) {

            // Get the departure airport timezone
            $departureAirport = $airports->where('code', $flight['departure_airport'])->first();
            $departureTimezone = $departureAirport['timezone'];

            // Calculate the arrival_time based on the departure_time and duration for one-way flights
            if (!isset($flight['return_time'])) {
                $flightDurationMinutes = (int) $flight['duration'];
               // var_dump('flightDurationMinutes :'.$flightDurationMinutes .'<br>');
                $flightDurationSeconds = $flightDurationMinutes * 60;
               
                

                // Convert departure time to the timezone of the departure airport
                $departureAirport = $airports->where('code', $flight['departure_airport'])->first();

                $departureTimezone = $departureAirport['timezone'];

                // Convert arrival time to the timezone of the destination airport
                $arrivalAirport = $airports->where('code', $flight['arrival_airport'])->first();
                $arrivalTimezone = $arrivalAirport['timezone']; 
              
                $departure_time = $flight['departure_time']; // Replace this with the current time in Montreal in 'YYYY-MM-DD H:i:s' format
                

                // Create DateTimeZone objects for both time zones
                $source_timezone = new DateTimeZone($departureTimezone);
                $target_timezone = new DateTimeZone($arrivalTimezone);

                // Get the current date
                $date = new DateTime('now');

                // Create a DateTime object for the source time
                $source_time = DateTime::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $departure_time, $source_timezone);

                // Calculate the time difference between the source and target time zones
                $time_difference = $target_timezone->getOffset($date) - $source_timezone->getOffset($date);

                // Create a DateInterval based on the time difference
                $time_interval = DateInterval::createFromDateString("$time_difference seconds");

                // Calculate the time in the target timezone
                $target_time = $source_time->add($time_interval);

                // Format the DateTime object to display the time
                $target_time_str = $target_time->format('H:i');
                $target_time->add(new DateInterval('PT'.$flightDurationSeconds.'S'));
                $target_time_str = $target_time->format('H:i');

                
                $flight['arrival_time'] = $target_time_str; // Set the calculated arrival_time


            }

            return $flight;
        });

       // Filter flights based on the user input for origin and destination airports
        $filteredFlights = $flights->filter(function ($flight) use ($originAirportCode, $destinationAirportCode) {
            return $flight['departure_airport'] === $originAirportCode && $flight['arrival_airport'] === $destinationAirportCode;
        });

        

        // Convert the duration of all flights to hours instead of minutes
        $allFlights = $filteredFlights->map(function ($flight) {
            $flight['duration'] = gmdate('H:i', $flight['duration'] * 60);
            return $flight;
        });

        // Pass the merged flights, airlines, and airports data to the view
        return view('search', compact('allFlights', 'airlines', 'airports'));
    }
}
