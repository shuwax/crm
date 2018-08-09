{{--/*--}}
{{--*@category: CRM,--}}
{{--*@info: This view allows user to edit given hotel (DB table: "hotels"),--}}
{{--*@controller: CrmRouteController,--}}
{{--*@methods: hotelGet, hotelPost--}}
{{--*/--}}

@extends('layouts.main')
@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
    <link href="{{ asset('/css/fixedColumns.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/fixedHeader.dataTables.min.css')}}" rel="stylesheet">
@endsection
@section('content')

    <style>

        #datatable td {
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer */
            -webkit-user-select: none; /* Chrome, Safari, and Opera */
            -webkit-touch-callout: none; /* Disable Android and iOS callouts*/
        }

        #float {
            position: fixed;
            top: 3em;
            right: 2em;
            z-index: 100;
        }

        .heading-container {
            text-align: center;
            font-size: 2em;
            margin: 1em;
            font-weight: bold;
            box-shadow: 0 1px 15px 1px rgba(39, 39, 39, .1);
            padding-top: 1em;
            padding-bottom: 1em;
        }

        .form-container {
            box-shadow: 0 1px 15px 1px rgba(39, 39, 39, .1);
            padding-top: 1em;
            padding-bottom: 1em;
            margin: 1em;
        }

        .colorCell {
            background-color: #bcb7ff !important;
        }

        .selectedCell {
            border-color: blue !important;
            border-style: dashed !important;
            border-width: 1px !important;
        }

        .selectedRowDay{
            background: #bcb7ff !important;
        }
        .alert-info {
            font-size: 1.2em;
        }
<<<<<<< HEAD
         .limitSection, .limitInput, .separate {
             margin-top: 2%
         }
        .separate{
            margin-bottom: 14px
        }
        .dropdown-menu{
            left: 0%;
        }


=======
        .thisDay{
            background: #fffc8b !important;
        }
        .dropdown-menu {
            left: 0px;
        }

>>>>>>> upstream/develop
    </style>

    {{--Header page --}}
            <div class="page-header">
                <div class="alert gray-nav ">Planowanie Wyprzedzenia</div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Planowanie wyprzedzenia
                </div>
                <div class="alert alert-info">
                    Moduł planowanie wyprzedzenia zawiera tabelę pokazującą różnicę pomiędzy <i>zaproszeniami live</i> a ustalonymi <i>limitami</i> z zakładki <strong>informacje o kampaniach</strong> dla poszczególnych oddziałów dla określonych dni.
                    Kolumny można sumować w następujący sposób: Po pierwsze należy zaznaczyć pierwszą komórkę z sumy, przytrzymać lewy shift a następnie kliknąć ostatnią komórkę sumy.
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date" class="myLabel">Data początkowa:</label>
                                <div class="input-group date form_date col-md-5" data-date=""
                                     data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" name="date_start" id="date_start" type="text"
                                           value="{{date("Y-m-d")}}">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_stop" class="myLabel">Data końcowa:</label>
                                <div class="input-group date form_date col-md-5" data-date=""
                                     data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" name="date_stop" id="date_stop" type="text"
                                           value="{{date("Y-m-d")}}">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <button class="btn btn-default simulationClientLimit"  data-toggle="modal" data-target="#modalSimulationClient" >Symulacja Klienta(Edycja Limitów)</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <button class="btn btn-default simulationNewClient"  data-toggle="modal"  data-target="#modalSimulationClient" >Symulacja Klienta(Nowy Klient)</button>
                            </div>
                        </div>
                    </div>
<<<<<<< HEAD


                    <div id="modalSimulationClient" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg" style="width: 90%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title" id="modal_title">Sekcja symulatcji<span id="modal_category"></span></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Symalacja limitów klienta
                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-12 ">
                                                <div class="changeClientLimit" style="margin-bottom: 1%">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Wybierz klienta</span>
                                                            <select class="form-control selectedClientToChangeLimit selectpicker" title="Wybierz klientów..."
                                                                    data-live-search=”true” data-width="100%" multiple="multiple"
                                                            >
                                                                <option>1</option>
                                                                <option>2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Wybierz datę zmiany limitów</span>
                                                            <input  class="form-control dateStartClientToChangeLimit" name="dateStartClientToChangeLimit">
                                                        </div>
                                                    </div>
                                                    <div class="limitSection">
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1">Limit dla pokazów pełnych (3)</label>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #1</span>
                                                                <input class="form-control AllFirstLimit" name="AllFirstLimit">
                                                            </div>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #2</span>
                                                                <input class="form-control AllSecondLimit" name="AllSecondLimit">
                                                            </div>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #3</span>
                                                                <input class="form-control AllThirdLimit" name="AllThirdLimit">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1">Limit dla pokazów godzinowych</label>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #1</span>
                                                                <input class="form-control OnlyFirstLimit" name="OnlyFirstLimit">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button class="btn btn-default separate" class="AddNewSimulation">
                                                                <span class="glyphicon glyphicon-plus"></span> <span>Dodaj Kolejną symulacje</span>
                                                            </button>
                                                            <button class="btn btn-danger separate" class="RemoveSimulation">
                                                                <span class="glyphicon glyphicon-minus"></span> <span>Usuń Symulację</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12" style="border-bottom: 2px;background: black;">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="changeClientLimit" style="margin-bottom: 1%">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Wybierz klienta</span>
                                                            <select class="form-control selectedClientToChangeLimit">
                                                                <option>1</option>
                                                                <option>2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">Wybierz datę zmiany limitów</span>
                                                            <input  class="form-control dateStartClientToChangeLimit" name="dateStartClientToChangeLimit">
                                                        </div>
                                                    </div>
                                                    <div class="limitSection">
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1">Limit dla pokazów pełnych (3)</label>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #1</span>
                                                                <input class="form-control AllFirstLimit" name="AllFirstLimit">
                                                            </div>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #2</span>
                                                                <input class="form-control AllSecondLimit" name="AllSecondLimit">
                                                            </div>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #3</span>
                                                                <input class="form-control AllThirdLimit" name="AllThirdLimit">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="exampleInputEmail1">Limit dla pokazów godzinowych</label>
                                                            <div class="input-group limitInput">
                                                                <span class="input-group-addon">Limit #1</span>
                                                                <input class="form-control OnlyFirstLimit" name="OnlyFirstLimit">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button class="btn btn-default separate" class="AddNewSimulation">
                                                                <span class="glyphicon glyphicon-plus"></span> <span>Dodaj Kolejną symulacje</span>
                                                            </button>

                                                            <button class="btn btn-danger separate" class="RemoveSimulation">
                                                                <span class="glyphicon glyphicon-minus"></span> <span>Usuń Symulację</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>





                                        </div>
=======
                    <div class="row">
                        <div class="col-md-4 buttonSection">
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-4">
                            <button class="btn btn-block btn-default" id="resultsSimulationButton">Symulacje wyników <span class="glyphicon glyphicon-chevron-down"></span></button>
                            <div class="simulationSection well well-sm">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <select id="simulation" class="selectpicker form-control show-tick" title="Wybierz symulację wyników">
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Dni wolne</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button id="simulationButton" class="btn btn-block btn-primary">Symuluj</button>
>>>>>>> upstream/develop
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="datatable" class="table table-striped row-border" style="width:100%;">
                        <thead>
                        <tr>
                            <th>Tydzien</th>
                            <th>Dzień</th>
                            <th>Data</th>
                            @foreach($departmentInfo as $item)
                                <th>{{$item->name2.' '.$item->name}}</th>
                            @endforeach
                            <th>Suma</th>
                            <th>Podział</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="row">
                    </div>
                </div>
            </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('/js/fixedColumns.dataTables.min.js')}}"></script>
    <script src="{{ asset('/js/dataTables.fixedHeader.min.js')}}"></script>
    <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{ asset('/js/moment.js')}}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            (function activateDatepicker() {
                $('.form_date').datetimepicker({
                    language: 'pl',
                    autoclose: 1,
                    minView: 2,
                    pickTime: false,
                });
            })();

            $('.selectpicker').selectpicker({
                style: 'btn-info',
                size: 4
            });


            /********** GLOBAL VARIABLES ***********/
            let elementsToSum = {
                firstElement: {trId: null, tdId: null},
                lastElement: {trId: null, tdId: null}
            };

            let sumOfSelectedCells = 0;

            let selectedRowDays = [];

            const firstDayOfThisMonth = moment().format('YYYY-MM')+'-01';
            const today = moment().format('YYYY-MM-DD');
            const startDate = moment().add(-1,'w').format('YYYY-MM-DD');
            const stopDate = moment().add(3,'w').format('YYYY-MM-DD');
            /*******END OF GLOBAL VARIABLES*********/

            $('#date_start').val(startDate);
            $('#date_stop').val(stopDate);

            /*********************DataTable FUNCTUONS****************************/
            let aheadPlanningData = {
                limitSimulation: null,
                newClientSimulation: null,
                data: {
                    aheadPlaning: null,
                    departmentsInvitationsAverages: null
                },
                getData: function (startDate, stopDate) {
                    let deffered = $.Deferred();
                    let obj = this;
                    $.ajax({
                        url: "{{ route('api.getaHeadPlanningInfo') }}",
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: {
                            startDate: startDate,
                            stopDate: stopDate,
                            limitSimulation: obj.limitSimulation,
                            newClientSimulation: obj.newClientSimulation,
                            departmentsInvitationsAverages: obj.data.departmentsInvitationsAverages
                        },
                        success: function (response) {
                            obj.data.aheadPlaning = response.aheadPlanningData;
                            obj.data.departmentsInvitationsAverages = response.departmentsInvitationsAveragesData;
                            deffered.resolve(obj.data.aheadPlaning);
                        },
                        error: function (jqXHR, textStatus, thrownError) {
                            console.log(jqXHR);
                            console.log('textStatus: ' + textStatus);
                            console.log('hrownError: ' + thrownError);
                            swal({
                                type: 'error',
                                title: 'Błąd ' + jqXHR.status,
                                text: 'Wystąpił błąd: ' + thrownError+' "'+jqXHR.responseJSON.message+'"',
                            });
                            deffered.reject();
                        }
                    });
                    return deffered.promise();
                }
            };

            $('#datatable_processing').show();
            aheadPlanningData.getData($('#date_start').val(),$("#date_stop").val()).done(function (response) {
                aheadPlaningTable.setTableData(response);
                $('#datatable_processing').hide();
            });

            let aheadPlaningTable = {
                dataTable:  $('#datatable').DataTable({
                    //serverSide: true,
                    scrollY: '60vh',
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                    processing: true,
                    fixedColumns: {
                        leftColumns: 3
                    },
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                    },
                    fnRowCallback:  function( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
                        if(aData.day === today){
                            for( i = 0 ; i< 3; i++){
                                $($(nRow).children()[i]).addClass('thisDay');
                            }
                        }
                    },
                    fnDrawCallback: function () {
                        elementsToSum.firstElement.tdId = null;
                        elementsToSum.firstElement.trId = null;
                        elementsToSum.lastElement.tdId = null;
                        elementsToSum.lastElement.trId = null;
                        const allTd = document.querySelectorAll('td');
                        allTd.forEach(cell => {
                            if(cell.textContent == '0') {
                                cell.style.background = "#b9f4b7";
                            }
                        })
                    }, order: [[2,'asc']]
                    ,"columns": [
                        {"data": "numberOfWeek", orderable: false},
                        {"data": "dayName", orderable: false},
                        {"data": "day", orderable: false},
                            @foreach($departmentInfo as $item)
                        {
                            "data": `{{$item->name2}}`, "searchable": false, orderable: false
                        },
                            @endforeach
                        {
                            "data": "totalScore", orderable: false
                        },
                        {
                            "data": function (data, type, dataToSet) {
                                return data.allSet
                            }, "name": "allSet", orderable: false
                        }
                    ]
                }),

                setTableData: function (data){
                    let table = this.dataTable;
                    table.clear();
                    if($.isArray(data)) {
                        $.each(data, function (index, row) {
                            table.row.add(row).draw();
                        });
                    }
                }
            };


            /*********************EVENT LISTENERS FUNCTIONS****************************/


            $('#date_start, #date_stop').on('change', function () {
                $('#datatable_processing').show();
                aheadPlaningTable.dataTable.clear();
                aheadPlaningTable.dataTable.draw();
                aheadPlanningData.getData($('#date_start').val(),$("#date_stop").val()).done(function (response) {
                    aheadPlaningTable.setTableData(response);
                    $('#datatable_processing').hide();
                });
                //table.ajax.reload();
            });

            /**
             * This event listener finds row and column of clicked 'td' element and colors selected cells
             */
            $('#datatable').click((e) => {
                addOrRemoveClickedElement(e);

                if (elementsToSum.firstElement.tdId !== null
                    && (elementsToSum.firstElement.trId !== elementsToSum.lastElement.trId
                        || elementsToSum.firstElement.tdId !== elementsToSum.lastElement.tdId)) {
                    $('#sumButton').removeAttr('disabled');
                } else if (!$('#sumButton').prop('disabled'))
                    $('#sumButton').prop('disabled', true);
            });

            /*********************END EVENT LISTENERS FUNCTIONS****************************/

            /**
             * This function saves clicked cell positions (tr and td id's).
             * First cell is saved after click, second is saved after click + shift (if first is saved)
             */
            function addOrRemoveClickedElement(e) {
                let clickedElement = $(e.target);
                let trElement = clickedElement.parent();
                let tableElement = trElement.parent();
                let clickedElementTdIndex = trElement.children().index(clickedElement);
                let clickedElementTrIndex = tableElement.children().index(trElement);
                if (clickedElement.is('td') && clickedElementTdIndex >= 3 && clickedElementTdIndex < trElement.children().length - 1){
                    if (e.shiftKey) {
                        if (elementsToSum.firstElement.tdId !== null) {
                            elementsToSum.lastElement.tdId = elementsToSum.lastElement.tdId; //clickedElementTdIndex;
                            elementsToSum.lastElement.trId = clickedElementTrIndex;
                            colorCellsRectangle(elementsToSum);
                            $.notify({
                                title: $($('#datatable tr').first().children().get(elementsToSum.firstElement.tdId)).text() + ': ',
                                message: '<strong>' + sumOfSelectedCells + '</strong>'
                            }, {
                                type: 'info',
                                mouse_over: 'pause',
                                placement: {
                                    from: "bottom",
                                    align: "right"
                                },
                            });
                        }
                    } else {
                        $('.selectedCell').removeClass('selectedCell');
                        clickedElement.addClass('selectedCell');
                        elementsToSum.firstElement.tdId = clickedElementTdIndex;
                        elementsToSum.firstElement.trId = clickedElementTrIndex;
                        elementsToSum.lastElement.tdId = clickedElementTdIndex;
                        elementsToSum.lastElement.trId = clickedElementTrIndex;
                        if (clickedElement.is('.colorCell')) {
                            $('.selectedCell').removeClass('selectedCell');
                            $('.colorCell').removeClass('colorCell');
                            elementsToSum.firstElement.tdId = null;
                            elementsToSum.firstElement.trId = null;
                            elementsToSum.lastElement.tdId = null;
                            elementsToSum.lastElement.trId = null;
                        } else
                            colorCellsRectangle(elementsToSum);
                    }
                }else if(clickedElement.is('td') && clickedElementTdIndex < 3){
                    let selectedSimulationsIndex = $('#simulation').val();
                    if(simulations[selectedSimulationsIndex]){
                        if($.inArray(clickedElementTrIndex, selectedRowDays) >= 0){
                            selectedRowDays.splice(selectedRowDays.indexOf(clickedElementTrIndex),1);
                            console.log('remove');
                            console.log(selectedRowDays);
                        }else if(selectedRowDays.length < simulations[selectedSimulationsIndex].availableSelectedDays){
                            selectedRowDays.push(clickedElementTrIndex);
                            console.log('add');
                            console.log(selectedRowDays);
                        }
                    }
                    colorSelectedRowDays();
                }

            }

            function colorSelectedRowDays() {
                let colorClassSelectedRowDay = 'selectedRowDay';
                $('.'+colorClassSelectedRowDay).removeClass(colorClassSelectedRowDay);
                let tableTr = $('.DTFC_LeftBodyWrapper .table.dataTable tbody').children();
                $.each(selectedRowDays, function (index, item) {
                    let tds = $(tableTr[item]).children();
                    for(i = 0; i < 3; i++){
                        $(tds[i]).addClass(colorClassSelectedRowDay);
                    }
                });
            }

            /**
             * This function add class 'colorCell' to cells in array of cells appointed by two corner cells.
             * Elements is a object that has positions of first and last cell (tr and td id's)
             */
            function colorCellsRectangle(elements) {
                $('.colorCell').removeClass('colorCell');
                trElements = $('#datatable tr');

                //selecting left top and right bottom cells
                firstElementTrId = elements.firstElement.trId;
                firstElementTdId = elements.firstElement.tdId;
                lastElementTrId = elements.lastElement.trId;
                lastElementTdId = elements.lastElement.tdId;

                //if selected cells are not left top and right bottom cells, switch values properly
                //firstElement - left top corner, lastElement - right bottom corner
                if (firstElementTrId > lastElementTrId) {               //if first element is below last element
                    firstElementTrId = elementsToSum.lastElement.trId;
                    lastElementTrId = elementsToSum.firstElement.trId;
                    if (firstElementTdId > lastElementTdId) {           //if first element is on right side of last element
                        firstElementTdId = elementsToSum.lastElement.tdId;
                        lastElementTdId = elementsToSum.firstElement.tdId;
                    }
                } else if (firstElementTdId > lastElementTdId) {               //if first element is on right side of last element
                    firstElementTdId = elementsToSum.lastElement.tdId;
                    lastElementTdId = elementsToSum.firstElement.tdId;
                }

                sumOfSelectedCells = 0;
                //add class colorCell to all cell beetween first and last element
                for (var i = firstElementTrId; i <= lastElementTrId; i++) {
                    tdElements = $(trElements.get(i + 1)).children();
                    for (var j = firstElementTdId; j <= lastElementTdId; j++) {
                        $(tdElements.get(j)).addClass('colorCell');
                        sumOfSelectedCells += parseInt($(tdElements.get(j)).text());
                    }
                }

                //rightTopCell = $($(trElements.get(firstElementTrId + 1)).children().get(lastElementTdId));

            }

            //  simulations template
            function Simulation( name, availableSelectedDays, simulateCallback, validateCallback) {
                return {
                    name: name,
                    availableSelectedDays: availableSelectedDays,
                    simulate: function () {
                        simulateCallback();
                    },
                    validate: function () {
                        validateCallback();
                    }
                };
            }

            /*  ---------------------- creating available simulations ---------------------- */
            let simulations = [];

            simulations.push(Simulation(
                'Przewidywanie wyprzedzenia na wybrany dzień',
                1,
                function (){

                },
                function () {

                }
            ));
            simulations.push(Simulation(
                'Wyliczenie średniej zaproszeń dla oddziałów',
                2,
                function () {

                },
                function () {

                }
            ));
            /*  ---------------------- end creating available simulations ---------------------- */

            // fill simulation select with created simulation objects
            $.each(simulations,function(index, item){
                $('#simulation').append($('<option>', {
                    value: index,
                    text: item.name
                }));
            });

            $('#simulation').change(function () {
               selectedRowDays = [];
               colorSelectedRowDays();
            });

            $('.simulationSection').hide();
            $('#resultsSimulationButton').click(function () {
               $('.simulationSection').slideToggle('slow', function () {

                   if($('.simulationSection').is(':hidden')){
                       $('#resultsSimulationButton span').removeClass().addClass('glyphicon glyphicon-chevron-down');
                   }else{
                       $('#resultsSimulationButton span').removeClass().addClass('glyphicon glyphicon-chevron-up');
                   }
               });
            });

            $('#simulationButton').click(function () {
                let simulationIndex = $('#simulation').val();
                if(simulations[simulationIndex]){
                    simulations[simulationIndex].validate();
                    simulations[simulationIndex].simulate();
                }else{
                    swal('Wybierz symulację');
                }
            });
        });
    </script>
@endsection
