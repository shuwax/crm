@extends('layouts.main')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <div class="alert gray-nav">Raport Tygodniowy Coaching Dyrektor</div>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ URL::to('/pageReportCoachingDirector') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row">

            <div class="panel panel-default">
                <div class="panel-heading">
                    Legenda
                </div>
                <div class="panel-body">
                    <div class="alert alert-success">
                        <h4>
                            <p>W toku - coachingi trwające mniej niż <strong>4 dni.</strong> </p>
                            <p>Nierozliczone - coachingi, po <strong>4 dniach</strong> od daty założenia coachingu <strong>oczekujące na akceptacje</strong> przez trenera. </p>
                            <p>Rozliczone  - coachingi <strong>zakończone</strong>, zaakceptowane przez kierownika.<p>
                            <p>(Cel osiągnięty - <strong>'osiągnięty wynik' większy bądź równy 'założonemu celowi').</strong></p>
                            <p>(Cel nieosiągnięty - <strong>'osiągnięty wynik' mniejszy od ''założonego  celu').</strong></p>
                            <p>Liczba coachingów - suma wszystkich coachingów: <strong>W toku + Nierozliczone + (Cel osiągnięty + Cel nieosiągnięty).</strong></p>
                            <p>Licznik celu - statystyka wyliczana z rozliczonych coachingów metodą : <strong>osiągnięty wynik - Cel.</strong></p>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Oddział:</label>
                    <select class="form-control" name="selected_dep">
                            @foreach($directors as $director)
                                <option
                                    @if($wiev_type == 'director' && ('10' . $director->id == $selectDirector)) selected @endif
                                value="10{{ $director->id }}">{{ $director->last_name . ' ' . $director->first_name }}</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Miesiąc:</label>
                    <select class="form-control" name="month_selected">
                        @foreach($months as $key => $value)
                            <option @if($month == $key) selected @endif value="{{$key}}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input style="margin-top: 25px; width: 100%" type="submit" class="btn btn-info" value="Generuj raport">
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="start_stop">
                            <div class="panel-body">
                                    @include('mail.reportCoachingWeekDirector')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('script')

    <script>


    </script>
@endsection
