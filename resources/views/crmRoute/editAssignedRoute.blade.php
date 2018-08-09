{{--/*--}}
{{--*@category: CRM,--}}
{{--*@info: This view is responsible for connecting clients with routes--}}
{{--*@database tables: voivodeship, city, routes_info,--}}
{{--*@controller: CrmRouteController,--}}
{{--*@methods: index, getSelectedRoute--}}
{{--*/--}}


@extends('layouts.main')
@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
@endsection
@section('content')
    <style>
        .client-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .client-container {
            background-color: white;
            padding: 2em;
            box-shadow: 0 1px 15px 1px rgba(39, 39, 39, .1);
            border: 0;
            border-radius: .1875rem;
            margin: 1em;

            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 90%;
            max-width: 90%;

        }

        .singleDayContainer, .summaryButtonContainer {
            background-color: white;
            padding: 2em;
            box-shadow: 0 1px 15px 1px gray;
            border: 0;
            border-radius: .1875rem;
            margin: 1em;
        }

        .singleShowContainer {
            background-color: white;
            padding: 2em;
            box-shadow: 0 1px 15px 1px rgba(39, 39, 39, .1);
            border: 0;
            border-radius: .1875rem;
            margin: 1em;
            position: relative;
        }

        .singleShowHeader {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            padding-bottom: .5em;
        }

        .remove-button-container{
            position: absolute;
            top: 1em;
            right: 1em;
        }

        .glyphicon-remove {
            font-size: 2em;
            transition: all 0.8s ease-in-out;
            color: red;
        }

        .glyphicon-remove:hover {
            transform-origin: center;
            transform: scale(1.2) rotate(180deg);
            cursor: pointer;
        }

        header {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
        }

        .check {
            background: #B0BED9 !important;
        }

        .first-show-date {
            margin-top: 1em;
        }

        .show-cities-statistics {
            padding-top: 1.65em;
            font-size: 1.3em;
        }

        .show-cities-statistics:hover {
            cursor: pointer;
            color: blue;
        }

    </style>

    {{--Header page --}}
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <div class="alert gray-nav ">Edycja trasy przypisanej do klienta</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Edycja trasy przypisanej do klienta
                </div>
                <div class="panel-body">
                    <div class="row">
                        @if(Session::has('adnotation'))
                            <div class="alert alert-warning addnotation-container"
                                 style="font-size:1.2em;font-weight:bold;text-align:center;">
                                {{Session::get('adnotation')}}

                                <script>
                                    const addnotationContainer = document.querySelector('.addnotation-container');
                                    let redirectButton2 = document.createElement('button');
                                    redirectButton2.classList.add('btn');
                                    redirectButton2.classList.add('btn-primary');
                                    redirectButton2.innerHTML = '<span class="glyphicon glyphicon-repeat"></span> Powrót';
                                    addnotationContainer.appendChild(redirectButton2);

                                    redirectButton2.addEventListener('click', (e) => {
                                        window.location.href = `{{URL::to('/showClientRoutes')}}`;
                                    });
                                </script>
                            </div>
                            {{Session::forget('adnotation')}}
                        @endif
                    </div>
                    <div class="client-wrapper">
                        <div class="client-container">
                            <header>Klient</header>
                            <div class="alert alert-info">
                                Wybierz klienta z listy. Jeśli nie ma klienta na liście, należy przejść do zakładki
                                <strong><a href="{{URL::to('/clientPanel')}}"> lista klientów</a></strong> i go dodać.
                                Wiersze zaznaczone na czerwono wskazują na klienta, który został wyłączony.
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="table_client" class="table table-striped thead-inverse">
                                        <thead>
                                        <tr>
                                            <th>Nazwa</th>
                                            <th>Priorytet</th>
                                            <th>Typ</th>
                                            <th style="text-align: center;">Akcja</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="client-wrapper">
                        <div class="client-container">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label>Klient:</label>
                                    <label id="client_choice_name"></label>
                                </div>
                                <div class="col-md-4">
                                    <label>Priorytet:</label>
                                    <label id="client_choice_priority"></label>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="client_choice_type">Typ:</label>
                                        <select id="client_choice_type" class="form-control">
                                            <option value="0">Wybierz</option>
                                            <option value="1">Badania</option>
                                            <option value="2">Wysyłka</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="client-wrapper">
                        <div class="client-container">
                            <div class="alert alert-info">
                                Wybierz szablon trasy z listy. Jeśli nie ma odpowiedniej trasy na liście, stwórz ją
                                naciskając na przycisk <strong>Dodaj trasę ręcznie</strong> <br>
                                Wiersze pokolorowane na czerwono wskazują na szablon trasy, w którym co najmniej 1
                                miasto przekroczyło karencję, względem
                                daty pierwszego pokazu.
                            </div>
                            <div class="col-md-12">
                                <div class="form-group first-show-date">
                                    <label class="myLabel">Data pierwszego pokazu:</label>
                                    <div class="input-group date form_date col-md-5" data-date-calendarWeeks="true"
                                         data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                        <input class="form-control first-show-date-input" name="date" id="date"
                                               type="text" value="{{date("Y-m-d")}}">
                                        <span class="input-group-addon"><span
                                                    class="glyphicon glyphicon-th"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" id="add-new-route" style="margin-bottom: 14px;"><span
                                            class="glyphicon glyphicon-plus"></span>Dodaj trasę ręcznie
                                </button>
                            </div>
                            <table id="datatable" class="thead-inverse table table-striped table-bordered"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Nazwa</th>
                                    <th>Akcja</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="client-container route-here">

                        </div>
                    </div>
                    <div class="client-wrapper">
                        <div class="client-container">
                            <button class="btn btn-primary" style="margin-top:1em;font-size:1.1em;font-weight:bold;"
                                    id="redirect"><span class='glyphicon glyphicon-repeat'></span> Powrót
                            </button>
                            <button class="btn btn-success"
                                    style="margin-top:1em;margin-bottom:1em;font-size:1.1em;font-weight:bold;"
                                    id="save"><span class='glyphicon glyphicon-save'></span> Zapisz
                            </button>
                            <button class="btn btn-danger" id="remove-route" style="margin-bottom:1em;font-size:1.1em;font-weight:bold;">Usuń trasę</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="showRecords" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Wykorzystanie miasta</h4>
                </div>
                <div class="modal2-body">
                    <div class="alert alert-danger">Ładowanie danych..</div>
                </div>
                <div class="modal-footer">
                    <button id="modal-close-button" type="button" class="btn btn-default" data-dismiss="modal">Close
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script>

        $(document).ready(function () {
            let globalDateIndicator = null;
            let globalSwalFlag = false;


            function activateDatepicker() {
                $('.form_date').datetimepicker({
                    language: 'pl',
                    autoclose: 1,
                    minView: 2,
                    calendarWeeks: 'true',
                    pickTime: false
                });
            }

            activateDatepicker();

            $('#client_choice_type').attr('disabled', true).val(0);

            let today = new Date();
            let dd = today.getDate();
            let mm = today.getMonth() + 1; //January is 0!

            let yyyy = today.getFullYear();
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            today = yyyy + '-' + mm + '-' + dd;

            let currentDate = today;

            let route_id = 0;
            let client_id = 0;

//*********************START CLIENT SECTON***************************

            let finalClientId = null; //This variable is needed for form submit

            function writeCheckedClientInfo() {
                let tr_line = document.getElementsByClassName('check')[0];
                let tr_line_name = tr_line.getElementsByClassName('client_name')[0].textContent;
                let tr_line_phone = tr_line.getElementsByClassName('client_priority')[0].textContent;
                let tr_line_type = tr_line.getElementsByClassName('client_type')[0].textContent;
                document.getElementById('client_choice_name').textContent = tr_line_name;
                document.getElementById('client_choice_priority').textContent = tr_line_phone;

                $('#client_choice_type').attr('disabled', false);
                if (tr_line_type == 'Badania') {
                    $('#client_choice_type').val(1);
                }
                else if (tr_line_type == 'Wysyłka') {
                    $('#client_choice_type').val(2);
                } else {
                    $('#client_choice_type').val(0);
                }
            }

            function clearCheckedClientInfo() {
                document.getElementById('client_choice_name').textContent = "";
                document.getElementById('client_choice_priority').textContent = "";
                $('#client_choice_type').attr('disabled', true).val(0);
            }

            /**
             * This function set client value and its type, using data from database about given client route
             */
            function setClientAndItsType() {
                @if(isset($client_route))
                const clientInfo = @json($client_route);
                @endif
                const clientTable = document.querySelector('#table_client');
                const allTr = clientTable.querySelectorAll('tr');
                const clientId = 'clientId_' + clientInfo.clientId;
                allTr.forEach(tableRow => {
                    if(tableRow.id == clientId) {
                        $(tableRow).trigger('click');
                    }
                });
                $('#client_choice_type').val(clientInfo.clientType);
            }


                let table_client = $('#table_client').DataTable({
                "autoWidth": true,
                scrollY: '40vh',
                "processing": true,
                "serverSide": true,
                "drawCallback": function (settings) {
                },
                "ajax": {
                    'url': "{{ route('api.getClient') }}",
                    'type': 'POST',
                    'data': function (d) {
                        // d.date_start = $('#date_start').val();
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                }, "rowCallback": function (row, data, index) {
                    if (data.status == 0) {
                        $(row).css('background', '#c500002e')
                    }
                    $(row).attr('id', "clientId_" + data.id);
                    return row;
                }, "fnDrawCallback": function (settings) {

                    //Zaznaczenie kolumny
                    $('#table_client tbody tr').on('click', function (e) {
                        if ($(this).hasClass('check')) {
                            $(this).removeClass('check');
                            $(this).find('.client_check').prop('checked', false);
                            client_id = 0;
                            finalClientId = 0;
                            clearCheckedClientInfo();
                        }
                        else {
                            if (e.target.dataset.noaction != 1) {
                                table_client.$('tr.check').removeClass('check');
                                $.each($('#table_client').find('.client_check'), function (item, val) {
                                    $(val).prop('checked', false);
                                });
                                $(this).addClass('check');
                                $(this).find('.client_check').prop('checked', true);
                                client_id = $(this).attr('id');
                                finalClientId = $(this).attr('id');
                                writeCheckedClientInfo();
                            }

                        }
                    });
                        setClientAndItsType();

                }, "columns": [
                    {"data": "name", "className": "client_name"},
                    {
                        "data": function (data, type, dataToSet) {
                            return data.priorityName;
                        }, "name": "priorityName", "className": "client_priority"
                    },
                    {"data": "type", "className": "client_type"},
                    {
                        "data": function (data, type, dataToSet) {
                            return ' <input style="display: inline-block;" type="checkbox" class="client_check"/>';
                        }, "orderable": false, "searchable": false
                    }
                ],
            });

            $('#menu-toggle').change(() => {
                table_client.columns.adjust().draw();
            });

//*********************END CLIENT SECTON***************************

//*********************START ROUTE(ROUND) SECTON***************************

            // Tabela zawierająca szablony tras
            let table = $('#datatable').DataTable({
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "drawCallback": function (settings) {
                },
                "ajax": {
                    'url': "{{ route('api.showRoutesAjax') }}",
                    'type': 'POST',
                    'data': function (d) {
                        d.date = currentDate;
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                }, "rowCallback": function (row, data, index) {
                    $(row).attr('id', 'route_' + data.id);
                    if (data.changeColor == '0') {
                        //Gdy trasa jest zajęta
                        $(row).css('background', '#c500002e');
                    }
                    return row;
                }, "fnDrawCallback": function (settings) {
                    $('#datatable tbody tr').on('click', function () {
                        // klikamy  na zaznaczony checkboxa trasy, oddzacz i usuń trase
                        if ($(this).hasClass('check')) {
                            $(this).removeClass('check');
                            $(this).find('.route_check').prop('checked', false);
                            route_id = 0; // przypisuje route_id = 0 gdy odznaczamy checkboxa
                            let placeToAppend = document.querySelector('.route-here');
                            placeToAppend.innerHTML = '';
                        }// klikamy  na odznaczony checkboxa trasy, zaznacz i dodaj trase
                        else {
                            table.$('tr.check').removeClass('check');
                            $.each($('#datatable').find('.route_check'), function (item, val) {
                                $(val).prop('checked', false);
                            });
                            $(this).addClass('check');
                            $(this).find('.route_check').prop('checked', true);
                            route_id = $(this).attr('id'); // przypisuje route_id gdy zaznaczamy checkboxa
                            let placeToAppend = document.querySelector('.route-here');
                            placeToAppend.innerHTML = '';

                            swal({
                                title: 'Ładowawnie...',
                                text: 'To może chwilę zająć',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                allowEnterKey: false,
                                onOpen: () => {
                                    swal.showLoading();
                                    // Pobranie informacji o zaznaczonej trasie
                                    $.ajax({
                                        type: "POST",
                                        async: false,
                                        url: '{{ route('api.getRouteTemplate') }}',
                                        data: {
                                            "route_id": route_id
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                            globalSwalFlag = true;
                                            let placeToAppend = document.querySelector('.route-here');
                                            placeToAppend.innerHTML = '';
                                            let dayFlag = null; //indices day change
                                            let dayBox = null;
                                            let dayContainer = null;
                                            for (let i = 0, respLength = response.length; i < respLength; i++) {
                                                if (i === 0) { //first iteration
                                                    console.log('warunek na raz');
                                                    dayBox = new DayBox();
                                                    dayBox.createDOMDayBox();
                                                    dayContainer = dayBox.getBox();
                                                    placeToAppend.insertAdjacentElement("beforeend", dayContainer);

                                                    let dateInfo = dayContainer.querySelector('.day-info');
                                                    let fulldate = dateInfo.textContent;
                                                    let correctDate = fulldate.substr(6); //YYYY-MM-DD

                                                    let formEl = new ShowBox();
                                                    formEl.addRemoveShowButton();
                                                    formEl.addDistanceCheckbox();
                                                    formEl.addNewShowButton();
                                                    formEl.createDOMBox(correctDate);
                                                    let firstFormDOM = formEl.getForm();

                                                    let citySelect = firstFormDOM.querySelector('.citySelect');
                                                    let voivodeSelect = firstFormDOM.querySelector('.voivodeSelect');
                                                    const voivodeId = response[i].voivodeId;
                                                    const cityId = response[i].cityId;

                                                    showWithoutDistanceAjax(voivodeId, citySelect, correctDate);

                                                    dayContainer.appendChild(firstFormDOM).scrollIntoView({behavior: "smooth"});

                                                    if(response[i].checkbox == 1) { //case when checkbox need to be checked
                                                        let checkboxElement = firstFormDOM.querySelector('.distance-checkbox');
                                                        if(!checkboxElement.checked) {
                                                            $(checkboxElement).trigger('click');
                                                        }
                                                    }

                                                    $(voivodeSelect).val(voivodeId).trigger('change');
                                                    $(citySelect).val(cityId).trigger('change');

                                                    dayFlag = response[i].day;

                                                    //Adding button section
                                                    let buttonSection = new ButtonBox();
                                                    buttonSection.appendAddNewDayButton();
                                                    let elButtonSection = buttonSection.getBox();

                                                    placeToAppend.insertAdjacentElement('beforeend', elButtonSection);
                                                }
                                                else if (dayFlag !== response[i].day && i !== 0) { //case when next container is in the next day
                                                    let addNewDayButton = $('#addNewDay');
                                                    addNewDayButton.trigger('click');

                                                    let allDayContainers2 = document.getElementsByClassName('singleDayContainer');
                                                    let lastDayContainer = allDayContainers2[allDayContainers2.length - 1];

                                                    let allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                                                    let lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                                                    let lastShowExistenceArr = checkingExistenceOfPrevAndNextContainers(lastShowContainer, 'singleShowContainer');

                                                    const citySelect = lastShowContainer.querySelector('.citySelect');
                                                    const voivodeSelect = lastShowContainer.querySelector('.voivodeSelect');
                                                    const voivodeId = response[i].voivodeId;
                                                    const cityId = response[i].cityId;

                                                    if(response[i].checkbox == 1) { //case when checkbox need to be checked
                                                        let prevShowContainer = lastShowExistenceArr[0];
                                                        let previousShowVoivodeSelect = prevShowContainer.querySelector('.voivodeSelect');
                                                        const previousShowVoivodeId = getSelectedValue(previousShowVoivodeSelect);
                                                        const previousShowCitySelect = prevShowContainer.querySelector('.citySelect');
                                                        const previousShowCityId = getSelectedValue(previousShowCitySelect);

                                                        let checkboxElement = lastShowContainer.querySelector('.distance-checkbox');
                                                        $(checkboxElement).trigger('click');
                                                        previousShowVoivodeSelect = prevShowContainer.querySelector('.voivodeSelect');
                                                        setOldValues(previousShowVoivodeSelect, previousShowVoivodeId, previousShowCitySelect, previousShowCityId);
                                                    }
                                                    $(voivodeSelect).val(voivodeId).trigger('change');
                                                    $(citySelect).val(cityId).trigger('change');
                                                    dayFlag = response[i].day;
                                                }
                                                else { // case when next show is in the same day container
                                                    let allDayContainers = document.getElementsByClassName('singleDayContainer');
                                                    let lastDayContainer = allDayContainers[allDayContainers.length - 1];
                                                    let allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                                                    let lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                                                    let addNextShowButton = lastShowContainer.querySelector('.addNewShowButton');
                                                    $(addNextShowButton).trigger('click');

                                                    //we need to select new container, which appear after triggering click
                                                    allDayContainers = document.getElementsByClassName('singleDayContainer');
                                                    lastDayContainer = allDayContainers[allDayContainers.length - 1];
                                                    allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                                                    lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                                                    if(response[i].checkbox == 1) { //case when checkbox need to be checked
                                                        const previousShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 2];
                                                        let previousShowVoivodeSelect = previousShowContainer.querySelector('.voivodeSelect');
                                                        const previousShowVoivodeId = getSelectedValue(previousShowVoivodeSelect);
                                                        const previousShowCitySelect = previousShowContainer.querySelector('.citySelect');
                                                        const previousShowCityId = getSelectedValue(previousShowCitySelect);

                                                        let checkboxElement = lastShowContainer.querySelector('.distance-checkbox');
                                                        $(checkboxElement).trigger('click');

                                                        previousShowVoivodeSelect = previousShowContainer.querySelector('.voivodeSelect');
                                                        setOldValues(previousShowVoivodeSelect, previousShowVoivodeId, previousShowCitySelect, previousShowCityId);
                                                    }

                                                    const citySelect = lastShowContainer.querySelector('.citySelect');
                                                    const voivodeSelect = lastShowContainer.querySelector('.voivodeSelect');
                                                    const voivodeId = response[i].voivodeId;
                                                    const cityId = response[i].cityId;

                                                    $(voivodeSelect).val(voivodeId).trigger('change');
                                                    $(citySelect).val(cityId).trigger('change');
                                                    dayFlag = response[i].day;
                                                }


                                            }
                                            globalSwalFlag = false;
                                        }

                                    }).done((response) => {
                                        swal.close();
                                    });
                                }
                            });

                                }
                            });

                        },
                "columns": [
                    {
                        "data": function (data, type, dataToSet) {
                            return '<span id="' + data.id + '">' + data.name + '</span>';
                        }, "name": "name", "orderable": true
                    },
                    {
                        "data": function (data, type, dataToSet) {
                            return '<input type="checkbox" style="display:inline-block" class="route_check">';
                        }, "orderable": false, "searchable": false, "width": "10%"
                    }
                ]
            });

            /**
             * This method is used in shows appended between another ones
             */
            function showInTheMiddleAjax(previousCityDistance, previousCityId, nextCityDistance, nextCityId, citySelect, voivodeSelect, dayContainer, oldValuesArray = null) {
                console.assert(citySelect.matches('.citySelect'), 'citySelect in showInTheMiddleAjax method is not city select');
                console.assert(voivodeSelect.matches('.voivodeSelect'), 'voivodeSelect in showInTheMiddleAjax method is not voivode select');
                console.assert((!isNaN(parseInt(nextCityId))) && (nextCityId != 0), 'nextCityId in showInTheMiddleAjax is not number!');
                console.assert((!isNaN(parseInt(previousCityId))) && (previousCityId != 0), 'previousCityId in showInTheMiddleAjax is not number!');
                console.assert((!isNaN(parseInt(previousCityDistance))) || (previousCityDistance == 'infinity'), 'previousCityId in showInTheMiddleAjax is not correct value!');
                console.assert((!isNaN(parseInt(nextCityDistance))) || (nextCityDistance == 'infinity'), 'nextCityDistance in showInTheMiddleAjax is not correct value!');
                let firstResponse = null;
                let secondResponse = null;
                let intersectionArray = null;

                const givenDayContainer = dayContainer.closest('.singleDayContainer');
                const date = givenDayContainer.querySelector('.day-info').textContent;

                if(globalSwalFlag) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                        data: {
                            'limit': previousCityDistance,
                            "currentDate": date,
                            "cityId": previousCityId
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            firstResponse = response;
                            console.assert(typeof(firstResponse) === "object", "firstResponse in showInTheMiddleAjax is not object!");
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                                data: {
                                    'limit': nextCityDistance,
                                    "currentDate": date,
                                    "cityId": nextCityId
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response2) {
                                    secondResponse = response2;
                                    voivodeSelect.innerHTML = '';
                                    citySelect.innerHTML = '';
                                    console.assert(typeof(secondResponse) === "object", "secondResponse in showInTheMiddleAjax is not object!");
                                    intersectionArray = getIntersection(firstResponse, secondResponse);

                                    let voivodeSet = intersectionArray[0];
                                    let citySet = intersectionArray[1];
                                    appendBasicOption(voivodeSelect);

                                    voivodeSet.forEach(voivode => {
                                        appendVoivodeOptions(voivodeSelect, voivode);
                                    });

                                    if(oldValuesArray) { //this is optional
                                        console.assert(Array.isArray(oldValuesArray), "oldVoivodeArr in showInExtreme method is not array!");
                                        appendBasicOption(citySelect);
                                        voivodeSet.forEach(voivode => {
                                            if(voivode.id == oldValuesArray[1]) {
                                                citySet.forEach(voivodeCity => {
                                                    console.assert(Array.isArray(voivodeCity), "voivodeCity in showInTheMiddleAjax method is not array!");
                                                    voivodeCity.forEach(city => {
                                                        if(city.id === voivode.id) {
                                                            appendCityOptions(citySelect, city);
                                                        }
                                                    });
                                                });
                                            }

                                        });
                                        setOldValues(oldValuesArray[0], oldValuesArray[1], oldValuesArray[2], oldValuesArray[3]);
                                    }

                                    citySelect.setAttribute('data-distance', nextCityDistance);
                                    $(voivodeSelect).on('change', function() {
                                        citySelect.innerHTML = ''; //cleaning previous insertions
                                        appendBasicOption(citySelect);

                                        voivodeSet.forEach(voivode => {
                                            citySet.forEach(voivodeCity => {
                                                console.assert(Array.isArray(voivodeCity), "voivodeCity in showInTheMiddleAjax method is not array!");
                                                voivodeCity.forEach(city => {
                                                    if(city.id === voivode.id) {
                                                        appendCityOptions(citySelect, city);
                                                    }
                                                });
                                            });
                                        });
                                    });
                                }
                            });

                        }
                    });
                }
                else {
                    swal({
                        title: 'Ładowawnie...',
                        text: 'To może chwilę zająć',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        onOpen: () => {
                            swal.showLoading();
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                                data: {
                                    'limit': previousCityDistance,
                                    "currentDate": date,
                                    "cityId": previousCityId
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    firstResponse = response;
                                    console.assert(typeof(firstResponse) === "object", "firstResponse in showInTheMiddleAjax is not object!");
                                    $.ajax({
                                        type: "POST",
                                        async: false,
                                        url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                                        data: {
                                            'limit': nextCityDistance,
                                            "currentDate": date,
                                            "cityId": nextCityId
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response2) {
                                            secondResponse = response2;
                                            voivodeSelect.innerHTML = '';
                                            citySelect.innerHTML = '';
                                            console.assert(typeof(secondResponse) === "object", "secondResponse in showInTheMiddleAjax is not object!");
                                            intersectionArray = getIntersection(firstResponse, secondResponse);

                                            let voivodeSet = intersectionArray[0];
                                            let citySet = intersectionArray[1];
                                            appendBasicOption(voivodeSelect);

                                            voivodeSet.forEach(voivode => {
                                                appendVoivodeOptions(voivodeSelect, voivode);
                                            });

                                            if(oldValuesArray) { //this is optional
                                                console.assert(Array.isArray(oldValuesArray), "oldVoivodeArr in showInExtreme method is not array!");
                                                appendBasicOption(citySelect);
                                                voivodeSet.forEach(voivode => {
                                                    if(voivode.id == oldValuesArray[1]) {
                                                        citySet.forEach(voivodeCity => {
                                                            console.assert(Array.isArray(voivodeCity), "voivodeCity in showInTheMiddleAjax method is not array!");
                                                            voivodeCity.forEach(city => {
                                                                if(city.id === voivode.id) {
                                                                    appendCityOptions(citySelect, city);
                                                                }
                                                            });
                                                        });
                                                    }

                                                });
                                                setOldValues(oldValuesArray[0], oldValuesArray[1], oldValuesArray[2], oldValuesArray[3]);
                                            }

                                            citySelect.setAttribute('data-distance', nextCityDistance);
                                            $(voivodeSelect).on('change', function() {
                                                citySelect.innerHTML = ''; //cleaning previous insertions
                                                appendBasicOption(citySelect);

                                                voivodeSet.forEach(voivode => {
                                                    citySet.forEach(voivodeCity => {
                                                        console.assert(Array.isArray(voivodeCity), "voivodeCity in showInTheMiddleAjax method is not array!");
                                                        voivodeCity.forEach(city => {
                                                            if(city.id === voivode.id) {
                                                                appendCityOptions(citySelect, city);
                                                            }
                                                        });
                                                    });
                                                });
                                            });
                                        }
                                    });

                                }
                            }).done((response) => {
                                swal.close();
                            });
                        }
                    });
                }


            }

            /**
             * This method is used in shows appended as first or last ones
             */
            function showInExtreme(limit, nextCityId, date, citySelect, voivodeSelect, oldVoivodeArr = null) {
                console.assert(citySelect.matches('.citySelect'), 'citySelect in showInExtreme method is not city select');
                console.assert(voivodeSelect.matches('.voivodeSelect'), 'voivodeSelect in showInExtreme method is not voivode select');
                console.assert(!isNaN(parseInt(limit)), 'limit in showInExtreme is not number!');
                console.assert((!isNaN(parseInt(nextCityId))) && (nextCityId != 0), 'nextCityId in showInExtreme is not number!');

                if(globalSwalFlag) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                        data: {
                            'limit': limit,
                            'currentDate': date,
                            "cityId": nextCityId
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            let allVoivodes = response['voievodeInfo'];
                            console.assert(Array.isArray(allVoivodes), "allVoivodes in showInExtreme method is not array!");
                            let allCitiesGroupedByVoivodes = response['cityInfo'];
                            console.assert(typeof(allCitiesGroupedByVoivodes) === "object", "allCitiesGroupedByVoivodes in showInExtreme method is not object!");
                            allVoivodes.forEach(voivode => {
                                appendVoivodeOptions(voivodeSelect, voivode)
                            });
                            citySelect.setAttribute('data-distance', limit); //applaying old value
                            if(oldVoivodeArr) { //this is optional
                                appendBasicOption(citySelect);
                                console.assert(Array.isArray(oldVoivodeArr), "oldVoivodeArr in showInExtreme method is not array!");
                                for(let Id in allCitiesGroupedByVoivodes) {
                                    if(oldVoivodeArr[1] == Id) {
                                        allCitiesGroupedByVoivodes[Id].forEach(city => {
                                            appendCityOptions(citySelect, city);
                                        });
                                    }
                                }
                                setOldValues(oldVoivodeArr[0], oldVoivodeArr[1], oldVoivodeArr[2], oldVoivodeArr[3]);
                            }

                            //After selecting voivode, this event listener appends cities from given range into city select
                            $(voivodeSelect).on('change', function(e) {
                                citySelect.innerHTML = ''; //cleaning previous insertions
                                appendBasicOption(citySelect);

                                let voivodeId = e.target.value;
                                for(let Id in allCitiesGroupedByVoivodes) {
                                    if(voivodeId == Id) {
                                        console.assert(Array.isArray(allCitiesGroupedByVoivodes[Id]), "allCitiesGroupedByVoivodes in showInExtreme method is not array!");
                                        allCitiesGroupedByVoivodes[Id].forEach(city => {
                                            appendCityOptions(citySelect, city);
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
                else {
                    swal({
                        title: 'Ładowawnie...',
                        text: 'To może chwilę zająć',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        onOpen: () => {
                            swal.showLoading();
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: '{{ route('api.getVoivodeshipRoundWithDistanceLimit') }}',
                                data: {
                                    'limit': limit,
                                    'currentDate': date,
                                    "cityId": nextCityId
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    let allVoivodes = response['voievodeInfo'];
                                    console.assert(Array.isArray(allVoivodes), "allVoivodes in showInExtreme method is not array!");
                                    let allCitiesGroupedByVoivodes = response['cityInfo'];
                                    console.assert(typeof(allCitiesGroupedByVoivodes) === "object", "allCitiesGroupedByVoivodes in showInExtreme method is not object!");
                                    allVoivodes.forEach(voivode => {
                                        appendVoivodeOptions(voivodeSelect, voivode)
                                    });
                                    citySelect.setAttribute('data-distance', limit); //applaying old value
                                    if(oldVoivodeArr) { //this is optional
                                        appendBasicOption(citySelect);
                                        console.assert(Array.isArray(oldVoivodeArr), "oldVoivodeArr in showInExtreme method is not array!");
                                        for(let Id in allCitiesGroupedByVoivodes) {
                                            if(oldVoivodeArr[1] == Id) {
                                                allCitiesGroupedByVoivodes[Id].forEach(city => {
                                                    appendCityOptions(citySelect, city);
                                                });
                                            }
                                        }
                                        setOldValues(oldVoivodeArr[0], oldVoivodeArr[1], oldVoivodeArr[2], oldVoivodeArr[3]);
                                    }

                                    //After selecting voivode, this event listener appends cities from given range into city select
                                    $(voivodeSelect).on('change', function(e) {
                                        citySelect.innerHTML = ''; //cleaning previous insertions
                                        appendBasicOption(citySelect);

                                        let voivodeId = e.target.value;
                                        for(let Id in allCitiesGroupedByVoivodes) {
                                            if(voivodeId == Id) {
                                                console.assert(Array.isArray(allCitiesGroupedByVoivodes[Id]), "allCitiesGroupedByVoivodes in showInExtreme method is not array!");
                                                allCitiesGroupedByVoivodes[Id].forEach(city => {
                                                    appendCityOptions(citySelect, city);
                                                });
                                            }
                                        }
                                    });
                                }
                            }).done((response) => {
                                swal.close();
                            });
                        }
                    });
                }

            }

            /**
             * This method is used in shows without distance limit
             */
            function showWithoutDistanceAjax(voivodeId, citySelect, date) {
                console.assert(!isNaN(parseInt(voivodeId)) && voivodeId != 0, 'voivodeId in showWithoutDistanceAjax is not number!');
                console.assert(citySelect.matches('.citySelect'), 'citySelect in showWithoutDistanceAjax method is not city select');

                if(globalSwalFlag) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: '{{ route('api.getCitiesNames') }}',
                        data: {
                            "id": voivodeId,
                            "currentDate": date
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.assert(Array.isArray(response), "response from ajax in showWithoutDistanceAjax method is not array!");
                            let placeToAppend = citySelect;
                            placeToAppend.innerHTML = '';
                            appendBasicOption(placeToAppend);
                            for(let i = 0; i < response.length; i++) {
                                let responseOption = document.createElement('option');
                                responseOption.value = response[i].id;
                                responseOption.textContent = response[i].name;

                                if(response[i].max_month_exceeded == 1) {
                                    responseOption.setAttribute('data-max_hours', `0`);
                                    responseOption.textContent = response[i].name + '[miesięczny limit przekroczony]';
                                }
                                else if(response[i].block == 1) {
                                    if(response[i].exceeded == 0) {
                                        responseOption.textContent = response[i].name + " [dostępne jeszcze " + response[i].used_hours + " godzin]";
                                        responseOption.setAttribute('data-max_hours', `${response[i].used_hours}`); //needed for auto setting hours
                                    }
                                    else {
                                        responseOption.textContent = response[i].name + " (KARENCJA do " + response[i].available_date + ") [przekroczono o " + response[i].used_hours + " godzin]";
                                        responseOption.setAttribute('data-max_hours', '0'); //needed for auto setting hours
                                    }
                                }
                                else if(response[i].block == 0) {
                                    responseOption.textContent = response[i].name;
                                    if (response[i].max_hour >= 0) {
                                        responseOption.setAttribute('data-max_hours', `${response[i].max_hour}`); //needed for auto setting hours
                                    }
                                    else {
                                        responseOption.setAttribute('data-max_hours', `3`); //needed for auto setting hours
                                    }
                                }
                                placeToAppend.appendChild(responseOption);
                            }
                        }
                    });
                }
                else {
                    swal({
                        title: 'Ładowawnie...',
                        text: 'To może chwilę zająć',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        onOpen: () => {
                            swal.showLoading();
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: '{{ route('api.getCitiesNames') }}',
                                data: {
                                    "id": voivodeId,
                                    "currentDate": date
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.assert(Array.isArray(response), "response from ajax in showWithoutDistanceAjax method is not array!");
                                    let placeToAppend = citySelect;
                                    placeToAppend.innerHTML = '';
                                    appendBasicOption(placeToAppend);
                                    for(let i = 0; i < response.length; i++) {
                                        let responseOption = document.createElement('option');
                                        responseOption.value = response[i].id;
                                        responseOption.textContent = response[i].name;

                                        if(response[i].max_month_exceeded == 1) {
                                            responseOption.setAttribute('data-max_hours', `0`);
                                            responseOption.textContent = response[i].name + '[miesięczny limit przekroczony]';
                                        }
                                        else if(response[i].block == 1) {
                                            if(response[i].exceeded == 0) {
                                                responseOption.textContent = response[i].name + " [dostępne jeszcze " + response[i].used_hours + " godzin]";
                                                responseOption.setAttribute('data-max_hours', `${response[i].used_hours}`); //needed for auto setting hours
                                            }
                                            else {
                                                responseOption.textContent = response[i].name + " (KARENCJA do " + response[i].available_date + ") [przekroczono o " + response[i].used_hours + " godzin]";
                                                responseOption.setAttribute('data-max_hours', '0'); //needed for auto setting hours
                                            }
                                        }
                                        else if(response[i].block == 0) {
                                            responseOption.textContent = response[i].name;
                                            if (response[i].max_hour >= 0) {
                                                responseOption.setAttribute('data-max_hours', `${response[i].max_hour}`); //needed for auto setting hours
                                            }
                                            else {
                                                responseOption.setAttribute('data-max_hours', `3`); //needed for auto setting hours
                                            }
                                        }
                                        placeToAppend.appendChild(responseOption);
                                    }
                                }
                            }).done((response) => {
                                swal.close();
                            });
                        }
                    });
                }


            }

            function limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr = null) {
                console.assert(grandNextShowContainer.matches('.singleShowContainer'), 'grandNextShowContainer in limitSelectsWhenBetweenSameDayContainer is not single day container');
                console.assert(thisSingleShowContainer.matches('.singleShowContainer'), 'thisSingleShowContainer in limitSelectsWhenBetweenSameDayContainer is not single day container');
                console.assert(nextShowContainer.matches('.singleShowContainer'), 'nextShowContainer in limitSelectsWhenBetweenSameDayContainer is not single day container');
                const grandNextShowContainerCitySelect = grandNextShowContainer.querySelector('.citySelect');
                const grandNextShowContainerCityDistance = grandNextShowContainerCitySelect.dataset.distance;
                let grandNextShowContainerCityId = getSelectedValue(grandNextShowContainerCitySelect);

                const thisSingleShowContainerCitySelect = thisSingleShowContainer.querySelector('.citySelect');
                const thisSingleShowContainerCitySelectCityDistance = thisSingleShowContainerCitySelect.dataset.distance;
                let thisSingleShowContainerCityId = getSelectedValue(thisSingleShowContainerCitySelect);

                const nextShowContainerCitySelect = nextShowContainer.querySelector('.citySelect');
                let nextShowContainerCityid = getSelectedValue(nextShowContainerCitySelect);

                let nextShowContainerVoivodeSelect = nextShowContainer.querySelector('.voivodeSelect');
                let nextShowContainerVoivodeId = getSelectedValue(nextShowContainerVoivodeSelect);

                if((grandNextShowContainerCitySelect.length == 0 || grandNextShowContainerCityId == 0) ||
                    (thisSingleShowContainerCitySelect.length == 0 || thisSingleShowContainerCityId == 0) ||
                    (nextShowContainerCitySelect.length == 0  || nextShowContainerCityid == 0) ||
                    (nextShowContainerVoivodeSelect.length == 0 || nextShowContainerVoivodeId == 0) ||
                    (!thisSingleShowContainerCityId) || (!nextShowContainerCityid) || (!nextShowContainerVoivodeId) || (!grandNextShowContainerCityId)) {
                    notify("Wybierz miasta i województwa we wszystkich listach 5");
                    return false;
                }

                let oldValuesArray = [nextShowContainerVoivodeSelect, nextShowContainerVoivodeId, nextShowContainerCitySelect, nextShowContainerCityid];

                $(nextShowContainerVoivodeSelect).off();

                nextShowContainerVoivodeSelect.innerHTML = '';
                nextShowContainerCitySelect.innerHTML = '';

                if(changeDistanceArr) {
                    let helpArr = [];
                    if(changeDistanceArr[0] != 'undefined') {
                        helpArr.push(changeDistanceArr[0]);
                    }
                    else {
                        helpArr.push(grandNextShowContainerCityDistance);
                    }
                    if(changeDistanceArr[1] != 'undefined') {
                        helpArr.push(changeDistanceArr[1]);
                    }
                    else {
                        helpArr.push(thisSingleShowContainerCitySelectCityDistance);
                    }
                    showInTheMiddleAjax(helpArr[0],grandNextShowContainerCityId,helpArr[1],thisSingleShowContainerCityId,nextShowContainerCitySelect,nextShowContainerVoivodeSelect, nextShowContainer, oldValuesArray);
                }
                else {
                    showInTheMiddleAjax(grandNextShowContainerCityDistance,grandNextShowContainerCityId,thisSingleShowContainerCitySelectCityDistance,thisSingleShowContainerCityId,nextShowContainerCitySelect,nextShowContainerVoivodeSelect, nextShowContainer, oldValuesArray);
                }
            }

            /**
             * This method handle refresh distance case when prev show is in the same day container and there is not previous container && case when next show is in the same day container and there is no next container
             */
            function limitSelectsWhenExtreme(previousShowContainer, nextShowContainerRelatedToPreviousShowContainer, limit) {
                let prevShowContainerVoivodeSelect = previousShowContainer.querySelector('.voivodeSelect');
                let prevShowVoivodeId = getSelectedValue(prevShowContainerVoivodeSelect);

                let prevShowContainerCitySelect = previousShowContainer.querySelector('.citySelect');
                let prevShowCityId = getSelectedValue(prevShowContainerCitySelect);

                let nextShowContainerRelatedToPreviousShowContainerCitySelect = nextShowContainerRelatedToPreviousShowContainer.querySelector('.citySelect');
                let nextShowContainerRelatedToPreviousShowContainerCityId = getSelectedValue(nextShowContainerRelatedToPreviousShowContainerCitySelect);

                let oldValuesArray = [prevShowContainerVoivodeSelect, prevShowVoivodeId, prevShowContainerCitySelect, prevShowCityId];

                if((prevShowContainerVoivodeSelect.length == 0 || prevShowVoivodeId == 0) ||
                    (prevShowContainerCitySelect.length == 0 || prevShowCityId == 0) ||
                    (nextShowContainerRelatedToPreviousShowContainerCitySelect.length == 0 || nextShowContainerRelatedToPreviousShowContainerCityId == 0) ||
                    (!prevShowCityId) || (!prevShowVoivodeId) || (!nextShowContainerRelatedToPreviousShowContainerCityId)) {
                    notify("Wybierz miasta i województwa we wszystkich listach 9");
                    return false;
                }

                $(prevShowContainerVoivodeSelect).off();

                prevShowContainerVoivodeSelect.innerHTML = '';
                appendBasicOption(prevShowContainerVoivodeSelect);
                prevShowContainerCitySelect.innerHTML = '';

                const previousShowDayContainer = nextShowContainerRelatedToPreviousShowContainer.closest('.singleDayContainer');
                const date = previousShowDayContainer.querySelector('.day-info').textContent;

                showInExtreme(limit, nextShowContainerRelatedToPreviousShowContainerCityId, date, prevShowContainerCitySelect, prevShowContainerVoivodeSelect, oldValuesArray);
            }

            //This method is used when appending all voivodes and all cities
            function allCitiesAndAllVoivodes(nextShowContainer, defaults = null) {
                //all cities and all voivodes.
                let nextContVoivodeSelect = nextShowContainer.querySelector('.voivodeSelect');
                nextContVoivodeSelect.innerHTML = '';
                appendBasicOption(nextContVoivodeSelect);
                let nextContCitySelect = nextShowContainer.querySelector('.citySelect');
                $(nextContVoivodeSelect).off(); //remove all previous event listeners
                        @foreach($voivodes as $voivode)
                var singleVoivode = document.createElement('option');
                singleVoivode.value = {{$voivode->id}};
                singleVoivode.textContent = '{{$voivode->name}}';
                nextContVoivodeSelect.appendChild(singleVoivode);
                @endforeach()

                const dayContainer = nextShowContainer.closest('.singleDayContainer');
                const fullDate = dayContainer.querySelector('.day-info').textContent;
                let correctDate = fullDate.substr(6); //YYYY-MM-DD

                $(nextContVoivodeSelect).on('change', function(e) {
                    nextContCitySelect.setAttribute('data-distance', 'infinity');
                    let voivodeId = e.target.value;
                    showWithoutDistanceAjax(voivodeId, nextContCitySelect, correctDate);
                });
                if(defaults) {
                    showWithoutDistanceAjax(defaults.voivode, nextContCitySelect, correctDate);
                }
            }

            /**
             * This method appends basic option to voivode select
             */
            function appendBasicOption(element) {
                console.assert(element.tagName === "SELECT", 'Element in appendBasicOption is not select element');
                let basicVoivodeOption = document.createElement('option');
                basicVoivodeOption.value = '0';
                basicVoivodeOption.textContent = 'Wybierz';
                element.appendChild(basicVoivodeOption);
            }

            /**
             * This method appends options with voivode data
             */
            function appendVoivodeOptions(element, data) {
                console.assert(element.matches('.voivodeSelect'), 'Element in appendVoivodeOptions method is not voivode select');
                let voivodeOption = document.createElement('option');
                voivodeOption.value = data.id;
                voivodeOption.textContent = data.name;
                element.appendChild(voivodeOption);
            }

            /**
             * This method appends options with city data
             */
            function appendCityOptions(element,data) {
                console.assert(element.matches('.citySelect'), 'Element in appendCityOptions method is not city select');
                let cityOpt = document.createElement('option');
                cityOpt.value = data.city_id;
                cityOpt.textContent = data.city_name;

                if(data.max_month_exceeded == 1) {
                    cityOpt.setAttribute('data-max_hours', `0`);
                    cityOpt.textContent = data.city_name + '[miesięczny limit przekroczony]';
                }
                else if(data.block == 1) {
                    if(data.exceeded == 0) {
                        cityOpt.setAttribute('data-max_hours', `${data.used_hours}`);
                        cityOpt.textContent = data.city_name + ' [dostępne jeszcze ' + data.used_hours + ' godzin]';
                    }
                    else {
                        cityOpt.setAttribute('data-max_hours', '0');
                        cityOpt.textContent = data.city_name + '(KARENCJA do ' + data.available_date + ') [przekroczono o ' + data.used_hours + ' godzin]';
                    }
                }
                else if(data.block == 0) {
                    if(data.max_hour >= 0) {
                        cityOpt.setAttribute('data-max_hours', `${data.max_hour}`);
                    }
                    else {
                        cityOpt.setAttribute('data-max_hours', `3`);
                    }
                }

                element.appendChild(cityOpt);
            }

            /**
             * This function return intersection of 2 given sets of cities
             */
            function getIntersection(firstResponse, secondResponse) {
                let intersectionVoivodes = [];
                let intersectionCities = [];
                const firstVoivodeInfo = firstResponse['voievodeInfo'];
                const secondVoivodeInfo = secondResponse['voievodeInfo'];
                const firstCityInfo = firstResponse['cityInfo'];
                const secondCityInfo = secondResponse['cityInfo'];
                console.assert(Array.isArray(firstVoivodeInfo), "firstVoivodeInfo in getIntersection method is not array!");
                console.assert(Array.isArray(secondVoivodeInfo), "secondVoivodeInfo in getIntersection method is not array!");
                console.assert(typeof(firstCityInfo) === "object", "firstCityInfo in getIntersection method is not object!");
                console.assert(typeof(secondCityInfo) === "object", "secondCityInfo in getIntersection method is not object!");

                //linear looking for same voivodes
                firstVoivodeInfo.forEach(voivode => {
                    secondVoivodeInfo.forEach(voivode2 => {
                        if(voivode2.id === voivode.id) {
                            intersectionVoivodes.push(voivode);
                        }
                    })
                });

                intersectionVoivodes.forEach(voivode => {
                    let voivodeCityArr = [];
                    if(firstCityInfo[voivode.id] && secondCityInfo[voivode.id]) {
                        let firstCitySet = firstCityInfo[voivode.id];
                        let secondCitySet = secondCityInfo[voivode.id];
                        firstCitySet.forEach(city => {
                            secondCitySet.forEach(city2 => {
                                if(city.city_id === city2.city_id) {
                                    voivodeCityArr.push(city);
                                }
                            });
                        });
                    }
                    if(voivodeCityArr.length != 0) {
                        intersectionCities.push(voivodeCityArr);
                    }
                });

                let intersectionArray = [];
                intersectionArray.push(intersectionVoivodes);
                intersectionArray.push(intersectionCities);

                console.assert(intersectionArray.length === 2, 'Problem with intersectionArray in getIntersection method');
                return intersectionArray;
            }

            /**
             * @param thisContainer
             * @param className
             * This function return array of prev and next containers if exist.
             */
            function checkingExistenceOfPrevAndNextContainers(thisContainer, className) {
                const allContainers = document.getElementsByClassName(className);
                let nextCont = null;
                let prevCont = null;
                let finalArray = [];
                //checking whether there is next and previous show container
                for(let i = 0; i < allContainers.length; i++) {
                    if(allContainers[i] == thisContainer) {
                        if(allContainers[i+1]) { //exist next container
                            nextCont = allContainers[i+1];
                        }
                        if(allContainers[i-1]) {
                            prevCont = allContainers[i-1];
                        }
                    }
                }
                finalArray.push(prevCont);
                finalArray.push(nextCont);
                return finalArray;
            }

            /**
             * This method check if in given contaiers there is checkbox checked or not
             * @param arrayOfContainers
             * @returns {Array} [undefined/true/false, undefined/true/false] - (undefined - no container given in arrayOfContainers, false - not checked, true - checked)
             */
            function checkboxFilter(arrayOfContainers) {
                console.assert(Array.isArray(arrayOfContainers), "arrayOfContainers in checkboxFilter method is not array!");
                let prevCont = arrayOfContainers[0];
                let nextCont = arrayOfContainers[1];
                let isCheckedPrev = undefined;
                let isCheckedNext = undefined;
                let checkArr = [];
                if(prevCont) {
                    let checkboxPrevElement = prevCont.querySelector('.distance-checkbox');
                    isCheckedPrev = checkboxPrevElement.checked;
                }
                if(nextCont) {
                    let checkboxNextElement = nextCont.querySelector('.distance-checkbox');
                    isCheckedNext = checkboxNextElement.checked;
                }
                checkArr.push(isCheckedPrev);
                checkArr.push(isCheckedNext);

                return checkArr;
            }

            /**
             * This method validate all single day forms
             * If withHour = true, validate with show-hour input, else - without
             */
            function validateAllForms(element, withHour = null) {
                // console.assert(element.matches('.singleShowContainer'), 'element in validateAllForms is not single show container');
                let flag = true;
                element.forEach(day => {
                    let validation
                    if(withHour) {
                        validation = validateForm(day, true);
                    }
                    else {
                        validation = validateForm(day);
                    }

                    if(validation === false) {
                        flag = false;
                    }
                });

                return flag;
            }

            /**
             * This method returns selected by user from list item's value or null.
             */
            function getSelectedValue(element) {
                console.assert(element.tagName === 'SELECT', 'Argument of getSelectedValue is not select element');
                if(element.options[element.selectedIndex]) {
                    return element.options[element.selectedIndex].value;
                }
                else {
                    return null;
                }
            }

            /**
             * This method sets old values for inputs.
             * @param voivodeSelect
             * @param voivodeId
             * @param citySelect
             * @param cityId
             */
            function setOldValues(voivodeSelect, voivodeId, citySelect, cityId) {
                console.assert(voivodeSelect.matches('.voivodeSelect'), 'voivodeSelect in setOldValues method is not voivode select');
                console.assert((!isNaN(parseInt(voivodeId))) && (voivodeId != 0), 'voivodeId in setOldValues is not number!');
                console.assert(citySelect.matches('.citySelect'), 'citySelect in setOldValues method is not city select');
                console.assert((!isNaN(parseInt(cityId))) && (cityId != 0), 'cityId in setOldValues is not number!');
                let voivodeFlag = true;
                let cityFlag = true;
                for(let i = 0; i < voivodeSelect.length; i++) {
                    if(voivodeSelect[i].value == voivodeId) {
                        voivodeSelect[i].selected = true;
                        voivodeFlag = false;
                    }
                }
                for(let j = 0; j < citySelect.length; j++) {
                    if(citySelect[j].value == cityId) {
                        citySelect[j].selected = true;
                        cityFlag = false;
                    }
                }
                if(voivodeFlag) {
                    $(voivodeSelect).val('0');
                }
                if(cityFlag) {
                    $(citySelect).val('0');
                }
            }

            /**
             * This function shows notification.
             */
            function notify(htmltext$string, type$string = 'info', delay$miliseconds$number = 5000) {
                $.notify({
                    // options
                    message: htmltext$string
                },{
                    // settings
                    type: type$string,
                    delay: delay$miliseconds$number,
                    animate: {
                        enter: 'animated fadeInRight',
                        exit: 'animated fadeOutRight'
                    }
                });
            }

            /**
             * This method validate form false - bad, true - good
             * if withHours - true, check whether hour input has != 0 value.
             */
            function validateForm(element, withHours = null) {
                console.assert(element.matches('.singleShowContainer'), 'element in validateForm is not singleShowContainer');
                let flag = true;
                let citySelect = element.querySelector('.citySelect');
                let cityValue = getSelectedValue(citySelect);
                let voivodeSelect = element.querySelector('.voivodeSelect');
                let voivodeId = getSelectedValue(voivodeSelect);
                let hourInput = element.querySelector('.show-hours');
                let hourValue = hourInput.value;
                if(withHours) {
                    if((cityValue == null || cityValue == 0) || (voivodeId == null || voivodeId == 0) || (hourValue == 0 || hourValue == null)) {
                        flag = false;
                    }
                }
                else {
                    if((cityValue == null || cityValue == 0) || (voivodeId == null || voivodeId == 0)) {
                        flag = false;
                    }
                }

                return flag;
            }

            /**
             * This function return date in YYYY-MM-DD format.
             * if number is given, it add days to date.
             */
            function getCorrectDate(number = 0) {
                console.assert(!isNaN(parseInt(number)), 'number in getCorrectDate is not number!');
                let userDate = $('#date').val();

                let firstShowDate = new Date(userDate);
                firstShowDate.setDate(firstShowDate.getDate() + number);
                let day = firstShowDate.getDate();
                let month = firstShowDate.getMonth() + 1; //January is 0!

                let year = firstShowDate.getFullYear();
                if (day < 10) {
                    day = '0' + day;
                }
                if (month < 10) {
                    month = '0' + month;
                }


                return (year + '-' + month + '-' + day);
            }

            /**
             * This method creates on fly table with clientRouteInfo records
             * @param placeToAppend - modal body
             * @param data - mostly clientRouteInfo records
             */
            function createModalTable(placeToAppend, data) {
                if (data.length != 0) {
                    const infoTable = document.createElement('table');
                    infoTable.classList.add('table', 'table-striped');

                    const theadElement = document.createElement('thead');
                    const tbodyElement = document.createElement('tbody');
                    const tr1Element = document.createElement('tr');
                    const th1Element = document.createElement('th');
                    const th2Element = document.createElement('th');

                    th1Element.textContent = 'Miasto';
                    tr1Element.appendChild(th1Element);

                    th2Element.textContent = 'Data';
                    tr1Element.appendChild(th2Element);

                    theadElement.appendChild(tr1Element);
                    infoTable.appendChild(theadElement);

                    let dateFlag = null;
                    if(data[0].date) {
                        dateFlag = data[0].date;
                    }

                    for (let i = 0; i < data.length; i++) {

                        if(dateFlag != data[i].date) { //this part add row if there is date change
                            const additionalTRelement = document.createElement('tr');
                            const additionaltd1Element = document.createElement('td');
                            const additionaltd2Element = document.createElement('td');
                            additionalTRelement.appendChild(additionaltd1Element);
                            additionalTRelement.appendChild(additionaltd2Element);
                            tbodyElement.appendChild(additionalTRelement);
                        }
                        dateFlag = data[i].date;
                        const trElement = document.createElement('tr');
                        const td1Element = document.createElement('td');
                        const td2Element = document.createElement('td');
                        td1Element.textContent = data[i].cityName;
                        td2Element.textContent = data[i].date;
                        trElement.appendChild(td1Element);
                        trElement.appendChild(td2Element);
                        tbodyElement.appendChild(trElement);
                    }
                    infoTable.appendChild(tbodyElement);
                    placeToAppend.appendChild(infoTable);
                }
            }

            /**
             * This constructor defines new show object
             * API:
             * let variable = new ShowBox(); - we create new show object,
             * variable.addNewShowButton() - indices that we want addNewShowButton(optional),
             * variable.addRemoveShowButton() - indices that we want removeShowButton(optional),
             * variable.addCheckboxFlag() - indices that we want checkbox(optional),
             * -----------------------------------------------------------------------
             * variable.createDOMBox() - we create new DOM element with no distance limit,
             * variable.createDOMBox(X, cityId) - we create new DOM element with distance limit of X(for example 30) relative to city(cityId),
             * variable.createDOMBox(x, cityId, true, previousShowContainer, nextShowContainer) -
             * we create new DOM element with distance limit between previousShowContainer's limit and
             * nextShowContainer's limit.
             * let DOMElement = variable.getForm(); - we obtain we obtain DOM representation of ShowBox
             * ------------------------------------------------------------------------
             */
            function ShowBox() {
                this.addNewShowButtonFlag = false; //indices whether add newShowButton
                this.addRemoveShowButtonFlag = false; //indices whether add removeShowButton
                this.addCheckboxFlag = false; //indices whether add refreshShowButton
                this.DOMBox = null; //here is stored DOM representation of ShowBox
                this.addNewShowButton = function() {
                    this.addNewShowButtonFlag = true;
                };
                this.addRemoveShowButton = function() {
                    this.addRemoveShowButtonFlag = true;
                };
                this.addDistanceCheckbox = function() {
                    this.addCheckboxFlag = true;
                };
                this.createDOMBox = function(date,distance = Infinity, selectedCity = null, intersetion = false, previousBox = null, nextBox = null) { //Creation of DOM form
                    let formBox = document.createElement('div'); //creation of main form container
                    formBox.classList.add('singleShowContainer');

                    /*REMOVE BUTTON PART*/
                    if(this.addRemoveShowButtonFlag) { //adding remove button.
                        console.assert(this.addRemoveShowButtonFlag === true, 'addRemoveShowButtonFlag error');
                        let removeButtonContainer = document.createElement('div');
                        removeButtonContainer.classList.add('remove-button-container');
                        let removeButton = document.createElement('span');
                        removeButton.classList.add('glyphicon');
                        removeButton.classList.add('glyphicon-remove');
                        removeButton.classList.add('remove-button');
                        removeButtonContainer.appendChild(removeButton);
                        formBox.appendChild(removeButtonContainer);
                    }
                    /*END REMOVE BUTTON PART*/

                    /*HEADER PART*/
                    let headerRow = document.createElement('div');
                    headerRow.classList.add('row');

                    let headerCol = document.createElement('div');
                    headerCol.classList.add('col-md-12');

                    let header = document.createElement('div'); // creation of form title
                    header.classList.add('singleShowHeader');
                    header.textContent = 'Kampania';

                    headerCol.appendChild(header);
                    headerRow.appendChild(headerCol);
                    formBox.appendChild(headerRow);
                    /*END HEADER PART*/

                    /* CHECKBOX PART */
                    if(this.addCheckboxFlag) { //adding checkbox
                        console.assert(this.addCheckboxFlag === true, 'addCheckboxFlag error');
                        let afterHeaderRow = document.createElement('div');
                        afterHeaderRow.classList.add('row');

                        let afterHeaderCol = document.createElement('div');
                        afterHeaderCol.classList.add('col-md-12');

                        let checkboxLabel = document.createElement('label');
                        checkboxLabel.textContent = 'Zdejmij ograniczenie';
                        checkboxLabel.style.display = 'inline-block';

                        let distanceCheckbox = document.createElement('input');
                        distanceCheckbox.setAttribute('type', 'checkbox');
                        distanceCheckbox.classList.add('distance-checkbox');
                        distanceCheckbox.style.display = 'inline-block';
                        distanceCheckbox.style.marginRight = '1em';

                        afterHeaderCol.appendChild(distanceCheckbox);
                        afterHeaderCol.appendChild(checkboxLabel);
                        afterHeaderRow.appendChild(afterHeaderCol);
                        formBox.appendChild(afterHeaderRow);
                    }
                    /*END CHECKBOX PART */

                    /*BODY PART*/
                    let formBodyRow = document.createElement('div');
                    formBodyRow.classList.add('row');

                    let formBodyColRightColumn = document.createElement('div');
                    formBodyColRightColumn.classList.add('col-md-5');

                    let formBodyRightColumnGroup = document.createElement('div');
                    formBodyRightColumnGroup.classList.add('form-group');

                    let secondSelectLabel = document.createElement('label');
                    secondSelectLabel.textContent = 'Miasto';

                    let secondSelect = document.createElement('select');
                    secondSelect.classList.add('citySelect');
                    secondSelect.classList.add('form-control');

                    let formBodyMaxRightColumn = document.createElement('div');
                    formBodyMaxRightColumn.classList.add('col-md-1');

                    let loopIcon = document.createElement('span');
                    loopIcon.classList.add('glyphicon');
                    loopIcon.classList.add('glyphicon-search');
                    loopIcon.classList.add('show-cities-statistics');

                    let formBodyColLeftColumn = document.createElement('div');
                    formBodyColLeftColumn.classList.add('col-md-6');

                    let formBodyLeftColumnGroup = document.createElement('div');
                    formBodyLeftColumnGroup.classList.add('form-group');

                    let firstSelectLabel = document.createElement('label');
                    firstSelectLabel.textContent = 'Województwo';

                    let firstSelect = document.createElement('select');
                    firstSelect.classList.add('voivodeSelect');
                    firstSelect.classList.add('form-control');

                    appendBasicOption(firstSelect);
                    if(distance === Infinity && intersetion === false) { //every voivodeship and every city
                        @foreach($voivodes as $voivode)
                            var singleVoivode = document.createElement('option');
                            singleVoivode.value = {{$voivode->id}};
                            singleVoivode.textContent = '{{$voivode->name}}';
                            firstSelect.appendChild(singleVoivode);
                        @endforeach()

                        $(firstSelect).on('change', function(e) {
                            secondSelect.setAttribute('data-distance', 'infinity');
                            let voivodeId = e.target.value;
                            showWithoutDistanceAjax(voivodeId, secondSelect, globalDateIndicator);
                        });
                    }
                    else if((distance === 100 || distance === 30) && intersetion === false) { // adding show in the end
                        showInExtreme(distance, selectedCity, date, secondSelect, firstSelect);
                    }
                    else if((distance === 100 || distance === 30) && intersetion === true) { // adding show between some shows
                        const previousCitySelect = previousBox.querySelector('.citySelect');
                        const previousCityDistance = previousCitySelect.dataset.distance;
                        const previousCityId = getSelectedValue(previousCitySelect);
                        const nextCitySelect = nextBox.querySelector('.citySelect');
                        const nextCityDistance = nextCitySelect.dataset.distance;
                        const nextCityId = getSelectedValue(nextCitySelect);

                        showInTheMiddleAjax(previousCityDistance,previousCityId,nextCityDistance,nextCityId,secondSelect,firstSelect,previousCitySelect);
                    }

                    formBodyLeftColumnGroup.appendChild(firstSelectLabel);
                    formBodyLeftColumnGroup.appendChild(firstSelect);
                    formBodyColLeftColumn.appendChild(formBodyLeftColumnGroup);
                    formBodyRow.appendChild(formBodyColLeftColumn);

                    appendBasicOption(secondSelect);
                    formBodyRightColumnGroup.appendChild(secondSelectLabel);
                    formBodyRightColumnGroup.appendChild(secondSelect);
                    formBodyColRightColumn.appendChild(formBodyRightColumnGroup);
                    formBodyRow.appendChild(formBodyColRightColumn);

                    formBodyMaxRightColumn.appendChild(loopIcon);
                    formBodyRow.appendChild(formBodyMaxRightColumn);

                    formBox.appendChild(formBodyRow);
                    /*END BODY PART*/

                    /*2ND BODY PART*/
                    let formBody2Row = document.createElement('div');
                    formBody2Row.classList.add('row');

                    let formBody2ColLeftColumn = document.createElement('div');
                    formBody2ColLeftColumn.classList.add('col-md-6');

                    let formBody2LeftColumnGroup = document.createElement('div');
                    formBody2LeftColumnGroup.classList.add('form-group');

                    let hourInputLabel = document.createElement('label');
                    hourInputLabel.textContent = 'Ilość godzin pokazów';

                    let hourInput = document.createElement('input');
                    hourInput.classList.add('show-hours');
                    hourInput.classList.add('form-control');
                    hourInput.setAttribute('min', '0');
                    hourInput.setAttribute('type', 'number');

                    formBody2LeftColumnGroup.appendChild(hourInputLabel);
                    formBody2LeftColumnGroup.appendChild(hourInput);
                    formBody2ColLeftColumn.appendChild(formBody2LeftColumnGroup);

                    formBody2Row.appendChild(formBody2ColLeftColumn);

                    formBox.appendChild(formBody2Row);

                    /*END 2ND BODY*/

                    /* ADD NEW SHOW BUTTON */
                    if(this.addNewShowButtonFlag) {
                        console.assert(this.addNewShowButtonFlag === true, 'addNewShowButtonFlag error');
                        let buttonRow = document.createElement('div');
                        buttonRow.classList.add('row');

                        let buttonCol = document.createElement('div');
                        buttonCol.classList.add('col-md-12');

                        let addNewShowButton = document.createElement('button');
                        addNewShowButton.classList.add('btn');
                        addNewShowButton.classList.add('btn-info');
                        addNewShowButton.classList.add('addNewShowButton');
                        addNewShowButton.style.width = "100%";
                        addNewShowButton.textContent = 'Dodaj nowy pokaz';

                        buttonCol.appendChild(addNewShowButton);
                        buttonRow.appendChild(buttonCol);
                        formBox.appendChild(buttonRow);
                    }
                    /* END NEW SHOW BUTTON */
                    this.DOMBox = formBox;
                };
                this.getForm = function() {
                    return this.DOMBox;
                }
            }

            /**
             * This constructor defines day container object.
             * API:
             * let variable = new DayBox();  - we obtain new day element.
             * variable.createDOMDayBox(); - we create DOM representation of DayBox;
             * let DOMElement = variable.getBox(); - we obtain DOM representation of DayBox
             */
            function DayBox() {
                this.dayBoxDOM = null;
                this.createDOMDayBox = function() {
                    const allDayContainers = document.getElementsByClassName('singleDayContainer');
                    const numberOfAllDayContainers = allDayContainers.length;

                    let mainContainer = document.createElement('div');
                    mainContainer.classList.add('singleDayContainer');

                    let dayInfoContainer = document.createElement('div');
                    dayInfoContainer.classList.add('day-info');

                    let correctDate = getCorrectDate(numberOfAllDayContainers);

                    dayInfoContainer.textContent = "Data: " + correctDate;
                    globalDateIndicator = correctDate;

                    mainContainer.appendChild(dayInfoContainer);
                    this.dayBoxDOM = mainContainer;
                };
                this.getBox = function() {
                    return this.dayBoxDOM;
                }
            }

            function ButtonBox() {
                this.save = false;
                this.addNewDay = false;
                this.appendSaveButton = function() {
                    this.save = true;
                }
                this.appendAddNewDayButton = function() {
                    this.addNewDay = true;
                }
                this.getBox = function() {
                    let box = document.createElement('div');
                    box.classList.add('summaryButtonContainer');
                    if(this.addNewDay) {
                        let nextDayRow = document.createElement('div');
                        nextDayRow.classList.add('row');

                        let nextDayCol = document.createElement('div');
                        nextDayCol.classList.add('col-md-12');

                        let nextDayButton = document.createElement('button');
                        nextDayButton.id = 'addNewDay';
                        nextDayButton.classList.add('btn');
                        nextDayButton.classList.add('btn-success');
                        nextDayButton.style.width = '100%';
                        nextDayButton.textContent = 'Dodaj nowy dzień';

                        nextDayCol.appendChild(nextDayButton);
                        nextDayRow.appendChild(nextDayCol);
                        box.appendChild(nextDayRow);
                    }
                    if(this.save) {
                        let saveRow = document.createElement('div');
                        saveRow.classList.add('row');

                        let saveCol = document.createElement('div');
                        saveCol.classList.add('col-md-12');

                        let saveButton = document.createElement('button');
                        saveButton.id = 'save';
                        saveButton.classList.add('btn');
                        saveButton.classList.add('btn-success');
                        saveButton.style.width = '100%';
                        saveButton.textContent = 'Zapisz';

                        saveCol.appendChild(saveButton);
                        saveRow.appendChild(saveCol);
                        box.appendChild(saveRow);
                    }
                    return box;
                }
            }

            /****************************************EVENT LISTENERS FUNCTIONS******************************************/




            //Ta funkcja jest globalnym event listenerem na click
            function buttonHandler(e) {
                if (e.target.matches('#redirect')) {
                    location.href = "{{URL::to('/showClientRoutes')}}";
                }
                else if(e.target.matches('.addNewShowButton')) { //user clicks on "add new show" button
                    e.preventDefault();
                    const newShowButton = e.target;
                    const thisShowContainer = newShowButton.closest('.singleShowContainer');
                    const thisSingleDayContainer = newShowButton.closest('.singleDayContainer');
                    const containerDate = thisSingleDayContainer.querySelector('.day-info').textContent;
                    const allSingleShowContainers = document.getElementsByClassName('singleShowContainer');
                    const isChecked = thisShowContainer.querySelector('.distance-checkbox').checked;

                    const selectedCity = thisShowContainer.querySelector('.citySelect');
                    const selectedCityId = getSelectedValue(selectedCity);

                    let validation = validateForm(thisShowContainer);

                    if(validation) {
                        let newForm = new ShowBox();
                        newForm.addRemoveShowButton();
                        newForm.addDistanceCheckbox();
                        newForm.addNewShowButton();

                        let lastOneFlag = true;
                        let nextShowContainer = null;
                        //we are checking whether there is more single show containers
                        for(let i = 0; i < allSingleShowContainers.length; i++) {
                            if(allSingleShowContainers[i] === thisShowContainer) {
                                if(allSingleShowContainers[i+1]) {
                                    lastOneFlag = false;
                                    nextShowContainer = allSingleShowContainers[i+1];
                                }

                            }
                        }

                        if(isChecked) { //when clicked singleDayContainer has checkbox checked
                            newForm.createDOMBox(containerDate);
                            let newFormDomElement = newForm.getForm();
                            thisShowContainer.insertAdjacentElement('afterend',newFormDomElement).scrollIntoView({behavior: "smooth"});
                        }
                        else {
                            //we are checking whether cliecked singleDayContainer is last one, or between others.
                            if(lastOneFlag === true) {
                                newForm.createDOMBox(containerDate, 30, selectedCityId);
                                let newFormDomElement = newForm.getForm();
                                thisShowContainer.insertAdjacentElement('afterend',newFormDomElement).scrollIntoView({behavior: "smooth"});
                            }
                            else { //container is not last one
                                const apreviousCitySelect = thisShowContainer.querySelector('.citySelect');
                                const anextCitySelect = nextShowContainer.querySelector('.citySelect');
                                //we are checking if user selected any city in upper and lower show container
                                if(anextCitySelect.options[anextCitySelect.selectedIndex].value != 0 && apreviousCitySelect.options[apreviousCitySelect.selectedIndex].value != 0) {
                                    apreviousCitySelect.dataset.distance = 30;
                                    newForm.createDOMBox(containerDate, 30, selectedCityId, true, thisShowContainer, nextShowContainer);
                                    let newFormDomElement = newForm.getForm();
                                    thisShowContainer.insertAdjacentElement('afterend',newFormDomElement).scrollIntoView({behavior: "smooth"});
                                }
                                else {
                                    notify('Wybierz miasta w pokazach powyżej i poniżej');
                                }
                            }
                        }


                    }
                    else { //validation failed
                        notify('Wybierz miasto');
                    }
                }
                else if(e.target.matches('.remove-button')) { // user clicks on "remove show" button
                    e.preventDefault();
                    const removeShowButton = e.target;
                    const showContainer = removeShowButton.closest('.singleShowContainer');
                    const dayContainer = removeShowButton.closest('.singleDayContainer');

                    let prevDayFlag = undefined; //true - another day, false - same day
                    let nextDayFlag = undefined;
                    let grandPrevDayFlag = undefined; //true - another day, false - same day
                    let grandNextDayFlag = undefined;
                    let nextShowFlag = undefined; //true - another day, false - same day
                    let prevShowFlag = undefined;

                    const showExistenceArray = checkingExistenceOfPrevAndNextContainers(showContainer, 'singleShowContainer');
                    let siblingsCheckboxArr = checkboxFilter(showExistenceArray);

                    if(showExistenceArray[0]) { //case when previous container exist
                        let prevShowContainer = showExistenceArray[0];
                        let dayContOfPrevShowContainer = prevShowContainer.closest('.singleDayContainer');
                        prevDayFlag = dayContainer == dayContOfPrevShowContainer ? false : true; //checking if next show is in the same day container

                        let prevShowExistenceArr = checkingExistenceOfPrevAndNextContainers(prevShowContainer, 'singleShowContainer');
                        let grandPrevCont = null;
                        if(prevShowExistenceArr[0]) {
                            grandPrevCont = prevShowExistenceArr[0];
                        }

                        if(grandPrevCont) { // grandprev container exist
                            let dayContOfGrandPrevShowContainer = grandPrevCont.closest('.singleDayContainer');
                            grandPrevDayFlag = dayContOfPrevShowContainer == dayContOfGrandPrevShowContainer ? false : true; //checking if next show is in the same day container
                        }

                        let nextShowContainer = null;

                        if(showExistenceArray[1]) {
                            nextShowContainer = showExistenceArray[1];
                        }

                        if(nextShowContainer) {
                            let dayContOfNextShowContainer = nextShowContainer.closest('.singleDayContainer');
                            nextShowFlag = dayContainer == dayContOfNextShowContainer ? false : true; //checking if next show is in the same day container
                        }

                        //main part
                        if(!siblingsCheckboxArr[0]) {
                            if(prevDayFlag) { //prev container is in previous day
                                if(grandPrevCont) { //grandprev container exist
                                    if(grandPrevDayFlag) { //grandprev is in grand previous day
                                        if(nextShowContainer) { //next container exist
                                            if(nextShowFlag) { //next container is in another day
                                                console.log('prev exist & prev day, grandprev exist & grandprev is in grandprevday, nextshowcontaierExist & in another day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                            else { //next container is in the same day
                                                console.log('prev exist & prev day, grandprev exist & grandprev is in grandprevday, nextshowcontaierExist & in same day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //next container doesn't exist
                                            console.log('prev exist & prev day, grandprev exist & grandprev is in grandprevday, nextshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(prevShowContainer, grandPrevCont, 100);
                                        }

                                    }
                                    else { //grandprev is in previous day(same as previousShowContainer)
                                        if(nextShowContainer) { //next container exist
                                            if(nextShowFlag) { //next container is in another day
                                                console.log('prev exist & prev day, grandprev exist & grandprev is same day as prev, nextshowcontaier Exist & in another day');
                                                let changeDistanceArr = ['undefined', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);

                                            }
                                            else { //next container is in the same day
                                                console.log('prev exist & prev day, grandprev exist & grandprev is same day as prev, nextshowcontaier Exist & in same day');
                                                let changeDistanceArr = ['undefined', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //next container doesn't exist
                                            console.log('prev exist & prev day, grandprev exist & grandprev is same day as prev, nextshowcontaier doesnt Exist');
                                            limitSelectsWhenExtreme(prevShowContainer, grandPrevCont, 30);
                                        }
                                    }
                                }
                                else { //grandprev doesn't exist
                                    if(nextShowContainer) { //next container exist
                                        if(nextShowFlag) { //next container is in another day
                                            console.log('prev exist & prev day, grandprev doesnt exist, nextshowcontaier Exist & in another day');
                                            //nic nie robie, ponieważ akcja ma miejsce w tym przypadku w przypadku dla nastepnego.
                                        }
                                        else { //next container is in the same day
                                            console.log('prev exist & prev day, grandprev doesnt exist, nextshowcontaier Exist & in same day');
                                            //nic nie robie, ponieważ akcja ma miejsce w tym przypadku w przypadku dla nastepnego.
                                        }
                                    }
                                    else { //next container doesn't exist
                                        console.log('prev exist & prev day, grandprev doesnt exist, nextshowcontaier doesnt Exist');
                                        allCitiesAndAllVoivodes(prevShowContainer);
                                    }
                                }
                            }
                            else { //prev container is in the same day
                                if(grandPrevCont) { // grandprev container exist
                                    if(grandPrevDayFlag) { //grandprev is in grand previous day
                                        if(nextShowContainer) { //next container exist
                                            if(nextShowFlag) { //next container is in another day
                                                console.log('prev exist & same day, grandprev exist & grandprev is in grandprevday, nextshowcontaier exist & in another day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                            else { //next container is in the same day
                                                console.log('prev exist & same day, grandprev exist & grandprev is in grandprevday, nextshowcontaier exist & in same day');
                                                let changeDistanceArr = [100, 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //next container doesn't exist
                                            console.log('prev exist & same day, grandprev exist & grandprev is in grandprevday, nextshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(prevShowContainer, grandPrevCont, 100);
                                        }
                                    }
                                    else { //grandprev is in previous day(same as previousShowContainer)(all containers are in same day container case)
                                        if(nextShowContainer) { //next container exist
                                            if(nextShowFlag) { //next container is in another day
                                                console.log('prev exist & same day, grandprev exist & grandprev is prev day, nextshowcontaier Exist & in another day');
                                                let changeDistanceArr = ['undefined', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                            else { //next container is in the same day
                                                console.log('prev exist & same day, grandprev exist & grandprev is prev day, nextshowcontaier Exist & in same day');
                                                let changeDistanceArr = ['undefined', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevCont, nextShowContainer, prevShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //next container doesn't exist
                                            console.log('prev exist & same day, grandprev exist & grandprev is prev day, nextshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(prevShowContainer, grandPrevCont, 30);
                                        }
                                    }
                                }
                                else { //grandprev container doesn't exist
                                    if(nextShowContainer) { //next container exist
                                        if(nextShowFlag) { //next container is in another day
                                            console.log('grandprev doesnt exist, next exist and another day');
                                            ////nic nie robie, ponieważ akcja ma miejsce w tym przypadku w przypadku dla nastepnego.
                                        }
                                        else { //next container is in the same day
                                            console.log('grandprev doesnt exist, next exist and same day');
                                            //nic nie robie, ponieważ akcja ma miejsce w tym przypadku w przypadku dla nastepnego.
                                        }
                                    }
                                    else { //next container doesn't exist
                                        console.log('grandprev doesnt exist, next doesnt exist');

                                        let prevShowVoivodeSelect = prevShowContainer.querySelector('.voivodeSelect');
                                        let prevVoivode = getSelectedValue(prevShowVoivodeSelect);
                                        let prevShowCitySelect = prevShowContainer.querySelector('.citySelect');
                                        let prevCity = getSelectedValue(prevShowCitySelect);
                                        allCitiesAndAllVoivodes(prevShowContainer);
                                        setOldValues(prevShowVoivodeSelect, prevVoivode, prevShowCitySelect, prevCity);
                                        // allCitiesAndAllVoivodes(prevShowContainer);
                                    }
                                }
                            }
                        }
                    }

                    if(showExistenceArray[1]) { //case when next container exist
                        let nextShowContainer = showExistenceArray[1];
                        let dayContOfNextShowContainer = nextShowContainer.closest('.singleDayContainer');
                        nextDayFlag = dayContainer == dayContOfNextShowContainer ? false : true; //checking if next show is in the same day container

                        let nextShowExistenceArr = checkingExistenceOfPrevAndNextContainers(nextShowContainer, 'singleShowContainer');
                        let grandNextCont = null;
                        if(nextShowExistenceArr[1]) {
                            grandNextCont = nextShowExistenceArr[1];
                        }

                        if(grandNextCont) { // grandprev container exist
                            let dayContOfGrandNextShowContainer = grandNextCont.closest('.singleDayContainer');
                            grandNextDayFlag = dayContOfNextShowContainer == dayContOfGrandNextShowContainer ? false : true; //checking if next show is in the same day container
                        }

                        let prevShowContainer = null;

                        if(showExistenceArray[0]) {
                            prevShowContainer = showExistenceArray[0];
                        }

                        if(prevShowContainer) {
                            let dayContOfPrevShowContainer = prevShowContainer.closest('.singleDayContainer');
                            prevShowFlag = dayContainer == dayContOfPrevShowContainer ? false : true; //checking if next show is in the same day container
                        }

                        //main part
                        if(!siblingsCheckboxArr[1]) { //checkbox is not selected
                            if(nextDayFlag) { //prev container is in previous day
                                if(grandNextCont) { //grandprev container exist
                                    if(grandNextDayFlag) { //grandprev is in grand previous day
                                        if(prevShowContainer) { //next container exist
                                            if(prevShowFlag) { //next container is in another day
                                                console.log('next exist & next day, grandnext exist & grandnext is in grandnextday, prevshowcontaierExist & in another day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else { //prev container is in the same day
                                                console.log('next exist & next day, grandnext exist & grandnext in grandnextday, prevshowcontaierExist & in same day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //prev container doesn't exist
                                            console.log('next exist & next day, grandnext exist & grandnext is in grandnextday, prevshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(nextShowContainer, grandNextCont, 100);
                                        }

                                    }
                                    else { //grandnext is in next day(same as nextShowContainer)
                                        if(prevShowContainer) { //prev container exist
                                            if(prevShowFlag) { //prev container is in another day
                                                console.log('next exist & next day, grandnext exist & grandnext is same day as next, prevshowcontaier Exist & in another day');
                                                let changeDistanceArr = ['undefined', 100]; //[dalszy, blizszy]
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);

                                            }
                                            else { //prev container is in the same day
                                                console.log('next exist & next day, grandnext exist & grandnext is same day as next, prevshowcontaier Exist & in same day');
                                                let changeDistanceArr = ['undefined', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //prev container doesn't exist
                                            console.log('next exist & next day, grandnext exist & grandnext is same day as prev, prevshowcontaier doesnt Exist');
                                            limitSelectsWhenExtreme(nextShowContainer, grandNextCont, 30);
                                        }
                                    }
                                }
                                else { //grandNext doesn't exist
                                    if(prevShowContainer) { //prev container exist
                                        if(prevShowFlag) { //prev container is in another day
                                            console.log('next exist & next day, grandnext doesnt exist, prevshowcontaier Exist & in another day');
                                            limitSelectsWhenExtreme(nextShowContainer, prevShowContainer, 100);
                                        }
                                        else { //prev container is in the same day
                                            console.log('next exist & next day, grandnext doesnt exist, prevshowcontaier Exist & in same day');
                                            limitSelectsWhenExtreme(nextShowContainer, prevShowContainer, 100);
                                        }
                                    }
                                    else { //prev container doesn't exist
                                        console.log('next exist & next day, grandnext doesnt exist, prevshowcontaier doesnt Exist');
                                        allCitiesAndAllVoivodes(nextShowContainer);
                                    }
                                }
                            }
                            else { //next container is in the same day
                                if(grandNextCont) { // grandnext container exist
                                    if(grandNextDayFlag) { //grandnext is in grand next day
                                        if(prevShowContainer) { //prev container exist
                                            if(prevShowFlag) { //prev container is in another day
                                                console.log('next exist & same day, grandnext exist & grandnext is in grandnextday, prevshowcontaier exist & in another day');
                                                let changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else { //prev container is in the same day
                                                console.log('next exist & same day, grandnext exist & grandnext is in grandnextday, prevshowcontaier exist & in same day');
                                                let changeDistanceArr = [100, 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //prev container doesn't exist
                                            console.log('next exist & same day, grandnext exist & grandnext is in grandnextday, prevshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(nextShowContainer, grandNextCont, 100);
                                        }
                                    }
                                    else { //grandnext is in next day(same as nextShowContainer)(all containers are in same day container case)
                                        if(prevShowContainer) { //prev container exist
                                            if(prevShowFlag) { //prev container is in another day
                                                console.log('next exist & same day, grandnext exist & grandnext is same day, prevshowcontaier Exist & in another day');
                                                let changeDistanceArr = ['undefined', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else { //prev container is in the same day
                                                console.log('next exist & same day, grandnext exist & grandnext is next day, prevshowcontaier Exist & in same day');
                                                let changeDistanceArr = ['undefined', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextCont, prevShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //prev container doesn't exist
                                            console.log('next exist & same day, grandnext exist & grandnext is same day, prevshowcontaier doesnt exist');
                                            limitSelectsWhenExtreme(nextShowContainer, grandNextCont, 30);
                                        }
                                    }
                                }
                                else { //grandnext container doesn't exist
                                    if(prevShowContainer) { //prev container exist
                                        if(prevShowFlag) { //prev container is in another day
                                            console.log('grandnext doesnt exist, next in same day, prev exist and another day');
                                            limitSelectsWhenExtreme(nextShowContainer, prevShowContainer, 100);
                                        }
                                        else { //next container is in the same day
                                            console.log('grandnext doesnt exist, prev exist and same day');
                                            limitSelectsWhenExtreme(nextShowContainer, prevShowContainer, 30);
                                        }
                                    }
                                    else { //prev container doesn't exist
                                        let nextShowVoivodeSelect = nextShowContainer.querySelector('.voivodeSelect');
                                        let nextVoivode = getSelectedValue(nextShowVoivodeSelect);
                                        let nextShowCitySelect = nextShowContainer.querySelector('.citySelect');
                                        let nextCity = getSelectedValue(nextShowCitySelect);
                                        allCitiesAndAllVoivodes(nextShowContainer);
                                        setOldValues(nextShowVoivodeSelect, nextVoivode, nextShowCitySelect, nextCity);
                                        // allCitiesAndAllVoivodes(nextShowContainer);
                                    }
                                }
                            }
                        }
                    }

                    const allRemoveButtons = dayContainer.getElementsByClassName('remove-button');
                    console.assert(allRemoveButtons, "Brak przycisków usuń");
                    if(allRemoveButtons.length > 1) { //delete only show box
                        showContainer.parentNode.removeChild(showContainer);
                    }
                    else if(allRemoveButtons.length === 1) { //delete day box
                        const allDayContainers = document.getElementsByClassName('singleDayContainer');
                        if(allDayContainers.length > 1) {
                            dayContainer.parentNode.removeChild(dayContainer);
                        }
                        else {
                            notify('Nie można usunąć pierwszego dnia!');
                        }

                    }
                }
                else if(e.target.matches('#addNewDay')) { // user clicks on 'add new day' button
                    let firstDay = new DayBox();
                    firstDay.createDOMDayBox();
                    let firstDayContainer = firstDay.getBox();
                    const allDayContainers = document.getElementsByClassName('singleDayContainer');
                    const lastDayContainer = allDayContainers[allDayContainers.length - 1];
                    const allSingleShowContainers = document.getElementsByClassName('singleShowContainer');
                    const allSingleShowContainersInsideLastDayContainer = lastDayContainer.querySelectorAll('.singleShowContainer');
                    const lastShowContainerInsideLastDay = allSingleShowContainersInsideLastDayContainer[allSingleShowContainersInsideLastDayContainer.length - 1];
                    const isChecked = lastShowContainerInsideLastDay.querySelector('.distance-checkbox').checked;

                    let correctDate = getCorrectDate(allDayContainers.length);

                    let validate = validateForm(allSingleShowContainers[allSingleShowContainers.length - 1]);

                    if(validate) {
                        let firstForm = new ShowBox();
                        firstForm.addRemoveShowButton();
                        firstForm.addDistanceCheckbox();
                        firstForm.addNewShowButton();
                        lastDayContainer.insertAdjacentElement("afterend", firstDayContainer).scrollIntoView({behavior: "smooth"});
                        if(isChecked) { // case when last single show container has checked checkbox;
                            firstForm.createDOMBox(correctDate);
                        }
                        else {
                            const allCitiesSelect = document.getElementsByClassName('citySelect');
                            const selectedCity = allCitiesSelect[allCitiesSelect.length - 1];
                            const selectedCityId = getSelectedValue(selectedCity);
                            firstForm.createDOMBox(correctDate, 100, selectedCityId);

                        }
                        let firstFormDOM = firstForm.getForm();
                        firstDayContainer.appendChild(firstFormDOM).scrollIntoView({behavior: "smooth"});
                    }
                    else {
                        notify('Uzupełnij miasto');
                    }

                }
                else if(e.target.matches('#add-new-route')) {
                    e.target.disabled = true; //disable button
                    let placeToAppend = document.querySelector('.route-here');
                    placeToAppend.innerHTML = '';

                    let firstDay = new DayBox();
                    firstDay.createDOMDayBox();
                    let firstDayContainer = firstDay.getBox();
                    placeToAppend.insertAdjacentElement("afterbegin", firstDayContainer).scrollIntoView({behavior: "smooth"});

                    if(document.getElementsByClassName('singleDayContainer')) {
                        let allDayContainers = document.getElementsByClassName('singleDayContainer');
                        if(allDayContainers.length === 1) {
                            let buttonSection = new ButtonBox();
                            buttonSection.appendAddNewDayButton();
                            // buttonSection.appendSaveButton();
                            let elButtonSection = buttonSection.getBox();
                            placeToAppend.insertAdjacentElement('beforeend', elButtonSection);
                        }
                    }
                    else {
                        let buttonSection = new ButtonBox();
                        buttonSection.appendAddNewDayButton();
                        let elButtonSection = buttonSection.getBox();
                        placeToAppend.insertAdjacentElement('afterend', elButtonSection);
                    }

                    let correctDate = getCorrectDate();

                    let firstForm = new ShowBox();
                    firstForm.addRemoveShowButton();
                    firstForm.addDistanceCheckbox();
                    firstForm.addNewShowButton();
                    firstForm.createDOMBox(correctDate);
                    let firstFormDOM = firstForm.getForm();

                    firstDayContainer.appendChild(firstFormDOM).scrollIntoView({behavior: "smooth"});
                    e.target.disabled = false; //enable button
                }
                else if(e.target.matches('#save')) {
                    let submitPlace = document.querySelector('.client-container');
                    const allSingleShowContainers = document.querySelectorAll('.singleShowContainer');
                    const allSingleDayContainers = document.getElementsByClassName('singleDayContainer');
                    let finalArray = [];

                    let isOk = validateAllForms(allSingleShowContainers, true); //validation(hours != 0, selected city, selected voivode)

                    if(isOk) {
                        const clientTypeValue = $('#client_choice_type option:selected').val();
                        const clientTable = document.querySelector('#table_client');
                        let selectedCheckbox;
                        if(clientTable.querySelector('input[type="checkbox"]:checked')) {
                            selectedCheckbox = clientTable.querySelector('input[type="checkbox"]:checked');
                        }
                        else {
                            notify('Wybierz klienta!');
                            return false;
                        }
                        const selectedTr = selectedCheckbox.closest('tr');
                        let clientId = selectedTr.id;
                        clientId = clientId.substr(9);

                        const clientInfo = {
                            clientId: clientId,
                            clientType: clientTypeValue
                        };

                        for(let i = 0; i < allSingleDayContainers.length; i++) {
                            let singleShowContainersInsideGivenDay = allSingleDayContainers[i].querySelectorAll('.singleShowContainer');
                            let fullDate = allSingleDayContainers[i].querySelector('.day-info').textContent;
                            let date = fullDate.substr(6);

                            singleShowContainersInsideGivenDay.forEach((show, index) => {
                                let voivodeSelect = show.querySelector('.voivodeSelect');
                                let voivodeId = getSelectedValue(voivodeSelect);

                                let citySelect = show.querySelector('.citySelect');
                                let cityId = getSelectedValue(citySelect);

                                let checkboxElement = show.querySelector('.distance-checkbox');
                                let checkboxVal = checkboxElement.checked ? 1 : 0;

                                let hourElement = show.querySelector('.show-hours');
                                let hourNumber = hourElement.value;

                                let info = {
                                    order: index,
                                    date: date,
                                    hours: hourNumber,
                                    voivode: voivodeId,
                                    city: cityId,
                                    checkbox: checkboxVal
                                }
                                finalArray.push(info);
                            });
                        }
                        let JSONData = JSON.stringify(finalArray);
                        let JSONClientInfo = JSON.stringify(clientInfo);
                        let finalForm = document.createElement('form');
                        finalForm.setAttribute('method', 'post');
                        finalForm.setAttribute('action', `{{url()->current()}}`);
                        finalForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="alldata" value=' + JSONData + '> <input type="hidden" name="clientInfo" value=' + JSONClientInfo + '>';
                        submitPlace.appendChild(finalForm);
                        finalForm.submit();
                    }
                    else {
                        notify('Wypełnij wszystkie pola');
                    }
                }
                else if (e.target.matches('.show-cities-statistics')) { //after clicking on search glyphicon, open modal with cities.
                    const thisSingleShowContainer = e.target.closest('.singleShowContainer');
                    const thisSingleDayContainer = e.target.closest('.singleDayContainer');
                    const citySelect = thisSingleShowContainer.querySelector('.citySelect');
                    const selectedCity = getSelectedValue(citySelect);

                    const dateContainer = thisSingleDayContainer.querySelector('.day-info');
                    const fullDate = dateContainer.textContent;

                    const selectedDate = fullDate.substr(6);

                    const url = `{{route('api.getClientRouteInfoRecord')}}`;
                    const ourHeaders = new Headers();
                    ourHeaders.append('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

                    const ajaxData = new FormData();
                    ajaxData.append('city_id', selectedCity);
                    ajaxData.append('date', selectedDate);

                    fetch(url, {
                        method: 'post',
                        headers: ourHeaders,
                        body: ajaxData,
                        credentials: "same-origin"
                    })
                        .then(resp => resp.json())
                        .then(resp => {
                            const modalBody = document.querySelector('.modal2-body');
                            modalBody.innerHTML = '';
                            createModalTable(modalBody, resp);
                            return resp.length;
                        })
                        .then(numberOfElements => {
                            const modalBody = document.querySelector('.modal2-body');
                            const info = document.createElement('div');
                            info.classList.add('alert', 'alert-info', 'loadedMessage');
                            if(selectedCity == 0) {
                                info.textContent = "Miasto nie zostało wybrane";
                            }
                            else if (numberOfElements === 0) {
                                info.textContent = "Miasto nie zostało wykorzystane w przeciągu ostatniego miesiąca";
                            }
                            else {
                                info.textContent = "Wykorzystanie miasta w ciągu miesiąca wstecz i w przód";
                            }

                            modalBody.appendChild(info);
                            $('#showRecords').modal('show');
                        })

                }
                else if(e.target.matches('#remove-route')) {
                    swal({
                        title: 'Jesteś pewien?',
                        text: "Brak możliwości cofnięcia zmian!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Tak, usuń!'
                    }).then((result) => {
                        if (result.value) {

                            const ourHeaders = new Headers();
                            ourHeaders.append('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

                            fetch('{{URL::current()}}', {
                                method: 'delete',
                                headers: ourHeaders,
                                credentials: "same-origin"
                            })
                                .then(resp => resp.text())
                                .then(resp => {
                                    swal(
                                        'Usunięto!',
                                        'Trasa została zdezaktywowana',
                                        'success'
                                    )
                                    window.location.href = '{{URL::to('/showClientRoutes')}}';
                                })
                        }
                    })
                }
            }

            /**
             * This event listener is responsible for change event on document
             * @param e
             */
            function globalChangeHandler(e) {
                if(e.target.matches('.distance-checkbox')) {
                    let isChecked = e.target.checked;
                    let previousSingleShowContainer = null;
                    let nextSingleShowContainer = null;
                    const thisSingleShowContainer = e.target.closest('.singleShowContainer');

                    const dayContainer = thisSingleShowContainer.closest('.singleDayContainer');
                    const fullDate = dayContainer.querySelector('.day-info').textContent;
                    let correctDate = fullDate.substr(6); //YYYY-MM-DD

                    let voivodeSelect = thisSingleShowContainer.querySelector('.voivodeSelect');
                    voivodeSelect.innerHTML = ''; //clear select
                    let citySelect = thisSingleShowContainer.querySelector('.citySelect');
                    citySelect.innerHTML = ''; //clear select

                    $(voivodeSelect).off();

                    voivodeSelect = thisSingleShowContainer.querySelector('.voivodeSelect');

                    appendBasicOption(citySelect);
                    appendBasicOption(voivodeSelect);

                    if(isChecked) { // activate no distance limit option
                        let existenceArr = checkingExistenceOfPrevAndNextContainers(thisSingleShowContainer, 'singleShowContainer');

                        citySelect.setAttribute('data-previousdistance', citySelect.dataset.distance);
                                @foreach($voivodes as $voivode)
                        var singleVoivode = document.createElement('option');
                        singleVoivode.value = {{$voivode->id}};
                        singleVoivode.textContent = '{{$voivode->name}}';
                        voivodeSelect.appendChild(singleVoivode);
                        @endforeach()
                        citySelect.setAttribute('data-distance', 'infinity');
                        $(voivodeSelect).on('change', function(e) {
                            let voivodeId = e.target.value;
                            showWithoutDistanceAjax(voivodeId, citySelect, correctDate);
                        });

                        if(existenceArr[0]) {
                            globalSwalFlag = true;
                            let prevVoivodeSelect = existenceArr[0].querySelector('.voivodeSelect');
                            let prevVoivodeId = getSelectedValue(prevVoivodeSelect);
                            let prevCitySelect = existenceArr[0].querySelector('.citySelect');
                            let prevCityId = getSelectedValue(prevCitySelect);
                            prevVoivodeSelect.innerHTML = '';
                            prevCitySelect.innerHTML = '';
                            let defaults = {voivode: prevVoivodeId};
                            allCitiesAndAllVoivodes(existenceArr[0], defaults);
                            setOldValues(prevVoivodeSelect, prevVoivodeId, prevCitySelect, prevCityId);
                            globalSwalFlag = false;
                        }
                        if(existenceArr[1]) {
                            globalSwalFlag = true;
                            let nextVoivodeSelect = existenceArr[1].querySelector('.voivodeSelect');
                            let nextVoivodeId = getSelectedValue(nextVoivodeSelect);
                            let nextCitySelect = existenceArr[1].querySelector('.citySelect');
                            let nextCityId = getSelectedValue(nextCitySelect);
                            nextVoivodeSelect.innerHTML = '';
                            nextCitySelect.innerHTML = '';
                            let defaults = {voivode: nextVoivodeId};
                            allCitiesAndAllVoivodes(existenceArr[1], defaults);
                            setOldValues(nextVoivodeSelect, nextVoivodeId, nextCitySelect, nextCityId);
                            globalSwalFlag = false;
                        }

                    }
                    else { //deactivate no distance limit option
                        const allSingleShowContainers = document.getElementsByClassName('singleShowContainer');
                        for(let i = 0; i < allSingleShowContainers.length; i++) {
                            if(thisSingleShowContainer == allSingleShowContainers[i]) {
                                if(allSingleShowContainers[i-1]) {
                                    previousSingleShowContainer = allSingleShowContainers[i-1];
                                }
                                if(allSingleShowContainers[i+1]) {
                                    nextSingleShowContainer = allSingleShowContainers[i+1];
                                }
                            }
                        }

                        if(previousSingleShowContainer === null && nextSingleShowContainer === null) { //there is only one show
                                    @foreach($voivodes as $voivode)
                            var singleVoivode = document.createElement('option');
                            singleVoivode.value = {{$voivode->id}};
                            singleVoivode.textContent = '{{$voivode->name}}';
                            voivodeSelect.appendChild(singleVoivode); //password_date
                            @endforeach()

                            $(voivodeSelect).on('change', function(e) {
                                citySelect.setAttribute('data-distance', 'infinity');
                                let voivodeId = e.target.value;
                                showWithoutDistanceAjax(voivodeId, citySelect, correctDate);
                            });
                        }
                        else if(previousSingleShowContainer !== null && nextSingleShowContainer === null) { //case when show is last one dziala
                            const previousCitySelect = previousSingleShowContainer.querySelector('.citySelect');
                            const previousCityId = getSelectedValue(previousCitySelect);
                            const previousShowDayContainer = previousCitySelect.closest('.singleDayContainer');
                            const date = previousShowDayContainer.querySelector('.day-info').textContent;
                            // console.log('date ', date);
                            showInExtreme(citySelect.dataset.previousdistance, previousCityId, date, citySelect, voivodeSelect);
                        }
                        else if(previousSingleShowContainer === null && nextSingleShowContainer !== null) { //case when show is first one
                            const nextCitySelect = nextSingleShowContainer.querySelector('.citySelect');
                            const nextCityId = getSelectedValue(nextCitySelect);
                            const nextShowDayContainer = nextCitySelect.closest('.singleDayContainer');
                            const date = nextShowDayContainer.querySelector('.day-info').textContent;
                            showInExtreme(30, nextCityId, date, citySelect, voivodeSelect);
                        }
                        else if(previousSingleShowContainer !== null && nextSingleShowContainer !== null) { //case when show is in the middle
                            const previousCitySelect = previousSingleShowContainer.querySelector('.citySelect');
                            const previousCityDistance = previousCitySelect.dataset.distance;
                            const previousCityId = getSelectedValue(previousCitySelect);

                            const nextCitySelect = nextSingleShowContainer.querySelector('.citySelect');
                            const nextCityDistance = nextCitySelect.dataset.distance;
                            const nextCityId = getSelectedValue(nextCitySelect);

                            showInTheMiddleAjax(previousCityDistance, previousCityId, nextCityDistance, nextCityId, citySelect, voivodeSelect, thisSingleShowContainer);
                        }

                    }
                }
                else if(e.target.matches('.citySelect')) { // user changes city
                    const citySelect = e.target;
                    const thisSingleShowContainer = citySelect.closest('.singleShowContainer');
                    const thisDayContainer = citySelect.closest('.singleDayContainer');
                    const thisContainerCheckbox = thisSingleShowContainer.querySelector('.distance-checkbox');
                    const isCheckedThisContainer = thisContainerCheckbox.checked;

                    const selectedCity = citySelect.options[citySelect.selectedIndex];
                    const maxHour = selectedCity.dataset.max_hours;
                    let showHoursSelect = thisSingleShowContainer.querySelector('.show-hours');
                    showHoursSelect.value = maxHour;

                    let previousShowContainer = undefined;
                    let nextShowContainer = undefined;

                    let nextDayFlag = null; //indices that next show container is in the same day container (false - same day, true - another day)
                    let prevDayFlag = null; //indices that prev show container is in the same day container (false - same day, true - another day)
                    let grandPrevDayFlag = null; //indices that grandPrev show container is in the same day container (false - same day, true - another day)
                    let grandNextDayFlag = null; //indices that grandNext show container is in the same day container (false - same day, true - another day)

                    let siblingShowContainersArr = [];
                    let siblingCheckboxArr = [];

                    siblingShowContainersArr = checkingExistenceOfPrevAndNextContainers(thisSingleShowContainer, 'singleShowContainer');
                    siblingCheckboxArr = checkboxFilter(siblingShowContainersArr);

                    previousShowContainer = siblingShowContainersArr[0] === null ? null : siblingShowContainersArr[0];
                    nextShowContainer = siblingShowContainersArr[1] === null ? null : siblingShowContainersArr[1];

                    if(!isCheckedThisContainer) { // if this container doesn't have checkbox checked
                        if(nextShowContainer) { //case when next show exist.
                            if(siblingCheckboxArr[1] === false) { //next show container doesn't have checked distance checkbox
                                let dayContainerOfNextShowContainer = nextShowContainer.closest('.singleDayContainer');
                                nextDayFlag = dayContainerOfNextShowContainer == thisDayContainer ? false : true; //checking if next show is in the same day container
                                let grandNextShowContainer = undefined; // previous show container of previous show container
                                let prevShowContainerRelatedToNextShowContainer = thisSingleShowContainer;
                                let siblingsOfNextShowContainerArr = checkingExistenceOfPrevAndNextContainers(nextShowContainer, 'singleShowContainer');
                                grandNextShowContainer = siblingsOfNextShowContainerArr[1] === null ? null : siblingsOfNextShowContainerArr[1];
                                let nextSiblingCheckboxArr = checkboxFilter(siblingsOfNextShowContainerArr);

                                if(nextDayFlag) { //case when next show is in another day container
                                    if(grandNextShowContainer) { // there is prev container and next container (related to next show container)
                                        let dayContainerOfGrandNextShowContainer = grandNextShowContainer.closest('.singleDayContainer');
                                        grandNextDayFlag = dayContainerOfGrandNextShowContainer == dayContainerOfNextShowContainer ? false : true; //checking if grandnext show is in the same day container as next show
                                        if(grandNextDayFlag) { //case when grand show is another day
                                            if(!nextSiblingCheckboxArr[1]) { //grand is not checked
                                                let changeDistanceArr = [100,'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else { //grand is checked
                                                let changeDistanceArr = ['infinity','undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //grand is same day
                                            if(!nextSiblingCheckboxArr[1]) { //grand is not checked
                                                let changeDistanceArr = [30,'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else { //grand is checked
                                                let changeDistanceArr = ['infinity','undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                    }
                                    else { // there is no next container (related to prev show container)
                                        let changeDistanceArr = [100,100];
                                        limitSelectsWhenBetweenSameDayContainer(nextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                        // limitSelectsWhenExtreme(nextShowContainer, prevShowContainerRelatedToNextShowContainer, 100);
                                    }
                                }
                                else { //case when next show is in the same day container
                                    if(grandNextShowContainer) { // there is prev container and next container (related to next show container)
                                        let dayContainerOfGrandNextShowContainer = grandNextShowContainer.closest('.singleDayContainer');
                                        grandNextDayFlag = dayContainerOfGrandNextShowContainer == dayContainerOfNextShowContainer ? false : true; //checking if grandnext show is in the same day container as next show
                                        if(grandNextDayFlag) { //grandnext show is in another day container related to next show
                                            if(!nextSiblingCheckboxArr[1]) { //grand is not checked
                                                let changeDistanceArr = [100, 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                            else {
                                                let changeDistanceArr = ['infinity', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //grandnext show is in the same day container as next show
                                            if(!nextSiblingCheckboxArr[1]) { //grand is not checked
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer);
                                            }
                                            else {
                                                let changeDistanceArr = ['infinity', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandNextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);
                                            }
                                        }
                                    }
                                    else { // there is no next container (related to next show container)
                                        let changeDistanceArr = [30,30];
                                        limitSelectsWhenBetweenSameDayContainer(nextShowContainer, thisSingleShowContainer, nextShowContainer, changeDistanceArr);

                                        // limitSelectsWhenExtreme(nextShowContainer, prevShowContainerRelatedToNextShowContainer, 30);
                                    }
                                }
                            }
                        }

                        //case when prev show exist.
                        if(previousShowContainer) {
                            if(siblingCheckboxArr[0] === false) { //prev show container doesn't have checked distance checkbox
                                let dayContainerOfPreviousShowContainer = previousShowContainer.closest('.singleDayContainer');
                                prevDayFlag = dayContainerOfPreviousShowContainer == thisDayContainer ? false : true; //checking if prev show is in the same day container
                                let nextShowContainerRelatedToPreviousShowContainer = thisSingleShowContainer;
                                let siblingsOfPreviousShowContainerArr = checkingExistenceOfPrevAndNextContainers(previousShowContainer, 'singleShowContainer');
                                let grandPrevShowContainer = undefined; // previous show container of previous show container
                                let prevSiblingCheckboxArr = checkboxFilter(siblingsOfPreviousShowContainerArr);
                                grandPrevShowContainer = siblingsOfPreviousShowContainerArr[0] === null ? null : siblingsOfPreviousShowContainerArr[0];

                                if(prevDayFlag) { //case when prev show is in another day container
                                    if(grandPrevShowContainer) { // there is previous container and next container (related to prev show container)
                                        let dayContainerOfGrandPrev = grandPrevShowContainer.closest('.singleDayContainer');
                                        grandNextDayFlag = dayContainerOfGrandPrev === dayContainerOfPreviousShowContainer ? false : true;
                                        let changeDistanceArr = [];
                                        if(grandNextDayFlag) {// case when grand prev show is another day related to prev show
                                            if(!prevSiblingCheckboxArr[0]) { //grand is not checked
                                                changeDistanceArr = [100, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                            else {
                                                changeDistanceArr = ['infinity', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //case when grand prev show is in same day container as prev show
                                            if(!prevSiblingCheckboxArr[0]) { //grand is not checked
                                                changeDistanceArr = [30, 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                            else {
                                                changeDistanceArr = ['infinity', 100];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                        }
                                    }
                                    else { // there is no previous container (related to prev show container)
                                        let changeDistanceArr = [100, 100];
                                        limitSelectsWhenBetweenSameDayContainer(previousShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                        //
                                        // console.log('c45');
                                        // limitSelectsWhenExtreme(previousShowContainer, nextShowContainerRelatedToPreviousShowContainer, 100);
                                    }

                                }
                                else { //case when prev show is in the same day container
                                    if(grandPrevShowContainer) { // there is previous container and next container (related to prev show container)
                                        let dayContainerOfGrandPrevShowContainer = grandPrevShowContainer.closest('.singleDayContainer');
                                        grandPrevDayFlag = dayContainerOfGrandPrevShowContainer == dayContainerOfPreviousShowContainer ? false : true; //checking if grandprev show is in the same day container as prev show
                                        if(grandPrevDayFlag) { //grandprev show is in another day container related to prev show
                                            if(!prevSiblingCheckboxArr[0]) { //grand is not checked
                                                let changeDistanceArr = [100, 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                            else {
                                                let changeDistanceArr = ['infinity', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                        }
                                        else { //grandprev show is in the same day container as prev show
                                            if(!prevSiblingCheckboxArr[0]) { //grand is not checked
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer);
                                            }
                                            else {
                                                let changeDistanceArr = ['infinity', 'undefined'];
                                                limitSelectsWhenBetweenSameDayContainer(grandPrevShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                            }
                                        }
                                    }
                                    else { // there is no previous container (related to prev show container)
                                        let changeDistanceArr = [30, 30];
                                        limitSelectsWhenBetweenSameDayContainer(previousShowContainer, thisSingleShowContainer, previousShowContainer, changeDistanceArr);
                                        // limitSelectsWhenExtreme(previousShowContainer, nextShowContainerRelatedToPreviousShowContainer, 30);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $(document).on('click', buttonHandler);
            $(document).on('change', globalChangeHandler);

            $('.form_date').on('change.dp', function (e) {
                currentDate = e.target.value;
                table.ajax.reload();
            });


            /**
             * This method launch as DOM loads
             */
            (function init() {
                globalSwalFlag = true;
                swal({
                    title: 'Ładowawnie...',
                    text: 'To może chwilę zająć',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                });

                //route generation part
                const response = @json($clientRouteInfo);
                let placeToAppend = document.querySelector('.route-here');
                placeToAppend.innerHTML = '';
                let dateFlag = null; //indices day change
                let dayBox = null;
                let dayContainer = null;
                for (let i = 0, respLength = response.length; i < respLength; i++) {
                    if (i === 0) { //first iteration
                        console.log('warunek na raz');
                        const dateInput = document.querySelector('#date');
                        dateInput.value = response[i].date;

                        dayBox = new DayBox();
                        dayBox.createDOMDayBox();
                        dayContainer = dayBox.getBox();
                        placeToAppend.insertAdjacentElement("beforeend", dayContainer);

                        let formEl = new ShowBox();
                        formEl.addRemoveShowButton();
                        formEl.addDistanceCheckbox();
                        formEl.addNewShowButton();
                        formEl.createDOMBox(response[i].date);

                        globalDateIndicator = response[i].date;
                        let firstFormDOM = formEl.getForm();

                        let citySelect = firstFormDOM.querySelector('.citySelect');
                        let voivodeSelect = firstFormDOM.querySelector('.voivodeSelect');
                        let hourInput = firstFormDOM.querySelector('.show-hours');
                        const voivodeId = response[i].voivodeId;
                        const cityId = response[i].cityId;
                        const showHours = response[i].hours;

                        showWithoutDistanceAjax(voivodeId, citySelect, response[i].date);

                        dayContainer.appendChild(firstFormDOM).scrollIntoView({behavior: "smooth"});

                        if(response[i].checkbox == 1) { //case when checkbox need to be checked
                            let checkboxElement = firstFormDOM.querySelector('.distance-checkbox');
                            if(!checkboxElement.checked) {
                                $(checkboxElement).trigger('click');
                            }
                        }

                        $(voivodeSelect).val(voivodeId).trigger('change');
                        $(citySelect).val(cityId).trigger('change');
                        hourInput.value = showHours;

                        dateFlag = response[i].date;

                        //Adding button section
                        let buttonSection = new ButtonBox();
                        buttonSection.appendAddNewDayButton();
                        let elButtonSection = buttonSection.getBox();

                        placeToAppend.insertAdjacentElement('beforeend', elButtonSection);
                    }
                    else if (dateFlag !== response[i].date && i !== 0) { //case when next container is in the next day
                        let addNewDayButton = $('#addNewDay');
                        addNewDayButton.trigger('click');

                        let allDayContainers2 = document.getElementsByClassName('singleDayContainer');
                        let lastDayContainer = allDayContainers2[allDayContainers2.length - 1];

                        let allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                        let lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                        let lastShowExistenceArr = checkingExistenceOfPrevAndNextContainers(lastShowContainer, 'singleShowContainer');

                        const citySelect = lastShowContainer.querySelector('.citySelect');
                        const voivodeSelect = lastShowContainer.querySelector('.voivodeSelect');
                        let hourInput = lastShowContainer.querySelector('.show-hours');
                        const voivodeId = response[i].voivodeId;
                        const cityId = response[i].cityId;
                        const showHours = response[i].hours;

                        if(response[i].checkbox == 1) { //case when checkbox need to be checked
                            let prevShowContainer = lastShowExistenceArr[0];
                            let previousShowVoivodeSelect = prevShowContainer.querySelector('.voivodeSelect');
                            const previousShowVoivodeId = getSelectedValue(previousShowVoivodeSelect);
                            const previousShowCitySelect = prevShowContainer.querySelector('.citySelect');
                            const previousShowCityId = getSelectedValue(previousShowCitySelect);

                            let checkboxElement = lastShowContainer.querySelector('.distance-checkbox');
                            $(checkboxElement).trigger('click');
                            previousShowVoivodeSelect = prevShowContainer.querySelector('.voivodeSelect');
                            setOldValues(previousShowVoivodeSelect, previousShowVoivodeId, previousShowCitySelect, previousShowCityId);
                        }
                        $(voivodeSelect).val(voivodeId).trigger('change');
                        $(citySelect).val(cityId).trigger('change');
                        hourInput.value = showHours;
                        dateFlag = response[i].date;
                    }
                    else { // case when next show is in the same day container
                        let allDayContainers = document.getElementsByClassName('singleDayContainer');
                        let lastDayContainer = allDayContainers[allDayContainers.length - 1];
                        let allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                        let lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                        let addNextShowButton = lastShowContainer.querySelector('.addNewShowButton');
                        $(addNextShowButton).trigger('click');

                        //we need to select new container, which appear after triggering click
                        allDayContainers = document.getElementsByClassName('singleDayContainer');
                        lastDayContainer = allDayContainers[allDayContainers.length - 1];
                        allShowContainersInsideLastDayContainer = lastDayContainer.getElementsByClassName('singleShowContainer');
                        lastShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 1];

                        if(response[i].checkbox == 1) { //case when checkbox need to be checked
                            const previousShowContainer = allShowContainersInsideLastDayContainer[allShowContainersInsideLastDayContainer.length - 2];
                            let previousShowVoivodeSelect = previousShowContainer.querySelector('.voivodeSelect');
                            const previousShowVoivodeId = getSelectedValue(previousShowVoivodeSelect);
                            const previousShowCitySelect = previousShowContainer.querySelector('.citySelect');
                            const previousShowCityId = getSelectedValue(previousShowCitySelect);

                            let checkboxElement = lastShowContainer.querySelector('.distance-checkbox');
                            $(checkboxElement).trigger('click');

                            previousShowVoivodeSelect = previousShowContainer.querySelector('.voivodeSelect');
                            setOldValues(previousShowVoivodeSelect, previousShowVoivodeId, previousShowCitySelect, previousShowCityId);
                        }

                        const citySelect = lastShowContainer.querySelector('.citySelect');
                        const voivodeSelect = lastShowContainer.querySelector('.voivodeSelect');
                        const voivodeId = response[i].voivodeId;
                        const cityId = response[i].cityId;
                        let hourInput = lastShowContainer.querySelector('.show-hours');
                        const showHours = response[i].hours;

                        $(voivodeSelect).val(voivodeId).trigger('change');
                        $(citySelect).val(cityId).trigger('change');
                        hourInput.value = showHours;
                        dateFlag = response[i].date;
                    }
                }
                notify('Trasa została w pełni załadowana!');
                globalSwalFlag = false;
                swal.close();
            })();

        });


    </script>
@endsection
