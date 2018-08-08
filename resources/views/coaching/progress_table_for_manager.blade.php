@extends('layouts.main')
@section('content')

    <div class="page-header">
        <div class="well gray-nav">Tabela postępów Kierownik</div>
    </div>

    <button data-toggle="modal" class="btn btn-default training_to_modal" id="new_coaching_modal" data-target="#Modal_Coaching" data-id="1" title="Nowy Coaching" style="margin-bottom: 14px">
        <span class="glyphicon glyphicon-plus"></span> <span>Nowy Coaching</span>
    </button>

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
                    <p>Coaching zmieni status z <strong>"W toku"</strong> na <strong>"Nierozliczone"</strong> po <strong>4 dniach</strong>,
                        od rozpoczęcia coachingu.</p>
                    <p>Coaching zmieni kolor na czerwony po <strong>5 dniach</strong> od daty coachingu</p>
                </h4>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="myLabel">Zakres od:</label>
                        <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                            <input class="form-control" id="date_start_in_progress" name="date_start_in_progress" type="text" value="{{date('Y-m-01')}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="myLabel">Zakres do:</label>
                        <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                            <input class="form-control" id="date_stop_in_progress" name="date_stop_in_progress" type="text" value="{{date('Y-m-d')}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="table_in_progress" class="table table-striped thead-inverse">
                            <thead>
                            <tr>
                                <th>Kierownik</th>
                                <th>Trener</th>
                                <th>Data</th>
                                <th>Temat</th>
                                <th>Typ coachingu</th>
                                <th>Wynik wyjściowy</th>
                                <th>Wynik Aktualny</th>
                                <th>Cel</th>
                                <th>Aktualne RBH</th>
                                <th>Akcja</th>
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
                    <div class="form-group">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Typ coaching'u:</label>
                                <select class="form-control" id="type_coaching_unsettled">
                                    <option value="0">Wszystkie</option>
                                    <option value="1">Średnia</option>
                                    <option value="2">Jakość</option>
                                    <option value="3">RBH</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Zakres od:</label>
                                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" id="date_start_unsettled" name="date_start_unsettled" type="text" value="{{date('Y-m-01')}}" >
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Zakres do:</label>
                                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" id="date_stop_unsettled" name="date_stop_unsettled" type="text" value="{{date('Y-m-d')}}" >
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table_unsettled" class="table table-striped thead-inverse">
                                    <thead>
                                    <tr>
                                        <th>Kierownik</th>
                                        <th>Trener</th>
                                        <th>Data</th>
                                        <th>Temat</th>
                                        <th>Typ coachingu</th>
                                        <th>Wynik wyjściowy</th>
                                        <th>Wynik Aktualny</th>
                                        <th>Cel</th>
                                        <th>Aktualne RBH</th>
                                        <th>Komentarz</th>
                                        <th>Akcja</th>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Typ coaching'u:</label>
                                <select class="form-control" id="type_coaching_settled">
                                    <option value="0">Wszystkie</option>
                                    <option value="1">Średnia</option>
                                    <option value="2">Jakość</option>
                                    <option value="3">RBH</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Zakres od:</label>
                                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" id="date_start_settled" name="date_start_settled" type="text" value="{{date('Y-m-01')}}" >
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="myLabel">Zakres do:</label>
                                <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                    <input class="form-control" id="date_stop_settled" name="date_stop_settled" type="text" value="{{date('Y-m-d')}}" >
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table_settled" class="table table-striped thead-inverse">
                                    <thead>
                                    <tr>
                                        <th>Kierownik</th>
                                        <th>Trener</th>
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



        <div id="Modal_Coaching" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" style="width: 90%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="modal_title">Ustal Coaching<span id="modal_category"></span></h4>
                    </div>
                    <div class="modal-body">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Legenda
                            </div>
                            <div class="panel-body">
                                <div class="alert alert-success">
                                    <h4>
                                        <p>
                                            Aktualne wyniki wyliczne są liczone po zaakceptowanej ilości RBH >= 18, dla każdego konsultanta z grupy trenera.
                                        </p>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="header_modal">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="myLabel">Trener</label>
                                            <select class="form-control" id="couaching_manager_id">
                                                <option>Wybierz</option>
                                                @foreach($coachingManagerList as $list)
                                                    <option value={{$list->manager_id}}>{{$list->manager_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Temat</label>
                                            <input type="text" class="form-control" id="coaching_subject" placeholder="Podaj temat"/>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Typ Coachingu:</label>
                                            <select class="form-control" id="couaching_manager_type">
                                                <option>Wybierz</option>
                                                <option value="1">Średnia</option>
                                                <option value="2">Jakość</option>
                                                <option value="3">RBH</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Data Coaching'u:</label>
                                            <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                                                <input class="form-control" id="date_start_new_coaching" name="date_start_new_coaching" value='{{date('Y-m-d')}}' type="text"  >
                                                <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-th"></span>
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Aktualna średnia</label>
                                            <input type="number" lang="en" class="form-control" name="manager_actual_avg" id="manager_actual_avg" placeholder="Wprawoadź aktualną średnią" @php $user_department_type == 1 ? print "" : print "disabled=true" @endphp />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Aktualna Jakość</label>
                                            <input type="number" lang="en" class="form-control" name="manager_actual_janky" id="manager_actual_janky" placeholder="Wprawoadź aktualną jakość" @php $user_department_type == 1 ? print "" : print "disabled=true" @endphp />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Aktualne RBH</label>
                                            <input type="number" lang="en" class="form-control" name="manager_actual_rbh" id="manager_actual_rbh" placeholder="Wprawoadź aktualne RBH" @php $user_department_type == 1 ? print "" : print "disabled=true" @endphp />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Średnia docelowa</label>
                                            <input type="number" class="form-control goal_manager" id="coaching_manager_avg_goal" placeholder="Wprawoadź docelową średnią" disabled="true"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">Jakość docelowa</label>
                                            <input type="number" class="form-control goal_manager" id="coaching_manager_avg_janky" placeholder="Wprawoadź docelowy próg jakości" disabled="true"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="myLabel">RBH docelowa</label>
                                            <input type="number" class="form-control goal_manager" id="coaching_manager_avg_rbh" placeholder="Wprawoadź cel rbh" disabled="true"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-success form-control" id="save_coaching_modal" onclick = "save_coaching(this)" >Dodaj Coaching</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" value="0" id="status_coauching" />

        @endsection

        @section('script')
            <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
            <script>

                //Sprawdzenie czy osoba wybrana jest z hr
                function isHr(manager_id) {
                    let coachingManagerList ="{{$coachingManagerList}}";
                    isHR = false;
                    for(var i = 0; i < coachingManagerList.length; i++){
                        if(coachingManagerList[i].manager_id == manager_id){
                            if(user_type == 5){
                                isHR = true;
                            }
                        }
                    }
                    return isHR;
                }

                /**
                 * Zapisywanie nowego coaching'u
                 * @param e
                 */
                function save_coaching(e) {
                    let manager_id = $('#couaching_manager_id').val();
                    let subject = $('#coaching_subject').val();
                    let coaching_date = $('#date_start_new_coaching').val();
                    let coaching_comment = $("#comment_coaching").val();

                    let coaching_type = $('#couaching_manager_type').val();
                    let manager_actual_avg = $("#manager_actual_avg").val();
                    let manager_actual_janky = $("#manager_actual_janky").val();
                    let manager_actual_rbh = $("#manager_actual_rbh").val();

                    let pom_manager_actual_avg =  parseFloat(manager_actual_avg);
                    let pom_manager_actual_janky =  parseFloat(manager_actual_janky);
                    let pom_manager_actual_rbh =  parseFloat(manager_actual_rbh);


                    let coaching_manager_goal_avg = $("#coaching_manager_avg_goal").val();
                    let coaching_manager_goal_janky = $("#coaching_manager_avg_janky").val();
                    let coaching_manager_goal_rbh = $("#coaching_manager_avg_rbh").val();

                    // Przedział 10 - 30 %
                    let proc_manager_goal_min_avg = Math.round((((pom_manager_actual_avg*10)/100) + pom_manager_actual_avg)*100)/100;
                    let proc_manager_goal_max_avg = Math.round((((pom_manager_actual_avg*30)/100) + pom_manager_actual_avg)*100)/100;

                    let proc_manager_goal_min_rbh = Math.round((((pom_manager_actual_rbh*10)/100) + pom_manager_actual_rbh)*100)/100;
                    let proc_manager_goal_max_rbh = Math.round((((pom_manager_actual_rbh*30)/100) + pom_manager_actual_rbh)*100)/100;

                    let proc_manager_goal_min_jank = Math.round((pom_manager_actual_janky - ((pom_manager_actual_janky*10)/100))*100)/100;
                    let proc_manager_goal_max_jank = Math.round((pom_manager_actual_janky - ((pom_manager_actual_janky*30)/100) )*100)/100;


                    let validation = true;
                    if(manager_id == 'Wybierz'){
                        validation = false;
                        swal('Wybierz kierownika')
                    }else if(coaching_type == 'Wybierz'){
                        validation = false;
                        swal('Wybierz Typ coachingu')
                    }else if(subject.trim('').length == 0){
                        validation = false;
                        swal('Podaj temat')
                    }else if(new Date(coaching_date).getTime() < new Date("{!! date('Y-m-d') !!}").getTime() && $('#status_coauching').val() == 0){
                        validation = false;
                        swal('Błędna data')
                    }

                    if(validation)
                    {
                        let user_department_typ ="{{$user_department_type}}";
                        let isHR = isHr(manager_id);


                        if(coaching_type == 1){
                            if(manager_actual_avg.trim('').length == 0 || isNaN(manager_actual_avg) ){
                                validation = false;
                                swal('Błędna aktualna średnia')
                            }
                            if(coaching_manager_goal_avg.trim('').length == 0 || isNaN(coaching_manager_goal_avg) || parseFloat(manager_actual_avg) > parseFloat(coaching_manager_goal_avg) ){
                                validation = false;
                                swal('Błędna docelowa średnia')
                            }
                            if(user_department_typ != 1 && isHR == false) {
                                if (manager_actual_avg > 0.5) {
                                    if (coaching_manager_goal_avg < proc_manager_goal_min_avg) {
                                        validation = false;
                                        swal('Minimalna średnia musi być większa niż 10% aktualnej średniej')
                                    } else if (coaching_manager_goal_avg > proc_manager_goal_max_avg) {
                                        validation = false;
                                        swal('Minimalna średnia musi być mniejsza niż 30% aktualnej średniej')
                                    }
                                }
                            }else{
                                console.log('nie sprawdzam');
                            }
                        }else if(coaching_type == 2){
                            if(manager_actual_janky.trim('').length == 0 || isNaN(manager_actual_janky) ){
                                validation = false;
                                swal('Błędna aktualna jakość')
                            }
                            if(coaching_manager_goal_janky.trim('').length == 0 || isNaN(coaching_manager_goal_janky) || parseFloat(manager_actual_janky) < parseFloat(coaching_manager_goal_janky) ){
                                validation = false;
                                swal('Błędna docelowa jakość')
                            }
                            if(manager_actual_janky > 0.5){
                                if(coaching_manager_goal_janky > proc_manager_goal_min_jank){
                                    validation = false;
                                    swal('Minimalna jakość musi być mniejsza niż 10% aktualnej jakości')
                                }
                                // else if(coaching_manager_goal_janky < proc_manager_goal_max_jank){
                                //     validation = false;
                                //     swal('Minimalna jakoś nie może być mniejsza niż 30% aktualnej jakości')
                                // }
                            }
                        }else if(coaching_type == 3){
                            if(manager_actual_rbh.trim('').length == 0 || isNaN(manager_actual_rbh) ){
                                validation = false;
                                swal('Błędne aktualne RBH')
                            }
                            if(coaching_manager_goal_rbh.trim('').length == 0 || isNaN(coaching_manager_goal_rbh) || parseFloat(manager_actual_rbh) > parseFloat(coaching_manager_goal_rbh) ){
                                validation = false;
                                swal('Błędne docelowe RBH')
                            }
                            if(manager_actual_rbh > 0.5){
                                if(coaching_manager_goal_rbh < proc_manager_goal_min_rbh){
                                    validation = false;
                                    swal('Minimalne RBH musi być większe niż 10% aktualnego RBH')
                                }else if(coaching_manager_goal_rbh > proc_manager_goal_max_rbh){
                                    validation = false;
                                    swal('Minimalne RBH musi być mniejsza niż 30% aktualnego RBH')
                                }
                            }
                        }else{
                            validation = false;
                            swal('Błędne dane')
                        }
                    }

                    if(validation){
                        $.ajax({
                            type: "POST",
                            url: "{{route('api.saveCoachingDirector')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'manager_id'                    : manager_id,
                                'subject'                       : subject,
                                'coaching_date'                 : coaching_date,
                                'coaching_comment'              : coaching_comment,
                                'coaching_level'                : 2,
                                'coaching_type'                 : coaching_type,
                                'manager_actual_avg'            : manager_actual_avg,
                                'manager_actual_janky'          : manager_actual_janky,
                                'manager_actual_rbh'            : manager_actual_rbh,

                                'coaching_manager_avg_goal'     :coaching_manager_goal_avg,
                                'coaching_manager_avg_janky'    :coaching_manager_goal_janky,
                                'coaching_manager_avg_rbh'      :coaching_manager_goal_rbh,

                                'status'                : $('#status_coauching').val(),
                            },
                            success: function (response) {
                                $('#Modal_Coaching').modal('hide');
                            }
                        })
                    }
                }

                function isNumeric(n) {
                    return !isNaN(parseFloat(n)) && isFinite(n);
                }

                $(document).ready(function(){
                    $('#new_coaching_modal').on('click',function () {
                        clear_moda();
                        $('#save_coaching_modal').text('Dodaj Coaching');
                    });
                    $('#Modal_Coaching').on('hidden.bs.modal',function () {
                        in_progress_table.ajax.reload();
                        $('#status_coauching').val("0");
                        clear_moda();
                    });

                    var manager = JSON.parse('{!!$coachingManagerList!!}');
                    $('#couaching_manager_id').on('change',function () {
                        for(var i =0;i<manager.length;i++){
                            if(manager[i].manager_id == $(this).val()){
                                $('input[name="manager_actual_avg"]').val((Math.round(manager[i].manager_actual_avg*100))/100);
                                $('input[name="manager_actual_janky"]').val((Math.round(manager[i].manager_actual_janky*100))/100);
                                $('input[name="manager_actual_rbh"]').val((Math.round(manager[i].manager_actual_rbh*100))/100);
                                break;
                            }else{
                                $('#coaching_actual_avg').val('');
                            }
                        }
                    });
                    $('#couaching_manager_type').on('change',function () {

                        let select_value = $(this).val();
                        var input = document.getElementsByClassName('goal_manager');
                        for(var i = 0;i<input.length;i++){
                            input[i].value = "";
                            if(select_value != 'Wybierz'){
                                if(select_value == i+1){
                                    input[i].disabled = false;
                                } else{
                                    input[i].disabled = true;
                                }
                            }else{
                                input[i].disabled = true;
                            }
                        }
                    });

                    function clear_moda() {
                        $('#couaching_manager_id').val('Wybierz');
                        $('#coaching_subject').val('');
                        $('#couaching_manager_type').val('Wybierz');

                        $('#manager_actual_avg').val('');
                        $('#manager_actual_janky').val('');
                        $('#manager_actual_rbh').val('');

                        $('#coaching_manager_avg_goal').val('');
                        $('#coaching_manager_avg_janky').val('');
                        $('#coaching_manager_avg_rbh').val('');

                        $('#date_start_new_coaching').val("{!! date('Y-m-d') !!}");

                        var input = document.getElementsByClassName('goal_manager');
                        for(var i = 0;i<input.length;i++){
                            input[i].value = "";
                            input[i].disabled = true;
                        }
                    }

                    // coachingi w toku
                    var in_progress_table = $('#table_in_progress').DataTable({
                        "autoWidth": false,
                        "processing": true,
                        "serverSide": true,
                        "bPaginate": false,
                        "bInfo" : false,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                        },"ajax": {
                            'url': "{{ route('api.datatableCoachingTableDirector') }}",
                            'type': 'POST',
                            'data': function (d) {
                                d.report_status = 0;
                                d.type          = $('#type_coaching_in_progress').val();
                                d.date_start    = $('#date_start_in_progress').val();
                                d.date_stop     = $('#date_stop_in_progress').val();
                                d.coaching_level = 2;
                            },
                            'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        },
                        "rowCallback": function( row, data, index ) {
                            var coaching_end_date = Date.parse(data.coaching_date);
                            coaching_end_date +=345600*1000; // stworzenie daty + dodanie 4 dni
                            var actual_date = new Date();
                            if (actual_date > coaching_end_date ) {
                                $(row).hide();
                            }
                            $(row).attr('id', data.id);
                            return row;
                        },
                        "fnDrawCallback": function(settings){
                            /**
                             * Usunięcie coachingu
                             */
                            $('.button-delete-coaching').on('click',function () {
                                coaching_id = $(this).data('id');
                                swal({
                                    title: 'Jesteś pewien?',
                                    text: "Nie będziesz w stanie cofnąć zmian!",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Tak, usuń coaching!'
                                }).then((result) => {
                                    if (result.value) {

                                    $.ajax({
                                        type: "POST",
                                        url: "{{ route('api.deleteCoachingTableDirector') }}", // do zamiany
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: {
                                            'coaching_id'         : coaching_id
                                        },
                                        success: function (response) {
                                            in_progress_table.ajax.reload();
                                        }
                                    });
                                }})
                            });

                            /**
                             * Educja coachingu
                             */
                            $('.button-edit-coaching').on('click',function () {
                                clear_moda();
                                coaching_id = $(this).data('id');
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('api.getCoachingDirector') }}", // do zamiany
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        'coaching_id'         : coaching_id
                                    },
                                    success: function (response) {

                                        $('#couaching_manager_id').val(response.user_id);
                                        $('#coaching_subject').val(response.subject);
                                        $('#couaching_manager_type').val(response.coaching_type);
                                        $('#manager_actual_avg').val(response.average_start);
                                        $('#manager_actual_janky').val(response.janky_start);
                                        $('#manager_actual_rbh').val(response.rbh_start);
                                        $('#coaching_manager_avg_goal').val(response.average_goal);
                                        $('#coaching_manager_avg_janky').val(response.janky_goal);
                                        $('#coaching_manager_avg_rbh').val(response.rbh_goal);
                                        $('#coaching_actual_avg').val(response.coaching_actual_avg);
                                        $('#coaching_goal').val(response.average_goal);
                                        $('#date_start_new_coaching').val(response.coaching_date);
                                        $('#save_coaching_modal').text('Edytuj Coaching');
                                        $('#status_coauching').val(response.id);
                                        $('#Modal_Coaching').modal('show');

                                        let select_value = response.coaching_type;
                                        var input = document.getElementsByClassName('goal_manager');
                                        for(var i = 0;i<input.length;i++){
                                            if(select_value != 'Wybierz'){
                                                if(select_value == i+1){
                                                    input[i].disabled = false;
                                                } else{
                                                    input[i].disabled = true;
                                                }
                                            }else{
                                                input[i].disabled = true;
                                            }
                                        }
                                        in_progress_table.ajax.reload();
                                    }
                                });
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
                                        if(parseFloat(data.actual_avg) >= parseFloat(data.average_goal))
                                            return span_good_start+data.actual_avg+span_end;
                                        else
                                            return span_bad_start+data.actual_avg+span_end;
                                    }else if(data.coaching_type == 2){
                                        if(parseFloat(data.actual_janky) < parseFloat(data.janky_goal))
                                            return span_good_start+data.actual_janky+span_end;
                                        else
                                            return span_bad_start+data.actual_janky+span_end;
                                    }else
                                    if(parseFloat(data.actual_rbh) >= parseFloat(data.rbh_goal))
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
                                    return "<button class='button-edit-coaching btn btn-warning btn-block' data-id="+data.id+">Edycja</button>" +
                                        "<button class='button-delete-coaching btn btn-danger btn-block' data-id="+data.id+">Usuń</button>";
                                },"orderable": false, "searchable": false
                            },
                        ],
                    });


                    $('#date_start_in_progress, #date_stop_in_progress,#type_coaching_in_progress').on('change',function (e) {
                        in_progress_table.ajax.reload();
                    });

                    var table_unsettled = $('#table_unsettled').DataTable({
                        "autoWidth": false,
                        "processing": true,
                        "serverSide": true,
                        "bPaginate": false,
                        "bInfo" : false,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                        },"ajax": {
                            'url': "{{ route('api.datatableCoachingTableDirector') }}",
                            'type': 'POST',
                            'data': function (d) {
                                d.type          = $('#type_coaching_unsettled').val();
                                d.report_status = 0;
                                d.date_start = $('#date_start_unsettled').val();
                                d.date_stop =  $('#date_stop_unsettled').val();
                                d.coaching_level = 2;
                            },
                            'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        },"rowCallback": function( row, data, index ) {
                            var coaching_end_date = Date.parse(data.coaching_date);
                            coaching_end_date += 345600*1000; // stworzenie daty + dodanie 4 dni
                            var limit_date = coaching_end_date + (86400*1000);
                            var actual_date = new Date();
                            if (actual_date < coaching_end_date ) {
                                $(row).hide();
                            }
                            if(actual_date >= limit_date){
                                $(row).css("background-color","#c500002e");
                            }
                            $(row).attr('id', data.id);
                            return row;
                        }
                        ,"fnDrawCallback": function(settings){

                            $('.btn-accept_coaching').on('click',function () {
                                let coaching_id = $(this).data('id');

                                let coaching_comment = $('#text_'+coaching_id).val();
                                let row = $(this).closest('tr');
                                let coaching_type =  row.find('td:nth-child(5)').text();
                                let end_score =  row.find('td:nth-child(7)').text() == null || row.find('td:nth-child(7)').text() == "" ? row.find('td:nth-child(7)').children().val() : row.find('td:nth-child(7)').text();
                                let rbh_end = row.find('td:nth-child(9)').text();
                                let user_department_typ ="{{$user_department_type}}";
                                let is_a_number = true;
                                let manager_type_id = $.ajax({
                                    async: false,
                                    type: "POST",
                                    url: "{{ route('api.getManagerId') }}", // do zamiany
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        'coaching_id'         : coaching_id
                                    },
                                    success: function (response) {
                                        return response[0];
                                    }
                                });
                                //sprawdzenie czy osoba jest od hr
                                let manager_type_id_JSON = JSON.parse(manager_type_id.responseText);
                                manager_type_id_JSON = manager_type_id_JSON[0];
                                let isHR = false;
                                    if(manager_type_id_JSON == 5)
                                        isHR = true;

                                if(user_department_typ == 1 || isHR == true)
                                    if(isNumeric(row.find('td:nth-child(7)').children().val()) == false) {
                                        is_a_number = false;
                                    }
                                if(is_a_number == true) {
                                    swal({
                                        title: 'Jesteś pewien?',
                                        text: "Nie będziesz w stanie cofnąć zmian!",
                                        type: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Tak, akceptuj coaching!'
                                    }).then((result) => {
                                        if (result.value) {
                                            $.ajax({
                                                type: "POST",
                                                url: "{{ route('api.acceptCoachingDirector') }}", // do zamiany
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                data: {
                                                    'coaching_id': coaching_id,
                                                    'coaching__comment': coaching_comment,
                                                    'coaching_type': coaching_type,
                                                    'end_score': end_score,
                                                    'rbh_end': rbh_end,
                                                },
                                                success: function (response) {
                                                    table_unsettled.ajax.reload();
                                                    table_settled.ajax.reload();
                                                }
                                            });
                                        }
                                    })
                                }else{
                                    swal({
                                        type: 'error',
                                        title: 'Zła wartość',
                                        text: 'Wartość z pola wynik aktualny musi być liczbą'
                                    })
                                }
                            });
                        }
                        ,"columns":[
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
                                    let coaching_id = data.id;
                                    let manager_type_id = $.ajax({
                                        async: false,
                                        type: "POST",
                                        url: "{{ route('api.getManagerId') }}", // do zamiany
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: {
                                            'coaching_id'         : coaching_id
                                        },
                                        success: function (response) {
                                            return response[0];
                                        }
                                    });
                                    //sprawdzenie czy osoba jest od hr
                                    let manager_type_id_JSON = JSON.parse(manager_type_id.responseText);
                                    manager_type_id_JSON = manager_type_id_JSON[0];
                                    let isHR = false;
                                    if(manager_type_id_JSON == 5)
                                        isHR = true;

                                    var user_department_type ="{{$user_department_type}}";

                                    if(user_department_type == 2 && isHR == false) {
                                        if (data.coaching_type == 1) {
                                            if (parseFloat(data.actual_avg) >= parseFloat(data.average_goal))
                                                return span_good_start + data.actual_avg + span_end;
                                            else
                                                return span_bad_start + data.actual_avg + span_end;
                                        } else if (data.coaching_type == 2) {
                                            if (parseFloat(data.actual_janky) < parseFloat(data.janky_goal))
                                                return span_good_start + data.actual_janky + span_end;
                                            else
                                                return span_bad_start + data.actual_janky + span_end;
                                        } else if (parseFloat(data.actual_rbh) >= parseFloat(data.rbh_goal))
                                            return span_good_start + data.actual_rbh + span_end;
                                        else
                                            return span_bad_start + data.actual_rbh + span_end;
                                    }else if(user_department_type == 1 || isHR == true) {
                                        if(data.coaching_type == 1){
                                            return '<input type="number" name="average_avg_inp" class="typed_by_user form-control">'
                                        }else if(data.coaching_type == 2){
                                            return '<input type="number" name="average_janky_inp" class="typed_by_user form-control">'
                                        }else
                                            return '<input type="number" name="average_rbh_inp" class="typed_by_user form-control">'
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
                            {"data":function (data, type, dataToSet) {
                                    let comment = 'Brak';
                                    if(data.comment != null){
                                        comment = data.comment;
                                    }
                                    return "<textarea class='form-control comment_class' id=text_"+data.id+">"+comment+"</textarea>";
                                },"name": "comment"
                            },
                            {"data":function (data, type, dataToSet) {
                                    return "<button class='btn-accept_coaching btn btn-success btn-block' data-id="+data.id+" >Akceptuj</button>";
                                },"orderable": false, "searchable": false
                            },
                        ],

                    });

                    $('#date_start_unsettled, #date_stop_unsettled,#type_coaching_unsettled').on('change',function (e) {
                        table_unsettled.ajax.reload();
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
                                d.type          = $('#type_coaching_settled').val()
                                d.report_status = 1;
                                d.date_start = $('#date_start_settled').val();
                                d.date_stop =  $('#date_stop_settled').val();
                                d.coaching_level = 2;
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

                                    if(data.coaching_type == 1){
                                        if(parseFloat(data.average_end) >= parseFloat(data.average_goal))
                                            return span_good_start+data.average_end+span_end;
                                        else
                                            return span_bad_start+data.average_end+span_end;
                                    }else if(data.coaching_type == 2){
                                        if(parseFloat(data.janky_end) < parseFloat(data.janky_goal))
                                            return span_good_start+data.janky_end+span_end;
                                        else
                                            return span_bad_start+data.janky_end+span_end;
                                    }else
                                    if(parseFloat(data.rbh_end) >= parseFloat(data.rbh_goal))
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

                    $('#date_start_settled, #date_stop_settled,#type_coaching_settled').on('change',function (e) {
                        table_settled.ajax.reload();
                    });

                    $('.form_date').datetimepicker({
                        language:  'pl',
                        autoclose: 1,
                        minView : 2,
                        pickTime: false,
                    });
                })
            </script>
@endsection
