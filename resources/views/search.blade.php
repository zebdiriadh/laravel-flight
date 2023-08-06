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
<div class="mt-4">
    @if ($paginatedFlights->isNotEmpty())
        <h2>Flight Results:</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Airline</th>
                        <th>Flight Number</th>
                        <th>Departure Airport</th>
                        <th>Departure Time</th>
                        <th>Arrival Airport</th>
                        <th>Arrival Time</th>
                        <th>Duration</th>
                        <th>Distance (km)</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginatedFlights as $flight)
                        @if (isset($flight['inbound']))
                            <tr>
                                <td>
                                    <p>{{ $flight['outbound']['airline'] }} </p>
                                    <p>{{ $flight['inbound']['airline'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['number'] }}</p>
                                    <p>{{ $flight['inbound']['number'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['departure_airport'] }}</p>
                                    <p>{{ $flight['inbound']['departure_airport'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['departure_time'] }}</p>
                                    <p>{{ $flight['inbound']['departure_time'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['arrival_airport'] }}</p>
                                    <p>{{ $flight['inbound']['arrival_airport'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['arrival_time'] }}</p>
                                    <p>{{ $flight['inbound']['arrival_time'] }}</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['duration'] }} minutes</p>
                                    <p>{{ $flight['inbound']['duration'] }} minutes</p>
                                </td>
                                <td>
                                    <p>{{ $flight['outbound']['distance'] }} km
                                    <p>{{ $flight['inbound']['distance'] }} km
                                </td>
                                <td>
                                    <p>${{ ((float) $flight['outbound']['price']) }}</p>
                                    <p>${{ ((float) $flight['inbound']['price']) }}</p>
                                </td>
                                <td>
                                    <p>${{ ((float) $flight['outbound']['price']) + ((float) $flight['inbound']['price']) }}</p>
                                </td>
                            </tr>

                        @else
                            <tr>
                                <td>{{ $flight['airline'] }}</td>
                                <td>{{ $flight['number'] }}</td>
                                <td>{{ $flight['departure_airport'] }}</td>
                                <td>{{ $flight['departure_time'] }}</td>
                                <td>{{ $flight['arrival_airport'] }}</td>
                                <td>{{ $flight['arrival_time'] }}</td>
                                <td>{{ $flight['duration'] }} minutes</td>
                                <td>{{ $flight['distance'] }} km</td>
                                <td>${{ ((float) $flight['price']) }}</td>
                                <td>${{ ((float) $flight['price']) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        {{ $paginatedFlights->links() }}
    @else
        <p>No flights available for the selected departure time.</p>
    @endif
</div>

@endsection