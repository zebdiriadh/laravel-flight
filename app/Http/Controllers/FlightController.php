<?php

namespace App\Http\Controllers;


use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;



class FlightController extends Controller
{
    public function search(Request $request)
    {
        try {

            $apiRequest = Request::create(url('/data/index'), 'GET');
            $response = app()->handle($apiRequest);

            // Get the JSON response body
            $jsonData = $response->getContent();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        // Decode the JSON data
        $data = json_decode($jsonData, true);

        $flightList = json_decode($jsonData, true);
        $airports = collect($flightList['airports']);

        // Extract the necessary arrays from the data
        $flights = collect($flightList['flights']);
        $airlines = collect($flightList['airlines']);
        $airports = collect($flightList['airports']);

        // Get the user input from the search form
        $originAirportCode = strtoupper($request->input('origin'));
        $destinationAirportCode = strtoupper($request->input('destination'));
        $departureTime = strtotime($request->input('departure_time'));
        $returnTime = strtotime($request->input('return_time'));

        // Calculate the arrival_time for one-way flights and update the $flights collection
        $flights = $flights->map(function ($flight) use ($airports) {

            // Get the departure airport timezone
            $departureAirport = $airports->where('code', $flight['departure_airport'])->first();
            $departureTimezone = $departureAirport['timezone'];
            $flightDurationMinutes = (int) $flight['duration'];
            $flightDurationSeconds = $flightDurationMinutes * 60;

            // Convert departure time to the timezone of the departure airport
            $departureAirport = $airports->where('code', $flight['departure_airport'])->first();
            $departureTimezone = $departureAirport['timezone'];


            // Convert arrival time to the timezone of the destination airport
            $arrivalAirport = $airports->where('code', $flight['arrival_airport'])->first();
            $arrivalTimezone = $arrivalAirport['timezone'];

            $departure_time = $flight['departure_time'];

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
            $target_time->add(new DateInterval('PT' . $flightDurationSeconds . 'S'));
            $target_time_str = $target_time->format('H:i');

            $flight['arrival_time'] = $target_time_str;


            $departureAirportCode = $flight['departure_airport'];
            $arrivalAirportCode = $flight['arrival_airport'];

            $departureAirport = collect($airports)->firstWhere('code', $departureAirportCode);
            $arrivalAirport = collect($airports)->firstWhere('code', $arrivalAirportCode);

            if ($departureAirport && $arrivalAirport) {
                $flight['distance'] = $this->calculateDistance(
                    $departureAirport['latitude'],
                    $departureAirport['longitude'],
                    $arrivalAirport['latitude'],
                    $arrivalAirport['longitude']
                );
            }
            return $flight;
        });


        $filteredFlights = $flights->filter(function ($flight) use ($originAirportCode, $destinationAirportCode) {

            return $flight['departure_airport'] === $originAirportCode && $flight['arrival_airport'] === $destinationAirportCode;
        });

        // Generate Inbound and outbound flights
        if ($returnTime) {
            $outboundFlights = $flights->filter(function ($flight) use ($originAirportCode, $destinationAirportCode) {
                return $flight['departure_airport'] === $originAirportCode && $flight['arrival_airport'] === $destinationAirportCode;
            });

            $inboundFlights = $flights->filter(function ($flight) use ($originAirportCode, $destinationAirportCode) {
                return $flight['departure_airport'] === $destinationAirportCode && $flight['arrival_airport'] === $originAirportCode;
            });

            $combinedFlights = $outboundFlights->flatMap(function ($outboundFlight) use ($inboundFlights) {
                return $inboundFlights->map(function ($inboundFlight) use ($outboundFlight) {
                    return [
                        'outbound' => $outboundFlight,
                        'inbound' => $inboundFlight,
                    ];
                });
            });
        }

        // Set the flights based on oneway-trip or round trip
        $allFlights = isset($combinedFlights) ? $combinedFlights : $filteredFlights;

        // Get the sorting parameter from the query string
        $sort = $request->query('sort');

        // Determine the column and direction for sorting
        $sortColumn = '';
        $sortDirection = 'asc';

        if ($sort) {
            $sortColumn = $sort;
            $sortDirection = 'asc';
            if (substr($sort, 0, 1) === '-') {
                $sortColumn = substr($sort, 1);
                $sortDirection = 'desc';
            }
        }

        // Sort the flights based on the chosen criteria
        if ($sortColumn && in_array($sortColumn, ['airline', 'flight_number', 'departure_airport', 'departure_time', 'arrival_airport', 'arrival_time', 'duration', 'price'])) {
            $allFlights = $allFlights->sortBy($sortColumn, SORT_REGULAR, $sortDirection === 'desc');
        }

        // Paginate the results with 5 flights per page
        $perPage = 5;
        $currentPage = $request->query('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Check if the search results are already cached
        $cacheKey =  $request->fullUrl();
        $paginatedFlights = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($allFlights, $offset, $perPage, $currentPage) {
            return new LengthAwarePaginator(
                $allFlights->slice($offset, $perPage),
                $allFlights->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        });

        // Pass the merged flights, airlines, and airports data to the view
        return view('search', compact('allFlights', 'airlines', 'airports', 'paginatedFlights'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2); // Return the distance rounded to 2 decimal places
    }
}
