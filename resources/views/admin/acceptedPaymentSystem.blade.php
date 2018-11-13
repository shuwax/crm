@php
    /**
     * Created by PhpStorm.
     * User: veronaprogramista
     * Date: 12.11.18
     * Time: 10:22
     */
@endphp

{{--/*--}}
{{--*@category: ,--}}
{{--*@info: This template view is for copy purpose--}}
{{--*@controller: ,--}}
{{--*@methods: , --}}
{{--*/--}}

@extends('layouts.main')
@section('style')
    <link rel="stylesheet" href="{{asset('/css/fixedHeader.dataTables.min.css')}}">
    <link href="{{ asset('/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/assets/css/VCtooltip.css')}}">
@endsection
@section('content')
    <div class="page-header">
        <div class="alert gray-nav ">Panel administratorski</div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Przepisywanie rekordów z bazy na nowy system akceptowania wypłat
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <button id="rewriteButton" class="form-control btn btn-primary">Przepisz konsultantów</button>
                </div>
                <div class="col-md-6">
                    <button id="rewriteButtonCadre" class="form-control btn btn-primary">Przepisz kadre</button>
                </div>
            </div>
            <hr>
            <label for="changeLog">Change log:</label>
            <textarea id="changeLog" style="resize: none; width: 100%; height: 20em; overflow: auto"></textarea>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let VARIABLES = {
                months: <?php echo json_encode($months)?>,
                counter: 0,
                jQElements:{
                    rewriteButtonCadre: $('#rewriteButtonCadre'),
                    rewriteButton: $('#rewriteButton'),
                    changeLog: $('#changeLog')
                },
                DATA_TABLES: {}
            };

            let FUNCTIONS = {
                /* function grups should be before other functions which aren't grouped */
                EVENT_HANDLERS: {
                    callEvents(){
                        VARIABLES.jQElements.rewriteButtonCadre.click(function (e) {
                            swal({
                                type: 'warning',
                                title: 'Czy na pewno przepisać na nowy system?',
                                showCancelButton: true
                            }).then((result)=>{
                                $(e.target).prop('disabled', true);
                                VARIABLES.jQElements.changeLog.append('Kadra start\n');
                                FUNCTIONS.AJAXs.acceptedPaymentSystemUpdateCadreAjax();
                            });
                        });
                        VARIABLES.jQElements.rewriteButton.click(function (e) {
                            swal({
                                type: 'warning',
                                title: 'Czy na pewno przepisać na nowy system?',
                                showCancelButton: true
                            }).then((result)=>{
                                $(e.target).prop('disabled', true);
                                VARIABLES.jQElements.changeLog.append('Konsultant start\n');
                                FUNCTIONS.AJAXs.acceptedPaymentSystemUpdateAjax();
                            });
                        });
                    }
                },
                AJAXs: {
                    acceptedPaymentSystemUpdateCadreAjax(){
                        return $.ajax({
                            url: "{{ route('api.acceptedPaymentSystemUpdateCadreAjax') }}",
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            data: {
                                month: VARIABLES.months[VARIABLES.counter]
                            },
                            success: function (response) {
                                console.log(VARIABLES.counter, VARIABLES.months.length);
                                FUNCTIONS.fillChangeLogWithResponse(response);
                                FUNCTIONS.scrollTextarea(VARIABLES.jQElements.changeLog);
                                VARIABLES.counter++;
                                if(VARIABLES.counter === VARIABLES.months.length){
                                    VARIABLES.counter = 0;
                                    VARIABLES.jQElements.rewriteButtonCadre.prop('disabled', false);
                                    VARIABLES.jQElements.changeLog.append('Kadra stop\n');
                                }else{
                                    FUNCTIONS.AJAXs.acceptedPaymentSystemUpdateCadreAjax();
                                }
                            },
                            error: function (jqXHR, textStatus, thrownError) {
                                console.log(jqXHR);
                                console.log('textStatus: ' + textStatus);
                                console.log('thrownError: ' + thrownError);
                                swal({
                                    type: 'error',
                                    title: 'Błąd ' + jqXHR.status,
                                    text: 'Wystąpił błąd: ' + thrownError+' "'+(typeof jqXHR.responseJSON === 'undefined' ? '': jqXHR.responseJSON.message )+'"',
                                });
                                VARIABLES.jQElements.rewriteButtonCadre.prop('disabled', false);
                                console.log(VARIABLES.counter++ );
                            }
                        });
                    },
                    acceptedPaymentSystemUpdateAjax(){
                        return $.ajax({
                            url: "{{ route('api.acceptedPaymentSystemUpdateAjax') }}",
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            data: {
                                month: VARIABLES.months[VARIABLES.counter]
                            },
                            success: function (response) {
                                console.log(VARIABLES.counter, VARIABLES.months.length);
                                FUNCTIONS.fillChangeLogWithResponse(response);
                                FUNCTIONS.scrollTextarea(VARIABLES.jQElements.changeLog);
                                VARIABLES.counter++;
                                if(VARIABLES.counter === VARIABLES.months.length){
                                    VARIABLES.counter = 0;
                                    VARIABLES.jQElements.rewriteButton.prop('disabled', false);
                                    VARIABLES.jQElements.changeLog.append('Konsultant stop\n');
                                }else{
                                    FUNCTIONS.AJAXs.acceptedPaymentSystemUpdateAjax();
                                }
                            },
                            error: function (jqXHR, textStatus, thrownError) {
                                console.log(jqXHR);
                                console.log('textStatus: ' + textStatus);
                                console.log('thrownError: ' + thrownError);
                                swal({
                                    type: 'error',
                                    title: 'Błąd ' + jqXHR.status,
                                    text: 'Wystąpił błąd: ' + thrownError+' "'+(typeof jqXHR.responseJSON === 'undefined' ? '': jqXHR.responseJSON.message )+'"',
                                });
                                VARIABLES.jQElements.rewriteButton.prop('disabled', false);
                                console.log(VARIABLES.counter++ );
                            }
                        });
                    }
                },
                fillChangeLogWithDepartments(array){
                    $.each(array,function (index, item) {
                       VARIABLES.jQElements.changeLog.append('ID '+item.id+': '+item.departments.name+' '+item.department_type.name+'\n');
                    });
                },
                fillChangeLogWithMonths(array){
                    VARIABLES.jQElements.changeLog.append('Months: '+array+'\n');
                },
                fillChangeLogWithResponse(response){
                    VARIABLES.jQElements.changeLog.append(response+'\n');
                },
                scrollTextarea(textArea){
                    textArea.scrollTop(textArea[0].scrollHeight);
                }
            };
            FUNCTIONS.EVENT_HANDLERS.callEvents();
            FUNCTIONS.fillChangeLogWithMonths(VARIABLES.months);
            FUNCTIONS.scrollTextarea(VARIABLES.jQElements.changeLog);
            resizeDatatablesOnMenuToggle();
        });
    </script>
@endsection
