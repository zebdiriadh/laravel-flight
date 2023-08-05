@extends('layouts.app')

@section('content')
<ul class="nav nav-tabs" id="flightTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="one-way-tab" data-bs-toggle="tab" href="#one-way" role="tab" aria-controls="one-way" aria-selected="true">One-way</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="round-trip-tab" data-bs-toggle="tab" href="#round-trip" role="tab" aria-controls="round-trip" aria-selected="false">Round-trip</a>
    </li>
</ul>
<div class="tab-content" id="flightTabContent">
    <div class="tab-pane fade show active" id="one-way" role="tabpanel" aria-labelledby="one-way-tab">
        <form action="{{ route('search') }}" method="GET">
            <div class="form-group">
                <label for="origin">Origin:</label>
                <input type="text" name="origin" id="origin" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="destination">Destination:</label>
                <input type="text" name="destination" id="destination" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="date">Time:</label>
                <input type="time" name="departure_time" id="departure_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="tab-pane fade" id="round-trip" role="tabpanel" aria-labelledby="round-trip-tab">
        <form action="{{ route('search') }}" method="GET">
            <div class="form-group">
                <label for="origin">Origin:</label>
                <input type="text" name="origin" id="origin" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="destination">Destination:</label>
                <input type="text" name="destination" id="destination" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="departure_time">Departure Time:</label>
                <input type="time" name="departure_time" id="departure_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="return_time">Return Time:</label>
                <input type="time" name="return_time" id="return_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>



    <div class="container">
        <h2>Flight Search Results</h2>

        @if ($allFlights->count() > 0)
            @foreach ($allFlights as $flight)

                @if (isset($flight['inbound']))
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Outbound</h4>
                            <p>Airline: ({{ $flight['outbound']['airline'] }})</p>
                            <p>Flight Number: {{ $flight['outbound']['number'] }}</p>
                            <p>Departure Airport: ({{ $flight['outbound']['departure_airport'] }})</p>
                            <p>Departure Time: {{ $flight['outbound']['departure_time'] }}</p>
                            <p>Arrival Airport: ({{ $flight['outbound']['arrival_airport'] }})</p>
                            <p>Arrival Time: {{ $flight['outbound']['arrival_time'] }}</p>
                            <p>Duration: {{ $flight['outbound']['duration'] }} minutes</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Inbound</h4>
                            <p>Airline: ({{ $flight['inbound']['airline'] }})</p>
                            <p>Flight Number: {{ $flight['inbound']['number'] }}</p>
                            <p>Departure Airport: ({{ $flight['inbound']['arrival_airport'] }})</p>
                            <p>Departure Time: {{ $flight['inbound']['departure_time'] }}</p>
                            <p>Arrival Airport: ({{ $flight['inbound']['departure_airport'] }})</p>
                            <p>Arrival Time: {{ $flight['inbound']['arrival_time'] }}</p>
                            <p>Duration: {{ $flight['inbound']['duration'] }} minutes</p>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <h4>One-way Trip</h4>
                            <p>Airline: ({{ $flight['airline'] }})</p>
                            <p>Flight Number: {{ $flight['number'] }}</p>
                            <p>Departure Airport: ({{ $flight['departure_airport'] }})</p>
                            <p>Departure Time: {{ $flight['departure_time'] }}</p>
                            <p>Arrival Airport: ({{ $flight['arrival_airport'] }})</p>
                            <p>Arrival Time: {{ $flight['arrival_time'] }}</p>
                            <p>Duration: {{ $flight['duration'] }} minutes</p>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <p>No flights found.</p>
        @endif
    </div>




    <!-- Your flight search form goes here -->
    <!-- The tabs and form fields -->
@endsection