@extends('layouts.main')
@section('style')
    <link href="{{ asset('/css/buttons.dataTables.min.css')}}" rel="stylesheet">
    <style>
        button{
            width: 100%;
            height: 50px;
        }
        div.container {
            width: 80%;
        }
        table.table {
            /*clear: both;*/
            /*margin-bottom: 6px !important;*/
            /*max-width: none !important;*/
            /*table-layout: fixed;*/
            /*word-break: break-all;*/
        }
        .bonusInfoSign:hover{
            cursor: pointer;
            color: #5bc0de;
        }
    </style>
@endsection
@section('content')



{{--Header page --}}
{{--Pomocnicza zmienna do przekzywanie informacji czy dane wypłaty można pobrać do csv--}}
@php
    $payment_saved_pom = 0;
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <div class="alert gray-nav ">Rozliczenia / Podgląd Wypłat Kadra</div>
        </div>
    </div>
</div>


    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Podgląd Wypłat
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="start_stop">
                                <div class="panel-body">
                                        <div class="well">
                                            <form action="view_payment_cadre" id="view_payment_cadre" method="post">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="col-md-8">
                                                    <select name="search_money_month" class="form-control" style="font-size:18px;" id="search_money_month">
                                                        @for ($i=0; $i < 3; $i++)
                                                            @php
                                                            $date = date("Y-m", mktime(0, 0, 0, date("m")-$i, 1, date("Y")));
                                                            @endphp
                                                            @if (isset($month))
                                                                @if ($month == $date)
                                                                    <option selected>{{$date}}</option>
                                                                @else{
                                                                    <option>{{$date}}</option>
                                                                @endif
                                                            @else
                                                                <option>{{$date}}</option>
                                                            @endif
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="btn btn-primary" id="show_load_data_info" style="width:100%;">Wyświetl</button>
                                                </div>
                                                </br></br>
                                                <input type="hidden" value="0" id="toSave" name="toSave">
                                            </form>
                                        </div>
                                        @if(isset($month))
                                            <div class="col-md-12">
                                                @if(!$payment_saved->isNotEmpty())
                                                    <button class="btn btn-danger" id="accept_payment">Zaakceptuj wypłaty</button>
                                                    @php $payment_saved_pom = 0 @endphp
                                                @else
                                                    @php $payment_saved_pom = 1 @endphp
                                                    <button class="btn btn-success">Wypłaty zaakceptowane</button>
                                                @endif
                                            </div>
                                    @endif
                                    </div>
                                                @if(isset($month))
                                                            @php
                                                                $payment_total = 0;
                                                                $rbh_total = 0;
                                                                $documents_total = 0;
                                                                $students_total = 0;
                                                                $users_total = 0;
                                                            @endphp
                                                            @php $wrapper_scroll = 0;  @endphp
                                                            @foreach($salary as $item => $key)
                                                                @foreach($agencies as $agency)
                                                                    @if($agency->id == $item)
                                                                        @php
                                                                            $salary_total_all = 0;
                                                                            $to_account_total_all = 0;
                                                                            $row_number = 1;@endphp

                                                                    {{--Typ Umowy--}}
                                                                    <div>
                                                                        <h4 style="margin-top:20px;"><b>Tabela Wypłat - {{$agency->name}}:</b></h4>
                                                                    </div>
                                                                    <br />
                                                                <div class="panel panel-default">
                                                                    <div class="panel-body">
                                                                    <div class="table-responsive">
                                                                      <div class="wrapper{{++$wrapper_scroll}}">
                                                                      <div class="div{{$wrapper_scroll}}"></div>
                                                                      </div>
                                                                      <div class="wrapper{{++$wrapper_scroll}}">
                                                                        <div class="div{{$wrapper_scroll}}">
                                                                        <table class="table table-striped table-bordered dt-responsive nowrap"cellspacing="0"  width="100%" id="datatable{{$agency->id}}">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>Lp.</th>
                                                                                <th>Imię</th>
                                                                                <th>Nazwisko</th>
                                                                                <th>Login Godzinówki</th>
                                                                                <th>Filia</th>
                                                                                <th>Podstawa</th>
                                                                                <th>Dodatek</th>
                                                                                <th>Premia</th>
                                                                                <th>Kara</th>
                                                                                <th>Student</th>
                                                                                <th>Dokument</th>
                                                                                <th>Całość na konto</th>
                                                                                <th>Max na konto</th>
                                                                                <th>Wynagrodzenie</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                        @foreach($key->sortBy('dep_name') as $item2)
                                                                            @php
                                                                                    $student = ($item2->student == 0) ? "Nie" : "Tak";
                                                                                    $documents = ($item2->documents == 0) ? "Nie" : "Tak";
                                                                                    $salary = ($item2->salary == null) ? 0 : $item2->salary;
                                                                                    $additional_salary = ($item2->additional_salary == null) ? 0 : $item2->additional_salary;
                                                                                    $bonus = ($item2->bonus == null) ? 0 : $item2->bonus;
                                                                                    $penatly = ($item2->penalty == null) ? 0 : $item2->penalty;
                                                                                    $total_one_salary = $salary+$additional_salary+$bonus-$penatly;
                                                                                    if($total_one_salary < 0)
                                                                                        $total_one_salary = 0;
                                                                                    $salary_total_all+=$total_one_salary;
                                                                                    $payment_total=+$salary_total_all;
                                                                                    $toAccount = ($item2->max_transaction == null) ? 0 : $item2->max_transaction;
                                                                                    $to_account_total_all += $toAccount;
                                                                                    $salary_to_account = $item2->salary_to_account == 0 ? "Nie" : "Tak";
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{$row_number++}}</td>
                                                                                <td>{{($item2->first_name)}}</td>
                                                                                <td>{{($item2->last_name)}}</td>
                                                                                <td>{{($item2->username)}}</td>
                                                                                <td>{{($item2->dep_name.' '.$item2->dep_type)}}</td>
                                                                                <td>{{$salary}}</td>
                                                                                <td>{{$additional_salary}}</td>
                                                                                <td>{{$bonus}}@if(count($item2->bonuses->where('type',2))>0) <span class="glyphicon glyphicon-info-sign bonusInfoSign" onclick="showBonuses('{{$item2->bonuses->where('type',2)}}')"></span>@endif</td>
                                                                                <td>{{$penatly*(-1)}}@if(count($item2->bonuses->where('type',1))>0) <span class="glyphicon glyphicon-info-sign bonusInfoSign" onclick="showBonuses('{{$item2->bonuses->where('type',1)}}')"></span>@endif</td>
                                                                                <td>{{$student}}</td>
                                                                                <td>{{$documents}}</td>
                                                                                <td>{{$salary_to_account}}</td>
                                                                                <td>{{$toAccount}}</td>
                                                                                <td>{{$total_one_salary}}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                        <tr>
                                                                            <td colspan="11"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td style="display: none;"></td>
                                                                            <td> Suma:</td>
                                                                            <td>{{$to_account_total_all}} </td>
                                                                            <td>{{$salary_total_all}}</td>
                                                                        </tr>
                                                                            </tbody>
                                                                        </table>
                                                                      </div>
                                                                    </div>
                                                                      </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                @endforeach
                                                            @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal" id="modalBonuses" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">{{--
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                <h4 class="modal-title">Szczegóły premii</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('script')

    <script src="{{ asset('/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('/js/jszip.min.js')}}"></script>
    <script src="{{ asset('/js/buttons.html5.min.js')}}"></script>
    @if(isset($payment_total))

        @if($payment_total !=0)
            <script>
                var payment_total =  <?php echo json_encode($payment_total); ?>;
                var documents_total = <?php echo json_encode($documents_total); ?>;
                var students_total = <?php echo json_encode($students_total); ?>;
                var rbh_total = <?php echo json_encode($rbh_total); ?>;
                var month = <?php echo json_encode($month); ?>;
                var user_total = <?php echo json_encode($users_total); ?>;
                var departments = <?php echo json_encode($departments); ?>;
                $.ajax({
                    type: "POST",
                    url: '{{ route('api.summary_payment_save') }}',
                    data: {"payment_total": payment_total, "documents_total": documents_total,
                        "students_total": students_total,"rbh_total":rbh_total,"month":month,
                        "user_total":user_total},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                    }
                });

            </script>
        @endif
    @endif

<script>
    //colors
    var bgcolorArray = [
        '#51ff93',
        '#d9ff1d',
        '#ffc497',
        '#F2B0B0',
        '#8bd9ba',
        '#00d65b',
        '#C6BD0B',
        '#F2EDB0',
        '#B0F2EA',
        '#e2f2de',
        '#C1D1F2',
        '#E9DCF2',
        '#DCF2E7',
        '#00d5d1',
        '#42df00'];
    var addCount = 15;
    let modalBonuses  =  $('#modalBonuses');

    $(document).ready(function() {
        $('thead > tr> th').css({ 'min-width': '1px', 'max-width': '100px' });

        table = $('#datatable1').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Umowy Szkoleniowe Kadra',
                    customize: function( xlsx ) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];


                        var fillscount = +$('fills', styles).attr('count');
                        $('fills', styles).attr('count', addCount + fillscount + '');
                        var cellXfscount = +$('cellXfs', styles).attr('count');
                        $('cellXfs', styles).attr('count', addCount + cellXfscount + '');


                        var fills = $('fills', styles)[0];
                        var cellXfs = $('cellXfs', styles)[0];
                        var namespace = styles.lookupNamespaceURI(null);


                        for (var i = 0; i < bgcolorArray.length; i++)
                        {
                            var bgcolor = bgcolorArray[i];
                            var fill = styles.createElementNS(namespace, 'fill');
                            var patternFill = styles.createElementNS(namespace, 'patternFill');
                            patternFill.setAttribute("patternType", "solid");
                            var fgColor = styles.createElementNS(namespace, 'fgColor');
                            fgColor.setAttribute("rgb", bgcolor.substring(1));
                            var bgColor = styles.createElementNS(namespace, 'bgColor');
                            bgColor.setAttribute("indexed", "64");
                            patternFill.appendChild(fgColor);
                            patternFill.appendChild(bgColor);
                            fill.appendChild(patternFill);
                            fills.appendChild(fill);

                            var xf = styles.createElementNS(namespace, 'xf');
                            xf.setAttribute("numFmtId", "0");
                            xf.setAttribute("fontId", "0");
                            xf.setAttribute("fillId", "" + (fillscount + i));
                            xf.setAttribute("borderId", "0");
                            xf.setAttribute("applyFont", "1");
                            xf.setAttribute("applyFill", "1");
                            xf.setAttribute("applyBorder", "1");
                            cellXfs.appendChild(xf);
                        }
                        $('row c[r^="E"]', sheet).each( function (key,value) {
                            if($('is t', this).text() != 'Całość na konto')
                            {
                                var text = $('is t', this)[0].textContent;
                                    for(var l=0;l<departments.length;l++)
                                    {
                                        if(departments[l].dep_name+" "+departments[l].dep_type == text)
                                        {
                                            let row_number = $('is t', this)[0].closest('c').attributes[1].nodeValue;
                                            row_number = row_number.substring(1);
                                            $('row:nth-child('+row_number+') c', sheet).attr('s', (cellXfscount + departments[l].id) + '');
                                        }
                                    }
                            }
                        });
                        $('row:nth-child(2) c', sheet).attr( 's', '42' );
                        $('row:first c', sheet).attr( 's', '51','1','2' );
                        $('row:last c', sheet).attr( 's', '2' );
                    }
                }
            ],
            "autoWidth": false,
            "searching": false,
            "ordering": false,
            "paging": false,
            "bInfo": false,
        });
        table2 = $('#datatable2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'APT Job Center Service Kadra',
                    customize: function( xlsx ) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];


                        var fillscount = +$('fills', styles).attr('count');
                        $('fills', styles).attr('count', addCount + fillscount + '');
                        var cellXfscount = +$('cellXfs', styles).attr('count');
                        $('cellXfs', styles).attr('count', addCount + cellXfscount + '');


                        var fills = $('fills', styles)[0];
                        var cellXfs = $('cellXfs', styles)[0];
                        var namespace = styles.lookupNamespaceURI(null);


                        for (var i = 0; i < bgcolorArray.length; i++)
                        {
                            var bgcolor = bgcolorArray[i];
                            var fill = styles.createElementNS(namespace, 'fill');
                            var patternFill = styles.createElementNS(namespace, 'patternFill');
                            patternFill.setAttribute("patternType", "solid");
                            var fgColor = styles.createElementNS(namespace, 'fgColor');
                            fgColor.setAttribute("rgb", bgcolor.substring(1));
                            var bgColor = styles.createElementNS(namespace, 'bgColor');
                            bgColor.setAttribute("indexed", "64");
                            patternFill.appendChild(fgColor);
                            patternFill.appendChild(bgColor);
                            fill.appendChild(patternFill);
                            fills.appendChild(fill);

                            var xf = styles.createElementNS(namespace, 'xf');
                            xf.setAttribute("numFmtId", "0");
                            xf.setAttribute("fontId", "0");
                            xf.setAttribute("fillId", "" + (fillscount + i));
                            xf.setAttribute("borderId", "0");
                            xf.setAttribute("applyFont", "1");
                            xf.setAttribute("applyFill", "1");
                            xf.setAttribute("applyBorder", "1");
                            cellXfs.appendChild(xf);
                        }
                        $('row c[r^="E"]', sheet).each( function (key,value) {
                            if($('is t', this).text() != 'Całość na konto')
                            {
                                var text = $('is t', this)[0].textContent;
                                for(var l=0;l<departments.length;l++)
                                {
                                    if(departments[l].dep_name+" "+departments[l].dep_type == text)
                                    {
                                        let row_number = $('is t', this)[0].closest('c').attributes[1].nodeValue;
                                        row_number = row_number.substring(1);
                                        $('row:nth-child('+row_number+') c', sheet).attr('s', (cellXfscount + departments[l].id) + '');
                                    }
                                }
                            }
                        });
                        $('row:nth-child(2) c', sheet).attr( 's', '42' );
                        $('row:first c', sheet).attr( 's', '51','1','2' );
                        $('row:last c', sheet).attr( 's', '2' );
                    }
                }
            ],
            "autoWidth": false,
            "searching": false,
            "ordering": false,
            "paging": false,
            "bInfo": false,
        });
        table3 = $('#datatable3').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Fruit Garden Kadra',
                    customize: function( xlsx ) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];


                    var fillscount = +$('fills', styles).attr('count');
                    $('fills', styles).attr('count', addCount + fillscount + '');
                    var cellXfscount = +$('cellXfs', styles).attr('count');
                    $('cellXfs', styles).attr('count', addCount + cellXfscount + '');


                    var fills = $('fills', styles)[0];
                    var cellXfs = $('cellXfs', styles)[0];
                    var namespace = styles.lookupNamespaceURI(null);


                    for (var i = 0; i < bgcolorArray.length; i++)
                    {
                        var bgcolor = bgcolorArray[i];
                        var fill = styles.createElementNS(namespace, 'fill');
                        var patternFill = styles.createElementNS(namespace, 'patternFill');
                        patternFill.setAttribute("patternType", "solid");
                        var fgColor = styles.createElementNS(namespace, 'fgColor');
                        fgColor.setAttribute("rgb", bgcolor.substring(1));
                        var bgColor = styles.createElementNS(namespace, 'bgColor');
                        bgColor.setAttribute("indexed", "64");
                        patternFill.appendChild(fgColor);
                        patternFill.appendChild(bgColor);
                        fill.appendChild(patternFill);
                        fills.appendChild(fill);

                        var xf = styles.createElementNS(namespace, 'xf');
                        xf.setAttribute("numFmtId", "0");
                        xf.setAttribute("fontId", "0");
                        xf.setAttribute("fillId", "" + (fillscount + i));
                        xf.setAttribute("borderId", "0");
                        xf.setAttribute("applyFont", "1");
                        xf.setAttribute("applyFill", "1");
                        xf.setAttribute("applyBorder", "1");
                        cellXfs.appendChild(xf);
                    }
                    $('row c[r^="E"]', sheet).each( function (key,value) {
                        if($('is t', this).text() != 'Całość na konto')
                        {
                            var text = $('is t', this)[0].textContent;
                            for(var l=0;l<departments.length;l++)
                            {
                                if(departments[l].dep_name+" "+departments[l].dep_type == text)
                                {
                                    let row_number = $('is t', this)[0].closest('c').attributes[1].nodeValue;
                                    row_number = row_number.substring(1);
                                    $('row:nth-child('+row_number+') c', sheet).attr('s', (cellXfscount + departments[l].id) + '');
                                }
                            }
                        }
                    });
                    $('row:nth-child(2) c', sheet).attr( 's', '42' );
                    $('row:first c', sheet).attr( 's', '51','1','2' );
                    $('row:last c', sheet).attr( 's', '2' );
                }
                }
            ],
            "autoWidth": false,
            "searching": false,
            "ordering": false,
            "paging": false,
            "bInfo": false,
        });
        table4 = $('#datatable4').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Fruit Garden', customize: function( xlsx ) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var styles = xlsx.xl['styles.xml'];


                    var fillscount = +$('fills', styles).attr('count');
                    $('fills', styles).attr('count', addCount + fillscount + '');
                    var cellXfscount = +$('cellXfs', styles).attr('count');
                    $('cellXfs', styles).attr('count', addCount + cellXfscount + '');


                    var fills = $('fills', styles)[0];
                    var cellXfs = $('cellXfs', styles)[0];
                    var namespace = styles.lookupNamespaceURI(null);


                    for (var i = 0; i < bgcolorArray.length; i++)
                    {
                        var bgcolor = bgcolorArray[i];
                        var fill = styles.createElementNS(namespace, 'fill');
                        var patternFill = styles.createElementNS(namespace, 'patternFill');
                        patternFill.setAttribute("patternType", "solid");
                        var fgColor = styles.createElementNS(namespace, 'fgColor');
                        fgColor.setAttribute("rgb", bgcolor.substring(1));
                        var bgColor = styles.createElementNS(namespace, 'bgColor');
                        bgColor.setAttribute("indexed", "64");
                        patternFill.appendChild(fgColor);
                        patternFill.appendChild(bgColor);
                        fill.appendChild(patternFill);
                        fills.appendChild(fill);

                        var xf = styles.createElementNS(namespace, 'xf');
                        xf.setAttribute("numFmtId", "0");
                        xf.setAttribute("fontId", "0");
                        xf.setAttribute("fillId", "" + (fillscount + i));
                        xf.setAttribute("borderId", "0");
                        xf.setAttribute("applyFont", "1");
                        xf.setAttribute("applyFill", "1");
                        xf.setAttribute("applyBorder", "1");
                        cellXfs.appendChild(xf);
                    }
                    $('row c[r^="E"]', sheet).each( function (key,value) {
                        if($('is t', this).text() != 'Całość na konto')
                        {
                            var text = $('is t', this)[0].textContent;
                            for(var l=0;l<departments.length;l++)
                            {
                                if(departments[l].dep_name+" "+departments[l].dep_type == text)
                                {
                                    let row_number = $('is t', this)[0].closest('c').attributes[1].nodeValue;
                                    row_number = row_number.substring(1);
                                    $('row:nth-child('+row_number+') c', sheet).attr('s', (cellXfscount + departments[l].id) + '');
                                }
                            }
                        }
                    });
                    $('row:nth-child(2) c', sheet).attr( 's', '42' );
                    $('row:first c', sheet).attr( 's', '51','1','2' );
                    $('row:last c', sheet).attr( 's', '2' );
                }
                }
            ],
            "autoWidth": false,
            "searching": false,
            "ordering": false,
            "paging": false,
            "bInfo": false,
        });

        // Ukrycie klawisza pozwalającego wygenerować wypłatę w csv
        let payment_saved = '{{$payment_saved_pom}}';
        if(payment_saved == 0){
            $(".dt-buttons").css('display','none');
        }
    });

    function showBonuses(bonuses){
        bonuses = JSON.parse(bonuses);
        let modalBonusesBody = modalBonuses.find('.modal-body');
        modalBonusesBody.empty();
        let thead = $('<thead>').append(
            $('<tr>')
                .append($('<th>').append('Data'))
                .append($('<th>').append('Kara/premia'))
                .append($('<th>').append('Dodał'))
                .append($('<th>').append('Powód'))
        );
        let tbody =$('<tbody>');
        $.each(bonuses, function (index, bonus) {
            tbody.append($('<tr>')
                .append($('<td>').append(bonus.event_date))
                .append($('<td>').append(function (){
                    let div = $('<div>');
                    if(bonus.type === 1){
                        div.css({'background-color': '#ff7b7b',
                            'padding':'4px 10px',
                            'border-radius': '5px',
                            'border':'1px solid #ff6a6a',
                            'color':'#7f2222',
                            'text-align':'center'
                        }).append('Kara: ');
                    }else{
                        div.css({'background-color': '#70ff5c',
                            'padding':'4px 10px',
                            'border-radius': '5px',
                            'border':'1px solid #33ff36',
                            'color':'#4b5c44',
                            'text-align':'center'
                        }).append('Premia: ');
                    }
                    div.append(bonus.amount);
                    return div;
                }))
                .append($('<td>').append(function (){
                    let div = $('<div>');
                    div.css({'background-color': '#d9edf7',
                            'padding':'4px 10px',
                            'border-radius': '5px',
                            'border':'1px solid #bce8f1',
                            'color':'#31708f',
                            'text-align':'center'
                        }).append(bonus.manager);
                    return div;
                }))
                .append($('<td>').append(bonus.comment))
            )
        });
        let table = $('<table>').addClass('table display datatable')
            .css({'width':'100%'}).append(thead).append(tbody);
        modalBonusesBody.append(table);
        modalBonuses.modal('show');
        let datatable = table.DataTable({
            paging:false,
            scrollY:true,
            height: '65vh',
            scrollCollapse: true
        });
        modalBonuses.on('shown.bs.modal', function () {
            datatable.columns.adjust().draw();
        });
    }

    $('#accept_payment').on('click',function (e) {

        swal({
            title: 'Jesteś pewien?',
            text: "Spowoduje to zaakceptowanie wypłat, bez możliwości cofnięcia zmian!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Zaakceptuj'
        }).then((result) => {
            if (result.value)
            {
                $('#toSave').val(1);
                $('#accept_payment').prop('disabled',true);
                $('#view_payment_cadre').submit();
                {{--let moneyMonth = document.querySelector('#search_money_month');--}}
                {{--console.log(moneyMonth.value);--}}

                {{--let url = `{{URL::to('/savePaymentsAjax')}}`;--}}
                {{--console.log(url);--}}
                {{--const ourHeaders = new Headers();--}}
                {{--ourHeaders.append('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));--}}
                {{--let data = new FormData();--}}
                {{--data.append('toSave', '1');--}}
                {{--data.append('search_money_month', `${moneyMonth.value}`);--}}

                {{--console.time('Początek pomiaru czasu');--}}
                {{--fetch(url, {--}}
                    {{--method: 'post',--}}
                    {{--headers: ourHeaders,--}}
                    {{--credentials: "same-origin",--}}
                    {{--body: data--}}
                {{--})--}}
                    {{--.then(function(response) {--}}
                        {{--return response.blob();--}}
                    {{--}).then(function(blob) {--}}
                        {{--console.timeEnd('Początek pomiaru czasu');--}}
                        {{--console.log(blob);--}}
                    {{--})--}}
                    {{--.catch(err => console.log(err))--}}
            }
        });
    });

</script>
@endsection
