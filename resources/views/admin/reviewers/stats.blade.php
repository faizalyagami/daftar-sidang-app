@extends('layouts.app')

@section('title', 'Statistik Reviewer')

@section('content')
<div class="card">
    <div class="card-header-custom">
        <h5 class="mb-0">
            <i class="bi bi-bar-chart me-2"></i>
            Statistik Beban Kerja Reviewer
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Reviewer</th>
                        <th>Email</th>
                        <th>Pending</th>
                        <th>In Progress</th>
                        <th>Selesai</th>
                        <th>Total</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats as $index => $stat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $stat['reviewer']->name }}</td>
                        <td>{{ $stat['reviewer']->email }}</td>
                        <td>
                            @if($stat['pending'] > 0)
                                <span class="badge bg-warning">{{ $stat['pending'] }}</span>
                            @else
                                <span class="badge bg-secondary">0</span>
                            @endif
                        </td>
                        <td>
                            @if($stat['in_progress'] > 0)
                                <span class="badge bg-info">{{ $stat['in_progress'] }}</span>
                            @else
                                <span class="badge bg-secondary">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $stat['completed'] }}</span>
                        </td>
                        <td>{{ $stat['total'] }}</td>
                        <td style="width: 200px;">
                            <div class="progress" style="height: 8px;">
                                @php
                                    $percentage = $stat['total'] > 0 ? ($stat['completed'] / $stat['total']) * 100 : 0;
                                @endphp
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                            <small>{{ number_format($percentage, 1) }}%</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection