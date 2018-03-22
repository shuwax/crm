{{--@extends('layouts.tyleoile')--}}
{{--@section('content')--}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Verdana, "Tahoma", sans-serif;
    }

    body {
        width: 100vw;
        height: 100vh;
    }

    .wraper {
        width: 100vw;
        height: 100vh;

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    /* ***************HEADER************** */

    header {
        width: 100%;
        height: 20%;
        padding-bottom: 20px;
        background-color: #2A2A2A;

        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
    }

    header > div {
        color: #FFF6B1;
    }

    .first {
        height: 100%;

        display:flex;
        flex-grow: 1;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    .second {
        height: 100%;

        display:flex;
        flex-grow: 1;
        flex-direction: row;
        align-items:flex-start;
        justify-content: flex-end;
        padding-top: 10px;
        margin-right: 55px;
        color: white;
    }

    #first-span {
        font-size: 3.7em;
    }

    #second-span {
        font-size: 2.4em;
    }

    .no-data {
        font-size: 5.2em;
        color: red;
        padding: 50px;
    }
    /* ************END HEADER*********** */

    section {
        width: 100%;
        height: 80%;

        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: flex-start;
    }

    /* ************TABLE**************** */

    table {
        font-size: 2.45em;
    }

    .firstTh {
        width: 7%;
    }

    td {
        text-align: center;
    }

    th {
        text-align: center;
    }

    thead {
        background: linear-gradient(dimgray, white);
    }

    tbody > tr:nth-of-type(2n) {
        background-color: #EDEDF4;
    }

    .two {
        background: lightcyan;
    }

    .secondTable {
        font-size: 2.6em;
    }

    /***********Last slide**************/
    .active {
        display: table;
    }

    .inactive {
        display: none;
    }

    .indx {
        width: 7%;
    }

    .secondTd {
        width: 40%;
        /*font-weight: bold;*/
    }

    .firstTr {
        height: 9vh;
        font-size: 1.2em;
    }

    .secondTr {
        height: 6.55vh;
        font-size: 0.9em;
    }

    .secondTh {
        font-size: 0.85em;
    }

    .firstThead {
        font-size: 1.2em;
    }


</style>
<div class="wraper">
    <header>
        <div class="first"><span id="first-span">Bieżące wyniki
                <script>
                         /***********Displaying actual date*********/
                         setInterval(function() {
                             var today = new Date();
                             $('#first-span').text('Bieżące wyniki ');

                             /*****Adding 0 to numbers less than 10*****/
                             if(today.getMinutes() < 10) {
                                 var minuty = '0' + today.getMinutes();
                             } else {
                                 var minuty = today.getMinutes();
                             }

                             if(today.getHours() < 10) {
                                 var godzina = '0' + today.getHours();
                             } else {
                                 var godzina = today.getHours();
                             }

                             if(today.getMonth() < 10) {
                                 var miesiac = '0' + today.getMonth();
                             } else {
                                 var miesiac = today.getMonth();
                             }

                             if(today.getDate() < 10) {
                                 var dzien = '0' + today.getDate();
                             } else {
                                 var dzien = today.getDate();
                             }

                             if(today.getSeconds() < 10) {
                                 var sekundy = '0' + today.getSeconds();
                             } else {
                                 var sekundy = today.getSeconds();
                             }
                             /****END***/

                             var actualDate = dzien + '.' + miesiac + '.' + today.getFullYear() + 'r. ' + godzina + ':' + minuty;
                             $('#first-span').text('Bieżące wyniki ');
                             $('#first-span').append(actualDate);
                         },1000);

                         /******Refreshing page every hour and after 5 minutes within each hour******/
                         var today = new Date();
                         var today2 = new Date();
                         setInterval(function(){
                             today = new Date();
                             if(today.getMinutes() == '5' && (today.getSeconds() == '1' || today.getSeconds() == '2')){
                                 window.location.reload(1);
                             }
                         }, 1000);
                         var actualHour = today2.getHours();
                         setInterval(function(){
                             today2 = new Date();
                             if(actualHour - today2.getHours() != 0) {
                                 window.location.reload(1);
                                 actualHour = today2.getHours()
                             }
                         }, 1000);
                </script></span></div>
        <div class="second">
            <div class="second-cont-inside">
                <span id="second-span">

                </span>
            </div>
        </div>
    </header>

    <section>
        @if(sizeof($userTable) != 0 || sizeof($reportTable) != 0)
            <table class="table inactive">
                <thead class="firstThead">
                <tr>
                    <th class="firstTh">L.P</th>
                    <th>Imie &amp; Nazwisko</th>
                    <th>Czas</th>
                    <th>L.zgód</th>
                    <th>Średnia</th>
                    <th>Pln/h</th>
                </tr>
                </thead>

                <tbody class="table-body table-body1">
                @foreach($userTable as $t)
                    <tr class="one firstTr">
                        <td class="indx"></td>
                        <td>{{$t->user->first_name . ' ' . $t->user->last_name}}</td>
                        <td>{{substr($t->login_time, 0, 5)}} </td>
                        <td>{{$t->success}}</td>
                        <td>{{$t->average}}</td>
                        <td class="pr">
                            <script>
                                var base = {{$t->user->department_info->commission_start_money}};
                                var step = {{$t->user->department_info->commission_step}};
                                var start = {{$t->user->department_info->commission_avg}};
                                var avg = {{$t->average}};
                                var salary;

                                if(start == 2.5) { //start od 2.5
                                    var count;
                                    var difference;
                                    if(avg >= start && avg < (start + 0.25)) { // avg in <2.5 ; 2.75)
                                        salary = base + step;
                                    }
                                    else if (avg >= start + 0.25) { // avg in <2.75 ; infty)
                                        difference = avg - start;
                                        if(difference % 0.25 == 0) { //Gdy roznica jest wielokrotnoscia 0.25
                                            difference += 0.01; // dodajemy, aby wskoczyło w próg o jeden wyżej
                                        }
                                        count = Math.ceil(difference / 0.25);
                                        salary = base + (count * step);
                                    }
                                    else { // avg in <0 ; 2.5)
                                        salary = base;
                                    }
                                }
                                else { //start od 3.0
                                    var count;
                                    var difference;
                                    if (avg >= start && avg <= start + 0.25) { // avg in <3 ; 3.25>
                                        salary = base + step;
                                    }
                                    else if (avg > start + 0.25) { // avg in (3.25 ; infty)
                                        difference = avg - start;
                                        count = Math.ceil(difference / 0.25);
                                        salary = base + (count * step);
                                    }
                                    else { //avg in <0 ; 3)
                                        salary = base;
                                    }
                                }
                                $('.pr:last').text(salary);
                            </script>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
            <table class="table active secondTable">
                <thead class="two secondTh">
                <tr>
                    <th>Oddział</th>
                    <th>Godzina</th>
                    <th>Średnia</th>
                    <th>Liczba zaproszeń</th>
                    <th>Proc. janków</th>
                </tr>
                </thead>

                <tbody class="table-body">
                @foreach($reportTable as $r)
                    <tr class="one secondTr">
                        <td class="secondTd">{{$r->department_info->departments->name . ' ' . $r->department_info->department_type->name}}</td>
                        <td>{{substr($r->hour, 0, 5)}}</td>
                        <td>{{$r->average}}</td>
                        <td>{{$r->success}}</td>
                        <td>{{$r->janky_count}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--*********CASE OF NO DATA***********--}}
        @else
            <div class="no-data">Brak danych!</div>
            <script>
                setInterval(function(){
                    window.location.reload(1);
                }, 30000);
            </script>
        @endif
            {{--************END CASE***************--}}
    </section>

    <script>
        $(document).ready(function() {
            var tablica = [];
            /***********Filling L.P column*********/
            var indx = $('.indx');
            indx.each(function(index) {
                $(this).prepend(index+1);
            });
            /*************End Filling*************/

            var rekordy = $('.table-body1 tr');
            rekordy.each(function() {
                var danyRekord = $(this);
                tablica.push(danyRekord);
            });

            /*****Function that splits data rows into array of objects*****/
            var chunks = function(array, size) {
                var results = [];
                while (array.length) {
                    results.push(array.splice(0, size));
                }
                return results;
            };
            /***********End of function*********/

            var delayInMilliseconds = 15000;
            var newTable = chunks(tablica,8);
            var tableBody = $('.table-body1');
            tableBody.text(' ');
            tableBody.append(newTable[0]);
            var iteracja = 0;

            setInterval(function() {
                if(iteracja == -1) {
                    $('table:last').toggleClass('active');
                    $('table:last').toggleClass('inactive');
                    $('table:first').toggleClass('inactive');
                    $('table:first').toggleClass('active');
                    iteracja++;
                    tableBody.text(' ');
                }
                else {
                    if(iteracja == 0) {
                        $('table:last').toggleClass('active');
                        $('table:last').toggleClass('inactive');
                        $('table:first').toggleClass('inactive');
                        $('table:first').toggleClass('active');
                    }
                    tableBody.text(' ');
                    tableBody.append(newTable[iteracja]);
                    iteracja++;
                    if(iteracja == newTable.length) {
                        iteracja = -1;
                    }
                }

            },delayInMilliseconds);
        });
    </script>
</div>
{{--@endsection--}}










