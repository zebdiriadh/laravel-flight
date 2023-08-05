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




<!-- Display search results or random results -->
<!-- Display search results or random results -->
<div class="mt-4">
    @if ($allFlights->isNotEmpty())
        <h2>Flight Results:</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Airline</th>
                        <th>Flight Number</th>
                        <th>Price</th>
                        <th>Departure Airport</th>
                        <th>Departure Time</th>
                        <th>Arrival Airport</th>
                        <th>Arrival Time</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allFlights as $flight)
                        <tr>
                            <td>{{ $flight['airline'] }}</td>
                            <td>{{ $flight['number'] }}</td>
                            <td>{{ $flight['price'] }}</td>
                            <td>{{ $airports->where('code', $flight['departure_airport'])->first()['name'] }}</td>
                            <td>{{ $flight['departure_time'] }}</td>
                            <td>{{ $airports->where('code', $flight['arrival_airport'])->first()['name'] }}</td>
                            @if (isset($flight['return_time']))
                            <td>
                                Depart: {{ $flight['departure_time'] }}<br>
                                Destination Arrival: {{ isset($flight['destination_arrival_time']) ? $flight['destination_arrival_time'] : 'N/A' }}<br>
                                Return: {{ $flight['return_time'] }}<br>
                                Return Arrival: {{ isset($flight['return_arrival_time']) ? $flight['return_arrival_time'] : 'N/A' }}
                            </td>
                            @else
                                <td>
                                    {{ isset($flight['arrival_time']) ? $flight['arrival_time'] : 'N/A' }}
                                </td>
                            @endif
                            <td>{{ $flight['duration'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No flights available for the selected departure time.</p>
    @endif
</div>
</div>


    <!-- Your flight search form goes here -->
    <!-- The tabs and form fields -->
@endsection