@extends('layouts.main')
@section('style')
    <style>
        .live_user td {
                 text-align: center;
            font-weight: bold;
             }
        live_user {
            table-layout: fixed;
            word-wrap: break-word;
        }

    </style>
    @endsection
@section('content')

<div class="page-header">
    <div class="alert gray-nav ">Godziny / Pracownicy Live</div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="alert alert-info">
            Zakładka <strong>Godziny / Pracownicy Live</strong> pokazuje, informacje o ewidencji godzin. <br>
            Osoba, która w kolumnie <strong>Nowy</strong> ma <i>Tak</i> pracuje w firmie mniej niż 30 RBH.
            <ul class="list-group">
                <li class="list-group-item">Osoby podświetlone na <span style="background-color: #83e05c;">zielono</span> nacisneły "rozpoczynam pracę" w czasie przewidzianym w grafiku.</li>
                <li class="list-group-item">Osoby podświetlone na <span style="background-color: #ffd932;">pomarańczowo</span> nacisneły "rozpoczynam pracę" po czasie przewidzianym w grafiku.</li>
                <li class="list-group-item">Osoby podświetlone na <span style="background-color: #E03838;">czerwono</span> nie nacisneły "rozpoczynam pracę" w czasie przewidzianym w grafiku.</li>
            </ul>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="start_stop">
                        <div class="well">
                        <table class="table table-bordered live_user">
                            <thead>
                            <tr>
                                <th style="width: 6%;">Lp.</th>
                                <th style="width: 19.6%;">Pracownik</th>
                                <th style="width: 5%;">Nowy</th>
                                <th style="width: 19.6%;">Telefon</th>
                                <th style="width: 19.6%;">Grafik</th>
                                <th style="width: 19.6%;">Start/Stop</th>
                                <th style="width: 19.6%;">Rejestracja</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $lp = 1;
                                $start_work = 0;
                                $start_work_click  =0;
                            @endphp
                        @foreach($shedule as $item)
                        @if($item->user->department_info_id == Auth::user()->department_info_id && $item->user->status_work == 1)
                            @php
                                $work_hour = $item->user->work_hours->where('date','=', date('Y-m-d'))->first();
                                $start_work_click = 0;
                                $phone = $item->user->phone;
                                if($phone == 0)
                                {
                                    if($item->user->private_phone != 0)
                                        $phone = $item->user->private_phone;
                                    else
                                          $phone = "Brak";
                                }
                            @endphp
                            <tr id={{$item->id.'w'}}>
                                <td>{{$lp++}}</td>
                                <td>{{$item->user->first_name.' '.$item->user->last_name}}</td>
                                <td>@if($item->newUser == 0)Nie @else Tak @endif</td>
                                <td>{{$phone}}</td>
                                @if($day_number == 0)
                                   <td>
                                       {{$item->monday_start}}
                                       <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                       {{$item->monday_stop}}
                                   </td>
                                    @php $start_work = $item->monday_start;  @endphp
                                @elseif($day_number == 1)
                                    <td>
                                        {{$item->tuesday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->tuesday_stop}}
                                    </td>
                                    @php $start_work = $item->tuesday_start;  @endphp
                                @elseif($day_number == 2)
                                    <td>
                                        {{$item->wednesday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->wednesday_stop}}
                                    </td>
                                    @php $start_work = $item->wednesday_start;  @endphp
                                @elseif($day_number == 3)
                                    <td>
                                        {{$item->thursday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->thursday_stop}}
                                    </td>
                                    @php $start_work = $item->thursday_start;  @endphp
                                @elseif($day_number == 4)
                                    <td>
                                        {{$item->friday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->friday_stop}}
                                    </td>
                                    @php $start_work = $item->friday_start;  @endphp
                                @elseif($day_number == 5)
                                    <td>
                                        {{$item->saturday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->saturday_stop}}
                                    </td>
                                    @php $start_work = $item->saturday_start;  @endphp
                                @elseif($day_number == 6)
                                    <td>
                                        {{$item->sunday_start}}
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        {{$item->sunday_stop}}
                                    </td>
                                    @php $start_work = $item->sunday_start;  @endphp
                                @endif

                                @if(isset($work_hour->id))
                                        @if($work_hour->click_start != null)
                                            @php $start_work_click = $work_hour->click_start;  @endphp
                                        <td>
                                            {{($work_hour->click_start)}}
                                                <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                            {{($work_hour->click_stop)}}
                                        </td>
                                        @else
                                        <td>
                                             <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                        </td>
                                        @endif
                                        <td>
                                             {{($work_hour->register_start)}}
                                             <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                             {{($work_hour->register_stop)}}
                                        </td>
                                @else
                                    <td>
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                    </td>
                                    <td>
                                        <br> <span class="glyphicon glyphicon-arrow-down"></span><br>
                                    </td>
                                @endif
                                @if($start_work_click > $start_work)
                                        <script>
                                            var id = {{$item->id}}
                                            document.getElementById(id+"w").style.backgroundColor = "#ffd932";
                                        </script>

                                @elseif ($start_work_click == 0)
                                    <script>
                                        var id = {{$item->id}}
                                        document.getElementById(id+"w").style.backgroundColor = "#E03838";
                                    </script>
                                 @else
                                    <script>
                                        var id = {{$item->id}}
                                        document.getElementById(id+"w").style.backgroundColor = "#83e05c";
                                    </script>
                                @endif
                            </tr>
                        @endif
                        @endforeach
                            </tbody>
                        </table>
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
