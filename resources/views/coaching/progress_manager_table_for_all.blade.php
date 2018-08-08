@extends('layouts.main')
@section('content')


    <div class="page-header">
        <div class="well gray-nav">Tabela postępów Zbiorczy</div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Legenda
        </div>
        <div class="panel-body">
            <div class="alert alert-success">
                <h4>
                    <p><strong>Wynik wyjściowy</strong> - wynik danego typu (średniej,jakości,RBH) przed rozpoczęciem coachingu. </p>
                    <p><strong>Aktualny Wynik</strong> - aktualny wynik danego typu coachingu(przyrostowy), liczony od daty rozpoczęcia coachingu.</p>
                    <p><strong>Aktualna RBH</strong> - ilość aktualnych zaakceptowanych godzin (przyrostowa), liczone od daty rozpoczęcia coachingu.</p>
                    <p><strong>Cel</strong> -  Wymagany wynik na coachingu.</p>
                    <p>Coaching zmieni status z <strong>"W toku"</strong> na <strong>"Nierozliczone"</strong> po <strong>18 RBH konsultanta</strong>,
                        licząc od daty rozpoczęcia coachingu.</p>
                    <p>Gdy pracownik przepracuje więcej niż <strong>26 RBH</strong> podświetli się na czerwono w tabeli "Nierozliczone"</p>
                </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <input id="all-coaches" type="checkbox" style="display: inline-block;">
            <label for="all-coaches" style="display: inline-block;">Pokaż również niepracujacych Coachów</label>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Oddział:</label>
                @if(Auth::user()->user_type_id === 4)
                        <select class="form-control" id="selected_dep" name="selected_dep" disabled>
                            @foreach($departments as $dep)
                                @if($dep_info_id === $dep->id)
                                    <option value="{{$dep->id}}" @if(($wiev_type == 'department') && $dep->id == $dep_info_id) selected @endif>{{$dep->departments->name . ' ' . $dep->department_type->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <select class="form-control" id="selected_dep" name="selected_dep">
                            <optgroup label="Oddziały">
                                @foreach($departments as $dep)
                                    <option value="{{$dep->id}}" @if(($wiev_type == 'department') && $dep->id == $dep_info_id) selected @endif>{{$dep->departments->name . ' ' . $dep->department_type->name}}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Dyrektor Regionalny HR">
                                @foreach($directorsHR as $director)
                                    <option
                                            @if($wiev_type == 'director' && ('10' . $director->id == $dep_info_id)) selected @endif
                                    value="10{{ $director->id }}">{{ $director->last_name . ' ' . $director->first_name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Kierownik Regionalny">
                                @foreach($regionalManagers as $director)
                                    <option
                                            @if($wiev_type == 'director' && ('10' . $director->id == $dep_info_id)) selected @endif
                                    value="10{{ $director->id }}">{{ $director->last_name . ' ' . $director->first_name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Dyrektorzy">
                                @foreach($directors as $director)
                                    <option
                                            @if($wiev_type == 'director' && ('10' . $director->id == $dep_info_id)) selected @endif
                                    value="10{{ $director->id }}">{{ $director->last_name . ' ' . $director->first_name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Trener:</label>
                <select class="form-control" id="coach_dep" name="coach_dep">
                    <option>Wszyscy</option>
                    @foreach($coach as  $item)
                        @if($item->status_work == 1)
                        <option value={{$item->id}}>{{$item->first_name.' '.$item->last_name}}</option>
                        @endif
                     @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="myLabel">Typ coaching'u:</label>
                <select class="form-control" id="type_coaching_in_progress">
                    <option value="0">Wszystkie</option>
                    <option value="1">Średnia</option>
                    <option value="2">Jakość</option>
                    <option value="3">RBH</option>
                </select>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="myLabel">Zakres od:</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                    <input class="form-control" id="date_start_in_progress" name="date_start_in_progress" type="text" value="{{date('Y-m-01')}}" >
                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="myLabel">Zakres do:</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                    <input class="form-control" id="date_stop_in_progress" name="date_stop_in_progress" type="text" value="{{date('Y-m-d')}}" >
                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
            </div>
        </div>
    </div>

    {{--Tabela z coaching w toku--}}
        <div class="panel panel-default">
            <div class="panel-heading">
                W toku
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                                <table id="table_in_progress" class="table table-striped thead-inverse">
                                    <thead>
                                    <tr>
                                        <th>Coach</th>
                                        <th>Osoba oceniana</th>
                                        <th>Data</th>
                                        <th>Temat</th>
                                        <th>Typ coachingu</th>
                                        <th>Wynik wyjściowy</th>
                                        <th>Wynik Aktualny</th>
                                        <th>Cel</th>
                                        <th>Aktualne RBH</th>
                                        <th>Zakończ</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    {{--Tabela z coaching w Nierozliczone--}}
        <div class="panel panel-default">
            <div class="panel-heading">
                Nierozliczone
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table_unsettled" class="table table-striped thead-inverse">
                                <thead>
                                <tr>
                                    <th>Coach</th>
                                    <th>Osoba oceniana</th>
                                    <th>Data</th>
                                    <th>Temat</th>
                                    <th>Typ coachingu</th>
                                    <th>Wynik wyjściowy</th>
                                    <th>Wynik Aktualny</th>
                                    <th>Cel</th>
                                    <th>Aktualne RBH</th>
                                    <th>Komentarz</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    {{--Tabela z coaching w Rozliczone--}}
        <div class="panel panel-default">
            <div class="panel-heading">
                Rozliczone
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table_settled" class="table table-striped thead-inverse">
                                <thead>
                                <tr>
                                    <th>Coach</th>
                                    <th>Osoba oceniana</th>
                                    <th>Data</th>
                                    <th>Temat</th>
                                    <th>Typ coachingu</th>
                                    <th>Wynik wyjściowy</th>
                                    <th>Osiągnięty wynik</th>
                                    <th>Cel</th>
                                    <th>Końcowe RBH</th>
                                    <th>Komentarz</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
    <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script>

        $(document).ready(function(){

            function isNumeric(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            }

            $('#selected_dep').on('change',function () {
                $.ajax({
                    type: "POST",
                    url: '{{ route('api.getcoach_list') }}',
                    data: {
                        "department_info_id": $(this).val(),
                        "coaching_level"    : 1
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#coach_dep option').remove();
                        let option_select = '<option>Wszyscy</option>';
                        for(var i=0;i<response.length;i++){
                            option_select += '<option value='+
                                response[i].id+'>'+response[i].first_name+' '+response[i].last_name+'</option>';
                        }
                        $('#coach_dep').append(option_select);
                    }
                });
            });
            var in_progress_table = $('#table_in_progress').DataTable({
                "bPaginate": false,
                "bInfo" : false,
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                },"ajax": {
                    'url': "{{ route('api.datatableCoachingTableDirector') }}",
                    'type': 'POST',
                    'data': function (d) {

                        d.type              = $('#type_coaching_in_progress').val();
                        d.coaching_level    = 1;
                        d.report_status     = 0;
                        d.date_start        = $('#date_start_in_progress').val();
                        d.date_stop         = $('#date_stop_in_progress').val();
                        d.department_info   = $('#selected_dep').val();
                        d.coach_id          = $('#coach_dep').val();
                        d.type_table              = 'manager';
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },"rowCallback": function( row, data, index ) {
                    if (parseInt(data.actual_rbh) >= parseInt(18)) {
                        $(row).hide();
                    }
                    $(row).attr('id', data.id);
                    return row;
                },"fnDrawCallback": function (settings) {
                    $('.btn-end_coaching').on('click',function () {
                        let caochingIDToEnd = $(this).data('id');
                       swal({
                           title: 'Zakończenie coachingu osoby niepracującej (podaj aktualny wynik)',
                           input: 'number',
                           inputAttributes: {
                               autocapitalize: 'off'
                           },
                           showCancelButton: true,
                           confirmButtonText: 'Zapisz i zakończ',
                           showLoaderOnConfirm: true,
                           preConfirm: (actualScore) => {
                               if(isNumeric(actualScore)){
                                   $.ajax({
                                       type: "POST",
                                       url: "{{ route('api.endCoachingDirector') }}",
                                       headers: {
                                           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                       },
                                       data:{
                                           'coachingID'     : caochingIDToEnd,
                                           'actualScore'    : actualScore
                                       },
                                       success: function (response) {
                                           in_progress_table.ajax.reload();
                                           $.notify({
                                               icon: 'glyphicon glyphicon-ok',
                                               message: '<strong>Coaching został zakończoy</strong>'
                                           }, {
                                               type: "success"
                                           });
                                       }
                                   })
                               }else{
                                   $.notify({
                                       icon: 'glyphicon glyphicon-ok',
                                       message: '<strong>Błędna liczba</strong>'
                                   }, {
                                       type: "danger"
                                   });
                               }
                           },
                       })
                    });
                },
                "columns":[
                    {data:function (data, type, dataToSet) {
                            return data.manager_first_name+' '+data.manager_last_name;
                        },"name": "manager_last_name"
                    },
                    {"data":function (data, type, dataToSet) {
                            return data.user_first_name + " " + data.user_last_name;
                        },"name": "user.last_name"
                    },
                    {"data":"coaching_date"},
                    {"data": "subject"},
                    // // typ coachingu
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return 'Średnia';
                            }else if(data.coaching_type == 2){
                                return 'Jakość';
                            }else
                                return 'RBH';
                        },"name": "coaching_type"
                    },
                    // wynik wyjściowy
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_start;
                            }else if(data.coaching_type == 2){
                                return data.janky_start;
                            }else
                                return data.rbh_start;
                        },"name": "average_start","searchable": false
                    },
                    // wynik aktualny
                    {"data":function (data, type, dataToSet) {
                            var span_good_start =  '<span style="color: green">';
                            var span_bad_start =  '<span style="color: red">';
                            var span_end= '</span>';
                            if(data.coaching_type == 1){
                                if(data.actual_avg == 'null')
                                    data.actual_avg = 0;
                                if(parseFloat(data.actual_avg) > parseFloat(data.average_goal))
                                    return span_good_start+data.actual_avg+span_end;
                                else
                                    return span_bad_start+data.actual_avg+span_end;
                            }else if(data.coaching_type == 2){
                                if(data.actual_janky == null)
                                    data.actual_janky = 0;
                                if(parseFloat(data.actual_janky) < parseFloat(data.janky_goal))
                                    return span_good_start+data.actual_janky+span_end;
                                else
                                    return span_bad_start+data.actual_janky+span_end;
                            }else{
                                if(data.actual_rbh == null)
                                    data.actual_rbh = 0;
                                if(parseFloat(data.actual_rbh) > parseFloat(data.rbh_goal))
                                    return span_good_start+data.actual_rbh+span_end;
                                else
                                    return span_bad_start+data.actual_rbh+span_end;
                            }

                        },"name": "average_start","searchable": false
                    },
                    // // wynik cel
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_goal;
                            }else if(data.coaching_type == 2){
                                return data.janky_goal;
                            }else
                                return data.rbh_goal;
                        },"name": "average_start","searchable": false
                    },
                    // //ile rbh minęło po coachingu
                    {"data":function (data, type, dataToSet) {
                            return data.actual_rbh;
                            //return Math.round(data.couching_rbh/3600,2);
                        },"name": "actual_rbh","searchable": false
                    },
                    {"data": function (data, type,dataToSet) {
                            if(data.statusWork == 0 && ($('#selected_dep').val() == 1 || $('#selected_dep').val() == 4) ){
                                return "<button class='btn-end_coaching btn btn-success btn-block' data-id=" + data.id + " >Zakończ</button>";
                            }else
                                return "Brak opcji";
                        },"searchable": false,"orderable":false
                    }
                ],
            });

            $('#date_start_in_progress, #date_stop_in_progress,#selected_dep,#coach_dep,#type_coaching_in_progress').on('change',function (e) {
                in_progress_table.ajax.reload();
                table_unsettled.ajax.reload();
                table_settled.ajax.reload();
            });

            var table_unsettled = $('#table_unsettled').DataTable({
                "bPaginate": false,
                "bInfo" : false,
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                },"ajax": {
                    'url': "{{ route('api.datatableCoachingTableDirector') }}",
                    'type': 'POST',
                    'data': function (d) {
                        d.type              = $('#type_coaching_in_progress').val();
                        d.coaching_level    = 1;
                        d.report_status     = 0;
                        d.date_start        = $('#date_start_in_progress').val();
                        d.date_stop         = $('#date_stop_in_progress').val();
                        d.department_info   = $('#selected_dep').val();
                        d.coach_id          = $('#coach_dep').val();
                        d.type_table        = 'manager';
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },"rowCallback": function( row, data, index ) {
                    if (parseInt(data.actual_rbh) < parseInt(18)) {
                        $(row).hide();
                    }
                    if(parseInt(data.actual_rbh) > 26){
                        $(row).css("background-color","#c500002e");
                    }
                    $(row).attr('id', data.id);
                    return row;
                },"columns":[
                    {data:function (data, type, dataToSet) {
                            return data.manager_first_name+' '+data.manager_last_name;
                        },"name": "manager_last_name"
                    },
                    {"data":function (data, type, dataToSet) {
                            return data.user_first_name + " " + data.user_last_name;
                        },"name": "user_last_name"
                    },
                    {"data":"coaching_date"},
                    {"data": "subject"},
                    // // typ coachingu
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return 'Średnia';
                            }else if(data.coaching_type == 2){
                                return 'Jakość';
                            }else
                                return 'RBH';
                        },"name": "coaching_type"
                    },
                    // wynik wyjściowy
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_start;
                            }else if(data.coaching_type == 2){
                                return data.janky_start;
                            }else
                                return data.rbh_start;
                        },"name": "average_start","searchable": false
                    },
                    // wynik aktualny
                    {"data":function (data, type, dataToSet) {
                            var span_good_start =  '<span style="color: green">';
                            var span_bad_start =  '<span style="color: red">';
                            var span_end= '</span>';

                                if(data.coaching_type == 1){
                                    if(parseFloat(data.actual_avg) > parseFloat(data.average_goal))
                                        return span_good_start+data.actual_avg+span_end;
                                    else
                                        return span_bad_start+data.actual_avg+span_end;
                                }else if(data.coaching_type == 2){
                                    if(parseFloat(data.actual_janky) < parseFloat(data.janky_goal))
                                        return span_good_start+data.actual_janky+span_end;
                                    else
                                        return span_bad_start+data.actual_janky+span_end;
                                }else
                                if(parseFloat(data.actual_rbh) > parseFloat(data.rbh_goal))
                                    return span_good_start+data.actual_rbh+span_end;
                                else
                                    return span_bad_start+data.actual_rbh+span_end;
                        },"name": "average_start","searchable": false
                    },
                    // // wynik cel
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_goal;
                            }else if(data.coaching_type == 2){
                                return data.janky_goal;
                            }else
                                return data.rbh_goal;
                        },"name": "average_start","searchable": false
                    },
                    // //ile rbh minęło po coachingu
                    {"data":function (data, type, dataToSet) {
                            return data.actual_rbh;
                            //return Math.round(data.couching_rbh/3600,2);
                        },"name": "actual_rbh","searchable": false
                    },
                    {"data":function (data, type, dataToSet) {
                            let comment = 'Brak';
                            if(data.comment != null){
                                comment = data.comment;
                            }
                            return comment;
                        },"name": "comment"
                    },
                ],

            });

            var table_settled = $('#table_settled').DataTable({
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                },"ajax": {
                    'url': "{{ route('api.datatableCoachingTableDirector') }}",
                    'type': 'POST',
                    'data': function (d) {
                        d.type              = $('#type_coaching_in_progress').val();
                        d.coaching_level    = 1;
                        d.report_status     = 1;
                        d.date_start        = $('#date_start_in_progress').val();
                        d.date_stop         = $('#date_stop_in_progress').val();
                        d.department_info   = $('#selected_dep').val();
                        d.coach_id          = $('#coach_dep').val();
                        d.type_table        = 'manager';
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },"columns":[
                    {data:function (data, type, dataToSet) {
                            return data.manager_first_name+' '+data.manager_last_name;
                        },"name": "manager_last_name"
                    },
                    {"data":function (data, type, dataToSet) {
                            return data.user_first_name + " " + data.user_last_name;
                        },"name": "user_last_name"
                    },
                    {"data":"coaching_date"},
                    {"data": "subject"},
                    // // typ coachingu
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return 'Średnia';
                            }else if(data.coaching_type == 2){
                                return 'Jakość';
                            }else
                                return 'RBH';
                        },"name": "coaching_type"
                    },
                    // wynik wyjściowy
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_start;
                            }else if(data.coaching_type == 2){
                                return data.janky_start;
                            }else
                                return data.rbh_start;
                        },"name": "average_start","searchable": false
                    },
                    // wynik aktualny
                    {"data":function (data, type, dataToSet) {
                            var span_good_start =  '<span style="color: green">';
                            var span_bad_start =  '<span style="color: red">';
                            var span_end= '</span>';
                            // if(user_department_type == 2) {
                            if(data.coaching_type == 1){
                                if(parseFloat(data.average_end) > parseFloat(data.average_goal))
                                    return span_good_start+data.average_end+span_end;
                                else
                                    return span_bad_start+data.average_end+span_end;
                            }else if(data.coaching_type == 2){
                                if(parseFloat(data.janky_end) < parseFloat(data.janky_goal))
                                    return span_good_start+data.janky_end+span_end;
                                else
                                    return span_bad_start+data.janky_end+span_end;
                            }else
                            if(parseFloat(data.rbh_end) > parseFloat(data.rbh_goal))
                                return span_good_start+data.rbh_end+span_end;
                            else
                                return span_bad_start+data.rbh_end+span_end;
                        },"name": "average_start","searchable": false
                    },
                    // // wynik cel
                    {"data":function (data, type, dataToSet) {
                            if(data.coaching_type == 1){
                                return data.average_goal;
                            }else if(data.coaching_type == 2){
                                return data.janky_goal;
                            }else
                                return data.rbh_goal;
                        },"name": "average_start","searchable": false
                    },
                    // //ile rbh minęło po coachingu
                    {"data":function (data, type, dataToSet) {
                            return data.rbh_end;
                            //return Math.round(data.couching_rbh/3600,2);
                        },"name": "actual_rbh","searchable": false
                    },
                    {"data":"comment"},
                ]
            });


            $('.form_date').datetimepicker({
                language:  'pl',
                autoclose: 1,
                minView : 2,
                pickTime: false,
            });

            const allCoachesCheckbox = document.querySelector('.all-coaches');

            /**
             * This method append basic option to select
             * @param placeToAppend
             */
            function appendBasicOption(placeToAppend) {
                console.assert(placeToAppend.matches('#coach_dep'), 'placeToAppend in method appendBasicOption is not coaches select');
                let basicOption = document.createElement('option');
                basicOption.textContent = "Wszyscy";
                placeToAppend.appendChild(basicOption);
            }

            function globalChangeHandler(e) {
                if(e.target.matches('#all-coaches')) {
                    const thisSelect = e.target;
                    let isChecked = thisSelect.checked;
                    let coachSelect = document.querySelector('#coach_dep');

                    @if(isset($coach))
                        let allCoaches = @json($coach);
                    @else
                        let allCoaches = [];
                    @endif

                    coachSelect.options.length = 0;

                    if(isChecked) { //true or false
                        appendBasicOption(coachSelect)

                        allCoaches.forEach(item => {
                            let optionElement = document.createElement('option');
                            optionElement.value = item.id;
                            if(item.status_work == 0) {
                                optionElement.textContent = `${item.first_name} ${item.last_name} (Niepracujący[a])`;
                            }
                            else {
                                optionElement.textContent = `${item.first_name} ${item.last_name}`;
                            }
                            coachSelect.appendChild(optionElement);
                        });
                    }
                    if(!isChecked) {
                        appendBasicOption(coachSelect)

                        allCoaches.forEach(item => {
                            if(item.status_work == 1) {
                                let optionElement = document.createElement('option');
                                optionElement.value = item.id;
                                optionElement.textContent = `${item.first_name} ${item.last_name}`;
                                coachSelect.appendChild(optionElement);
                            }
                        });
                    }
                }

            }

            document.addEventListener('change', globalChangeHandler);

        })
    </script>
@endsection
