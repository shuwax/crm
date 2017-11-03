@extends('layouts.main')
@section('content')
<style>
    button{
        width: 100%;
        height: 50px;
    }
    td.details-control {
        background: url({{ asset('/image/details_open.png')}}) no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url({{ asset('/image/details_close.png')}}) no-repeat center center;
    }
</style>

{{--Header page --}}
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Ustal Grafik</h1>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    Ustal Grafik
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="start_stop">
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="well">
                                            <h1 style ="font-family: 'bebas_neueregular',sans-serif; margin-top:0px;text-shadow: 2px 2px 2px rgba(150, 150, 150, 0.8); font-size:25px;">Wybierz tydzień:</h1>
                                            <form class="form-horizontal" method="post" action="view_schedule">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <select class="form-control" name="show_schedule" id="week_text">
                                                    <option>Wybierz</option>
                                                    @for ($i=0; $i < 5; $i++)
                                                        @php
                                                        $przelicznik = 7*$i;
                                                        $data = date("W",mktime(0,0,0,date("m"),date("d")+$przelicznik,date("Y"))); // numer tygodnia.
                                                        $data_czytelna =  date("Y.m.d", mktime(0,0,0,1,1+($data*7)-6,date("Y"))); // poniedziałek
                                                        $data_czytelna2 =  date("Y.m.d", mktime(0,0,0,1,(1+($data*7)-4)+4,date("Y"))); // niedziela
                                                        @endphp
                                                        @if (isset($number_of_week))
                                                            @if ($data == $number_of_week)
                                                                <option value={{$data}} selected>{{$data_czytelna.' -> '.$data_czytelna2}}</option>;
                                                            @else
                                                                <option value={{$data}}>{{$data_czytelna.' -> '.$data_czytelna2}}</option>;
                                                            @endif
                                                        @else
                                                            @if ($data == date("W"))
                                                                <option value={{$data}} selected>{{$data_czytelna.' -> '.$data_czytelna2}}</option>;
                                                            @else
                                                                <option value={{$data}}>{{$data_czytelna.' -> '.$data_czytelna2}}</option>;
                                                            @endif
                                                        @endif
                                                    @endfor
                                                </select></br>
                                                <button type="submit" class="btn btn-primary" name="show_week_grafik_send" style="font-size:18px; width:100%;">Wyszukaj</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="well">
                                            <h1 style ="font-family: 'bebas_neueregular',sans-serif; margin-top:0px;text-shadow: 2px 2px 2px rgba(150, 150, 150, 0.8); font-size:25px;">Kolory:</h1>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td align="center" style="width: 40px;background-color:#ff7070;"><b></b></td>
                                                    <td align="center"><b>Zbyt mało osób</b></td>
                                                </tr>
                                                <tr>
                                                    <td align="center" style="width: 40px;background-color:#ffee29;"><b></b></td>
                                                    <td align="center"><b>Za dużo osób</b></td>
                                                </tr>
                                            </table>
                                        </div>
                                </div>
                                    <div class="col-md-12">
                                        @if (isset($number_of_week))
                                            <table class="table table-bordered">
                                                <div class="panel-heading" style="border:1px solid #d3d3d3;"><h4><b>Analiza Grafik Plan</b></h4></div>
                                                <tr>
                                                    <td align="center"><b>Godzina</b></td>
                                                    @for($i=8;$i<=20;$i++)
                                                    <td align="center"><b>{{$i}}</b></td>
                                                    @endfor
                                                </tr>
                                                @for($i=0;$i<7;$i++)
                                                    <tr>
                                                    <td align="center"><b>Pon</b></td>
                                                    @for($j=8;$j<=20;$j++)
                                                        <td align="center"><b>{{$number_of_week}}</b></td>
                                                    @endfor
                                                    </tr>
                                                @endfor
                                            </table>

                                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Imię</th>
                                                    <th>Nazwisko</th>
                                                    <th>Telefon</th>
                                                    <th>Grafik</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
<script>
    moment().format();
    function format ( d ) {
        var week_array = ['Pon','Wt','Śr','Czw','Pt','Sob','Nie'];
        var day = $("#week_text option:selected").text();
        day = day.split(" ");
        var start_date = moment(day[0], "YYYY.MM.DD");
        var table = '<table class="table-bordered">'+
            '<thead>' +
            '<tr>';
            for(var i=0;i<7;i++)
            {
                //table+='<p><input type="checkbox" name="czw_checkbox_'.$key_id.'" id="checkczw-'.$key_id.'" '.$czw_checked.'> Wolne</p>';
                if(i==0)
                    table+='<th>'+week_array[i]+'. '+start_date.add(0, 'days').format('DD-MM')+'</th>';
                else
                    table+='<th>'+week_array[i]+'. '+start_date.add(1, 'days').format('DD-MM')+'</th>';
            }
            table += '</tr>'+
            '</thead>' +
            '<tbody>' +
            '</tbody>';
        return table+'</table>';

    }
    $(document).ready(function() {
        var year = $("#week_text option:selected").text();
        var week_number = $("select[name='show_schedule']").val();
        year = year.split(".");
        var start_date = moment(year).add(week_number, 'weeks').startOf('week').format('DD MM YYYY');
        var stop_date =  moment(year).add(week_number, 'weeks').startOf('isoweek').format('DD MM YYYY');

        table = $('#datatable').DataTable({
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "drawCallback": function (settings) {
            },
            "ajax": {
                'url': "{{ route('api.datatableShowUserSchedule') }}",
                'type': 'POST',
                'data': function (d) {
                    d.year = year[0];
                },
                'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            }, "columns": [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {"data": "user_first_name", "name": "first_name"},
                {"data": "user_last_name", "name": "last_name"},
                {"data": "user_phone", "name": "phone"},
                {
                    "data": function (data, type, dataToSet) {
                        if (data.id == null)
                            return 'Nie';
                        else return 'Tak'
                    }, "name": "id"
                },
            ],
            select: true
        });


        $('#datatable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );
    });

</script>
@endsection
