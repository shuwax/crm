@extends('layouts.main')
@section('content')
<style type="text/css">
      body{margin:40px;}
      .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
      }
      .btn-circle.btn-lg {
        width: 50px;
        height: 50px;
        padding: 13px 13px;
        font-size: 18px;
        line-height: 1.33;
        border-radius: 25px;
      }
      .selected-span {
        font-size: 30px;
        margin-left: 10px;
      }
      .btn {
         outline: none !important;
         box-shadow: none !important;
      }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>Ocena testu</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <b>Data testu</b>
            </div>
            <div class="panel-body">
                {{$test->created_at}}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <b>Tytuł testu</b>
            </div>
            <div class="panel-body">
                {{$test->name}}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <b>Użytkownik</b>
            </div>
            <div class="panel-body">
                {{$test->user->first_name . ' ' . $test->user->last_name}}
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs">
    @php($i = 0)
    @foreach($test->questions as $item)
        @php($i++)
        <li @if($i == 1) class="active" @endif>
            <a data-toggle="tab" href="#question{{$item->id}}">
                Pytanie nr {{$i}}
            </a>
        </li>
    @endforeach
    <li><a data-toggle="tab" href="#question_total">Ocena ogólna</a></li>
</ul>

<form method="POST" action="{{URL::to('/check_test')}}" id="checkForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="tab-content">

        @php($i = 0)
        @foreach($test->questions as $item)
            @php($i++)
            <div id="question{{$i}}" class="tab-pane @if($i == 1) fade in active @endif">
                    <div class="form-group" style="margin-top: 30px">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <b>Treść pytania</b>
                            </div>
                            <div class="panel-body">
                               {{$item->testQuestion->content}}
                            </div>
                        </div>
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <b>Odpowiedź użytkownika</b>
                            </div>
                            <div class="panel-body">
                                {{$item->user_answer}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment_question1">Dodaj komentarz (opcjonalne):</label>
                        <textarea class="form-control" name="comment_question[]" placeholder="Twój komentarz..." rows="5"></textarea>
                    </div>
                </div>
        @endforeach
        
        <div id="question_total" class="tab-pane fade">
            <div class="form-group" style="margin-top: 30px">
                <label>Test został zaliczony:</label>
                <div data-toggle="buttons">
                    <label id="q1_yes" class="btn btn-success btn-circle btn-lg"><input type="radio"  name="q1" value="1"><i class="glyphicon glyphicon-ok"></i></label>
                    <label id="q1_no"  class="btn btn-danger btn-circle btn-lg"><input type="radio" name="q1" value="2"><i class="glyphicon glyphicon-remove"></i></label>
                    <span class="selected-span" id="q1_span"></span>
                </div>
            </div>
            <div class="alert alert-danger" style="display: none" id="alert_checked">
                Zaznacz wynik testu!
            </div>
            <div class="form-group">
                <h3>Użytkownik zostanie poinformowany o wyniku testu drogą mailową.</h3>
            <div>
            <br />
            <div class="form-group">
                <input type="submit" class="btn btn-success btn-lg" value="Prześlij ocenę" id="send_opinion"/>
            <div>
        </div>
    </div>
    <input type="hidden" value="{{$test->id}}" name="test_id" />
</form>

@endsection

@section('script')
<script>

$("#q1_yes").on('click', () => {
    $('#q1_span').text('TAK');
});
$("#q1_no").on('click', () => {
    $('#q1_span').text('NIE');
});


$('#send_opinion').on('click', function(e) {
    e.preventDefault();
    var checkStatus = $('input[name=q1]:checked').val();

    if (checkStatus == null) {
        $('#alert_checked').slideDown(1000);
        return false;
    } else {
        $('#alert_checked').slideUp(1000);
        $('#checkForm').submit();
    }

});
</script>
@endsection
