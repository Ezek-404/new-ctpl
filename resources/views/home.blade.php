@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
    <h1>Home</h1>
@stop

@section('content')
    <div class="row">
        {{-- Private Car (PC) --}}
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="0" text="Private Car (PC)" icon="fas fa-car" 
                theme="primary" id="pc-box"/>
        </div>

        {{-- Tricycle (TC) --}}
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="0" text="Tricycle (TC)" icon="fas fa-motorcycle" 
                theme="warning" id="tc-box"/>
        </div>

        {{-- Motorcycle (MC) --}}
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="0" text="Motorcycle (MC)" icon="fas fa-biking" 
                theme="info" id="mc-box"/>
        </div>

        {{-- Commercial (CV) --}}
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="0" text="Commercial (CV)" icon="fas fa-truck" 
                theme="success" id="cv-box"/>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // We define the target numbers directly from PHP
        const counts = {
            '#pc-box h3': {{ $pcCount ?? 0 }},
            '#tc-box h3': {{ $tcCount ?? 0 }},
            '#mc-box h3': {{ $mcCount ?? 0 }},
            '#cv-box h3': {{ $cvCount ?? 0 }}
        };

        // Loop through each ID and animate the 'h3' tag inside it
        $.each(counts, function(selector, targetValue) {
            $({ Counter: 0 }).animate({ Counter: targetValue }, {
                duration: 2000,
                easing: 'swing',
                step: function () {
                    $(selector).text(Math.ceil(this.Counter));
                },
                complete: function() {
                    $(selector).text(targetValue);
                }
            });
        });
    });
</script>
@stop