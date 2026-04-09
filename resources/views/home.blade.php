@extends('adminlte::page')

@section('title', 'Dashboard V2')

@section('css')
<style>
    /* Animation for the Top Cards */
    .fade-in-card {
        animation: fadeInUp 0.8s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 25%, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    
    /* Staggered delays for a sequential entrance */
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.3s; }
    .delay-3 { animation-delay: 0.5s; }
    .delay-4 { animation-delay: 0.7s; }

    /* Hover effect for the cards */
    .small-box {
        transition: transform 0.3s ease;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
</style>
@stop

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- Animated Top Info Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6 fade-in-card delay-1">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 class="count-me">{{ $availPC }}</h3>
                    <p>Private Car (PC)</p>
                </div>
                <div class="icon"><i class="fas fa-car"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6 fade-in-card delay-2">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 class="count-me">{{ $availTC }}</h3>
                    <p>Tricycle (TC)</p>
                </div>
                <div class="icon"><i class="fas fa-motorcycle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6 fade-in-card delay-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 class="count-me">{{ $availMC }}</h3>
                    <p>Motorcycle (MC)</p>
                </div>
                <div class="icon"><i class="fas fa-motorcycle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6 fade-in-card delay-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 class="count-me">{{ $availCV }}</h3>
                    <p>Commercial Vehicle (CV)</p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
            </div>
        </div>
    </div>

    {{-- Main Chart Section --}}
    <div class="row fade-in-card delay-4">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Monthly Issuance Bar Chart</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-dark">
                    <div class="row text-center">
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-primary count-me">{{ $pcInsured }}</h5>
                                <span class="description-text">TOTAL PC ISSUED</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-warning count-me">{{ $tcInsured }}</h5>
                                <span class="description-text">TOTAL TC ISSUED</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-info count-me">{{ $mcInsured }}</h5>
                                <span class="description-text">TOTAL MC ISSUED</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="description-block">
                                <h5 class="description-header text-success count-me">{{ $cvInsured }}</h5>
                                <span class="description-text">TOTAL CV ISSUED</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function () {
    // 1. Counter Animation for Numbers
    $('.count-me').each(function () {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 1000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // 2. Bar Chart with Entrance Animation
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    
    var barData = {
      labels: {!! json_encode($months) !!},
      datasets: [
        {
          label: 'PC',
          backgroundColor: '#007bff',
          data: {!! json_encode($pcData) !!}
        },
        {
          label: 'TC',
          backgroundColor: '#ffc107',
          data: {!! json_encode($tcData) !!}
        },
        {
          label: 'MC',
          backgroundColor: '#17a2b8',
          data: {!! json_encode($mcData) !!}
        },
        {
          label: 'CV',
          backgroundColor: '#28a745',
          data: {!! json_encode($cvData) !!}
        }
      ]
    };

    new Chart(barChartCanvas, {
      type: 'bar',
      data: barData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 2000,
            easing: 'easeOutQuart' // Smooth growth effect for bars
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
});
</script>
@stop