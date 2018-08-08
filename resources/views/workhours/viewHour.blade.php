@extends('layouts.main')
@section('style')
    <style>
        .table td {
                 text-align: center;
             }
        table {
            table-layout: fixed;
            word-wrap: break-word;
        }
        .action{
            width: 69px;
        }

    </style>
    @endsection
@section('content')
<div class="page-header">
    <div class="alert gray-nav ">Godziny / Podgląd godzin</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        Zakres wyszukiwania:
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <div id="start_stop">
                    <div class="panel-body">
                        <form class="form-horizontal" method="post" action="view_hour" id="my_form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <label for ="ipadress">Pracownik:</label>
                                    <select class="form-control" name="userid" id="userid">
                                        @if(isset($response_userid))
                                            <option value="-1">Wybierz</option>
                                            @foreach ($users as $user)
                                                @if($response_userid == $user->id)
                                                    <option  selected value={{$user->id}} >{{ $user->last_name. ' '.$user->first_name}}</option>
                                                @else
                                                    <option value={{$user->id}}>{{ $user->last_name. ' '.$user->first_name}}</option>
                                                @endif
                                             @endforeach
                                        @else
                                        <option selected value="-1">Wybierz</option>
                                            @foreach ($users as $user)
                                                <option value={{$user->id}}>{{ $user->last_name. ' '.$user->first_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    <label for ="ipadress">Miesiąc:</label>
                                    <select class="form-control" name="month">
                                        @for ($i = 0; $i < 3; $i++)
                                            {{$date = date("Y-m",mktime(0,0,0,date("m")-$i,1,date("Y")))}}
                                            @if(isset($response_month))
                                                @if($response_month == $date)
                                                    <option selected>{{$date}}</option>
                                                @else
                                                    <option>{{$date}}</option>
                                                @endif
                                            @else
                                                    <option>{{$date}}</option>
                                            @endif
                                        @endfor

                                    </select>

                                </div>
                                <div class="form-group">
                                        <button id="form_submit" disabled class="btn btn-primary" name="submit" type="submit" style="width: 100%">
                                            Generuj
                                        </button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                                @if(isset($response_userid))
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Godziny pracy:
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="start_stop">
                                                    <div class="panel-body">


                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Data</th>
                                                                <th>Rej.</th>
                                                                <th>Akc.</th>
                                                                <th>Godz.</th>
                                                                <th>Pod.</th>
                                                                @if($agreement == 1)
                                                                    <th>Zgody</th>
                                                                    <th>Średnia</th>
                                                                @endif
                                                                <th>Status</th>
                                                                <th>Akcja</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $dateexplode = explode("-", $response_month);
                                                            $daysinmonth = date("t",mktime(0,0,0,$dateexplode[1],1,$dateexplode[0]));
                                                            $total_time = 0;
                                                            $cash_sum = 0;
                                                            $total_success = 0;
                                                            $total_avg = 0;
                                                            $iteration = 0;
                                                            ?>
                                                            @for ($i=0; $i < $daysinmonth; $i++)
                                                                <?php
                                                                $date = date("Y-m-d",mktime(0,0,0,$dateexplode[1],1+$i,$dateexplode[0]));
                                                                $check = 0;
                                                                ?>
                                                                    @foreach ($response_user_info as $item)
                                                                        @if($item->date == $date)
                                                                            <?php $check++?>
                                                                            <?php
                                                                            if($item->success == 0)
                                                                                $avg = number_format (0,2);
                                                                            else
                                                                                $avg = number_format ( $item->success/($item->second/3600), 2 );

                                                                            if($item->status == 3 && $item->id_manager == null)
                                                                                $status = 'Oczekuje na akceptacje';
                                                                            if($item->status == 4 && $item->id_manager != null)
                                                                                $status = 'Zaakceptowano przez:'."\n".$item->first_name.' '.$item->last_name;
                                                                            elseif($item->status == 5)
                                                                                $status = 'Zmodyfikowano przez:'."\n".$item->first_name.' '.$item->last_name;
                                                                            elseif($item->status == 6)
                                                                                $status = 'Usunięto przez:'."\n".$item->first_name.' '.$item->last_name;
                                                                            ?>
                                                                            <tr>
                                                                                <td>{{$item->date}}</td>
                                                                                <td>
                                                                                    <div>{{substr($item->register_start,0,-3)}}</div>
                                                                                    <span class='fa fa-arrow-circle-o-down fa-fw'></span>
                                                                                    <div>{{substr($item->register_stop,0,-3)}}</div>
                                                                                </td>
                                                                                <td class="accept_hour">
                                                                                    <div class="accept_hour_start" >{{substr($item->accept_start,0,-3)}}</div>
                                                                                        <span class='fa fa-arrow-circle-o-down fa-fw'></span>
                                                                                    <div class="accept_hour_stop" >{{substr($item->accept_stop,0,-3)}}</div>
                                                                                </td>
                                                                                <td>{{$item->time}}</td>
                                                                                @php
                                                                                    if (isset($item->time)) {
                                                                                        $time_array = explode(':', $item->time);
                                                                                        $hours_to_minute = $time_array[0] * 60;
                                                                                        $time_sum = $hours_to_minute + $time_array[1];
                                                                                        $total_time += $time_sum;
                                                                                    }
                                                                                @endphp
                                                                                <td>{{number_format ( ($item->second/3600)*$item->rate, 2 )}} PLN</td>
                                                                                @php
                                                                                    $cash_sum += number_format ( ($item->second/3600)*$item->rate, 2 );
                                                                                @endphp
                                                                                @if($agreement == 1)
                                                                                    <td class="succes_count">{{$item->success}}</td>
                                                                                    @php
                                                                                        $total_success += $item->success;
                                                                                        $total_avg += $avg;
                                                                                        $iteration++;
                                                                                    @endphp
                                                                                    <td>{{$avg}}</td>
                                                                                @endif
                                                                                <td>{{$status}}</td>
                                                                                <td>
                                                                                    <button type="button" id={{$item->id}} class="btn btn-danger action delete">Usuń</button>
                                                                                    <button type="button" data-toggle="modal" data-target="#editHourModal" id={{$item->id}} class="btn btn-info action edit">Edycja</button>
                                                                                </td>
                                                                            </tr>
                                                                            @endif
                                                                    @endforeach
                                                                @if($check == 0)
                                                                    <tr>
                                                                        <td>{{$date}}</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        @if($agreement == 1)
                                                                        <td></td>
                                                                        <td></td>
                                                                        @endif
                                                                        <td></td>
                                                                        <td>
                                                                            <button type="button" data-toggle="modal" data-target="#addHourModal" id={{$response_userid.'/'.$date}} class="btn btn-success action edit">Dodaj</button>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endfor
                                                            <tr>
                                                                <td colspan="3"><b>Suma</b></td>
                                                                @php
                                                                    $hours_sum = round($total_time );
                                                                    $minutes = ($total_time % 60 < 10) ? ("0" . ($total_time % 60)) : $total_time % 60 ;
                                                                    $time_string = round($total_time/60,2);
                                                                @endphp
                                                                <td><b>{{$time_string}} RBH</b></td>
                                                                <td><b>{{$cash_sum}} PLN</b></td>
                                                                @if($agreement == 1)
                                                                <td><b>{{$total_success}}</b></td>
                                                                    @if($time_string > 0)
                                                                        <td><b>{{round($total_success / $time_string, 2)}}</b></td>
                                                                    @else
                                                                        <td><b>0</b></td>
                                                                    @endif
                                                                @endif
                                                                <td></td>
                                                                <td></td>
                                                            <tr/>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

@endsection
@include('workhours.editHour');
@include('workhours.addHour');
@section('script')
    <script>

        var userid = $('#userid').val();
        if (userid != -1) {
            $("#form_submit").removeAttr('disabled');
        }else if (userid == -1) {
            $("#form_submit").attr('disabled',true);
        }
        $("#userid").change(function () {
            var userid = $('#userid').val();
            if (userid != -1) {
                $("#form_submit").removeAttr('disabled');
            }else if (userid == -1) {
                $("#form_submit").attr('disabled',true);
            }
        });

        $( ".delete" ).click(function() {

          swal({
            title: 'Usunąć godziny pracy?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Tak'
          }).then((result) => {
            if (result.value) {
              var id = (this.id);
              $(this).attr('disabled',true);
              $.ajax({
                  type: "POST",
                  url: '{{ route('api.deleteAcceptHour') }}',
                  data: {
                      "id": id
                  },
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                      if (response == 1) {
                          swal({
                              title: 'Godziny Usunięto!',
                              text: '',
                              }).then((result) => {
                              if (result.dismiss === 'timer') {
                                  $('#form_submit').trigger('click');
                              } else {
                                  $('#form_submit').trigger('click');
                              }
                          })
                      } else {
                          swal({
                              title: 'Ups! Coś poszło nie tak, Skontaktuj się z administratorem.',
                              text: '',
                              }).then((result) => {
                              if (result.dismiss === 'timer') {
                                  $('#form_submit').trigger('click');
                              } else {
                                  $('#form_submit').trigger('click');
                              }
                          })
                      }
                  }
              });
            }
          })
        });
    </script>
@endsection
