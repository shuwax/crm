{{--/*--}}
{{--*@category: CRM,--}}
{{--*@info: This view shows info list of client routes,--}}
{{--*@controller: CrmRouteController,--}}
{{--*@methods: , --}}
{{--*/--}}
@extends('layouts.main')
@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>
        #fullscreen {
            margin-top: 1.75em;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="alert gray-nav ">Baza miejscowości/hotele</div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Panel z informacjami
        </div>
        <div class="alert alert-info">
            Moduł <strong>Baza miejscowości/hotele</strong> prezentuje informacje o miastach i hotelach przypisanych do tras dla poszczególnych klientów.
            Lista <strong>klienci </strong> jest wielokrotnego wyboru. Tryb pełnoekranowy, można go opuścić naciskająć przycisk "ESC" na klawiaturze.
        </div>

            <div class="row">
                <div class="col-md-12">
                        <div class="form-group">
                            <label for="showWithoutHotel">Pokazy bez hoteli</label>
                            <input type="checkbox" style="display:inline-block" id="showWithoutHotel">
                        </div>
                </div>
                <div class="col-md-3">
                    <label for="date_start">Data początkowa:</label>
                    <input type="date" id="date_start" style="width: 100%;" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="date_stop">Data końcowa:</label>
                    <input type="date" id="date_stop" style="width: 100%;" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="clients">Klienci</label>
                    <select class="selectpicker form-control" id="clients" name="link_privilages[]" title="Brak wybranych użytkowników" multiple data-actions-box="true">
                        @if(isset($clients))
                            @foreach($clients as $client)
                                <option value="{{$client->id}}">{{$client->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="fullscreen" class="btn btn-info"><span class="glyphicon glyphicon-fullscreen"></span> Tryb pełnoekranowy</button>
                </div>
            </div>

            <div class="panel-body">

                <table id="datatable" class="thead-inverse table row-border table-striped">
                    <thead>
                    <tr>
                        <th>Klient</th>
                        <th>Tydzień</th>
                        <th>Data</th>
                        <th>Miasto</th>
                        <th>Hotel</th>
                        <th>Os. rezerwująca</th>
                        <th>Cena za salę</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

    </div>
@endsection
@section('script')
    <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script>
        let datatableHeight = '45vh'; //this variable defines height of table
        let fullscreen = document.getElementById('fullscreen'); // fullscreen button
        const legendBar = document.querySelector('.alert-info');

        const now = new Date();
        const day = ("0" + now.getDate()).slice(-2);
        const month = ("0" + (now.getMonth() + 1)).slice(-2);
        const today = now.getFullYear() + "-" + (month) + "-" + (day);
        const firstDayOfThisMonth = now.getFullYear() + "-" + (month) + "-01";
        const showWithoutHotelInput = $('#showWithoutHotel');
        /*Activation selectpicker and datetimepicker framework*/
        (function initial() {
            $('.selectpicker').selectpicker({
                selectAllText: 'Zaznacz wszystkie',
                deselectAllText: 'Odznacz wszystkie'
            });

            $('#date_start').val(firstDayOfThisMonth);
            $('#date_stop').val(today);

        })();

        let table = $('#datatable').DataTable({
            autoWidth: true,
            processing: true,
            serverSide: true,
            scrollY: datatableHeight,
            ajax: {
                url: "{{route('api.datatableClientRouteInfoAjax')}}",
                type: 'POST',
                data: function (d) {
                    d.dateStop = $('#date_stop').val();
                    d.dateStart = $('#date_start').val();
                    d.clients = $('#clients').val();
                    d.showWithoutHotelInput = showWithoutHotelInput.prop('checked');
                },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
            },
            columns:[
                {data: 'clientName','orderable':false},
                {data: 'weekOfYear','orderable':false},
                {data: 'date','orderable':false},
                {data: 'cityName','orderable':false},
                {data: 'hotelName','orderable':false},
                {data: 'userReservation','orderable':false},
                {data: 'hotelPrice','orderable':false}
            ]
        });

        $('#menu-toggle').change(()=>{
            table.columns.adjust().draw();
        });
        showWithoutHotelInput.change(function(e){
            table.ajax.reload();
        });
        /**
         * This event listener reloads table after changing start or stop date
         */
        $('#date_start, #date_stop, #clients').on('change', function(e) {
            table.ajax.reload();
        });

        /**
         * This event listener function allow fullscreen with proper table height.
         */
        function fullScreenHandler(e) {
            const elem = document.querySelector('.panel-default');

            if(elem.mozRequestFullScreen) {
                datatableHeight = '65vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'none';
                elem.mozRequestFullScreen();
            }
            if(elem.webkitRequestFullscreen) {
                datatableHeight = '65vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'none';
                elem.webkitRequestFullscreen();
            }

            if(elem.msRequestFullscreen) {
                datatableHeight = '65vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'none';
                elem.msRequestFullscreen();
            }

        }

        /**
         * This event listeners adjust height of table after closing full screen mode.
         */
        document.addEventListener("mozfullscreenchange", function( ev ) {
            if ( document.mozFullScreenElement === null ) {
                datatableHeight = '45vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'block';
            }
        });

        document.addEventListener("webkitfullscreenchange", function( ev ) {
            if ( document.webkitFullscreenElement === null ) {
                datatableHeight = '45vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'block';
            }
        });

        document.addEventListener("MSFullscreenChange", function( ev ) {
            if ( document.msFullscreenElement === null ) {
                datatableHeight = '45vh';
                $('div.dataTables_scrollBody').css('height',datatableHeight);
                legendBar.style.display = 'block';
            }
        });

        fullscreen.addEventListener('click', fullScreenHandler);
    </script>
@endsection