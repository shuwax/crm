@extends('layouts.main')
@section('content')
    {{--****************************************--}}
    {{--THIS PAGE SHOWS CONTROL PANEL FOR AUDITS--}}
    {{--****************************************--}}
    <style>
        .active {
            display: block;
        }
        .inactive {
            display: none;
        }

        .glyphicon-remove {
            transition: all 0.8s ease-in-out;
        }
        .glyphicon-remove:hover {
            transform: scale(1.2) rotate(180deg);
            cursor: pointer;
        }

    </style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <div class="alert gray-nav">Edycja Audytów</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default panel-primary first-panel">
                <div class="panel-heading">
                    Edycja
                </div>

                <div class="panel-body">
                    <div class="alert alert-info firstClick">Po naciśnięciu na dowolny nagłówek uzyskasz podgląd powiązanych kryteriów</div>
                    <form action="{{URL::to('/editAuditPage')}}" method="post" id="formularz">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row row1">
                        <div class="col-md-6">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nagłówki</th>
                                        <th>Usuń</th>
                                    </tr>
                                </thead>
                                <tbody class="tableInside">
                                @foreach($headers as $h)
                                    @if($h->status == $status)
                                    <tr>
                                        <td id="{{$h->id}}" class="headers">{{$h->name}}</td>
                                        <td><span class="glyphicon glyphicon-remove gl-heads"  style="font-size:2em;color:red;" data-headid="{{$h->id}}"></span></td>
                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kryteria</th>
                                        <th>Usuń</th>
                                    </tr>
                                </thead>
                                <tbody id="crit">
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row row2">
                        <div class="col-md-6 headerAddDel">
                            <button type="button" class="btn btn-info bt" id="firstHeaderAdd" style="width:100%;">Dodaj nowy nagłówek</button>
                        </div>
                        <div class="col-md-6" critAddDel>
                            <button type="button" class="btn btn-info bt" id="firstCritAdd" style="width:100%;">Dodaj nowe kryterium</button>
                        </div>
                    </div>
                        <div class="row row-between">
                            <div class="col-md-6">
                                <div class="form-group headerInpDiv">
                                    <p>Podaj nazwę nowego nagłówka:</p>
                                    <input type="text" name="newHeaderName" class="form-control headerInp" style="margin-top:1em;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group critInpDiv">
                                    <p>Podaj nazwę nowego kryterium:</p>
                                    <input type="text" name="newCritName" class="form-control critInp" style="margin-top:1em;">
                                </div>
                                <div class="form-group relatedHeaderDiv">
                                    <label for="selectRelatedHeader">Wybierz nagłówek do którego ma być dodane kryterium:</label>
                                    <select name="relatedHeader" class="form-control headerRelated" style="margin-top:1em;" id="selectRelatedHeader">
                                        <option value="0">Wybierz</option>
                                        @foreach($headers as $h)
                                            @if($h->status == $status)
                                            <option value="{{$h->id}}">{{$h->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row row4">
                            <div class="col-md-6">
                                <button class="btn btn-info btn-header" type="submit" style="margin:1em;">Akceptuj</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info btn-crit" type="submit" style="margin:1em;">Akceptuj</button>
                            </div>
                        </div>
                        <input type="hidden" name="status" value="{{$status}}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

<script>
    document.addEventListener("DOMContentLoaded", function(event) {

        /****************FIRST STAGE********************/
        //selects each row
        var row2 = document.getElementsByClassName('row2')[0];
        var row4 = document.getElementsByClassName('row4')[0];
        var rowBetween = document.getElementsByClassName('row-between')[0];

        //hide rows 2-4
        row4.classList.add('inactive');
        rowBetween.classList.add('inactive');

        var headers = Array.from(document.querySelectorAll('.headers'));
        var buttons = Array.from(document.querySelectorAll('.bt'));
        var selects1 = Array.from(document.querySelectorAll('.selectItem1'));
        var selects2 = Array.from(document.querySelectorAll('.selectItem2'));
        var headerSubmit = document.querySelector('.btn-header');
        var critSubmit = document.querySelector('.btn-crit');
        var headerInp = document.querySelector('.headerInp');
        var critInp = document.querySelector('.critInp');
        var headerInpDiv = document.querySelector('.headerInpDiv');
        var critInpDiv = document.querySelector('.critInpDiv');
        var relatedHeader = document.querySelector('.relatedHeaderDiv');
        var selectRelated = document.querySelector('.headerRelated');
        var critGl = [];
        var headGl = Array.from(document.querySelectorAll('.gl-heads'));

        var addingHeader;
        var addingCrit;
        var headerVal;
        var critVal;
        var indexOfSelected;
        var lastOneSelected;
        var lastOneSelected2;
        var firstClick = 0;

        //This function gets data from database about criterions related to given heading and paste them into table and inpuct(type=select)
        function handleClick(e) {
            var tableBody = document.getElementById('crit');
            row2.classList.remove('inactive');

            //Part responsible for highlighting clicked row
            if(indexOfSelected != null) {
                lastOneSelected.style.backgroundColor="white";
                lastOneSelected2.style.backgroundColor="white";
            }
            e.target.style.backgroundColor='#CBE86B';
            e.target.nextElementSibling.style.backgroundColor='#CBE86B';
            indexOfSelected = e.target.id;
            lastOneSelected = document.querySelector('.tableInside td[id="' + indexOfSelected + '"]');
            lastOneSelected2 = lastOneSelected.nextElementSibling;

            if(firstClick === 0) {
                document.querySelector('.firstClick').style.display = 'none';
                firstClick = 1;
            }

            tableBody.textContent = '';
            $.ajax({ //generate list of trainers from given location
                type: "POST",
                url: '{{ route('api.editAudit') }}',
                data: {
                    "header_id": e.target.id,
                    "status": {{$status}}
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    for(var i = 0; i < response.length; i++) {
                        var newItem = $('<tr><td class="crits" data-id="' + response[i].id + '">' + response[i].name.replace(/_/g , " ") + '</td><td><span style="font-size:2em;color:red;" class="glyphicon glyphicon-remove gl-crits" data-critId="' + response[i].id + '"></span></td></tr>');
                        var option = $('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                        $('#crit').append(newItem);
                        $('#firstOption').after(option);
                    }
                }
            });
        }

        function critGlHandle(e) {
                swal({
                    title: 'Jesteś pewien?',
                    text: "Po potwierdzeniu, brak możliwości cofnięcia zmian!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Usuń!'
                }).then((result) => {
                    if (result.value) {
                    addingCrit = false;
                    var $input = $('<input type="hidden" name="addingCrit" value="' + addingCrit + '">');
                    $('.btn-crit').after($input);
                    var $input2 = $('<input type="hidden" name="cID" value="' + e.target.dataset.critid + '">');
                    $('.btn-crit').after($input2);
                    document.querySelector('#formularz').submit();
                    swal(
                        'Usunięte!',
                        'Kryterium zostało usunięte',
                        'success'
                    )
                }
            });
        }

        function headGlHandle(e) {
                swal({
                    title: 'Jesteś pewien?',
                    text: "Po potwierdzeniu, brak możliwości cofnięcia zmian!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Usuń!'
                }).then((result) => {
                    if (result.value) {
                    addingHeader = false;
                    var $input = $('<input type="hidden" name="addingHeader" value="' + addingHeader + '">');
                    $('.btn-crit').after($input);
                    var $input2 = $('<input type="hidden" name="hid" value="' + e.target.dataset.headid + '">');
                    $('.btn-crit').after($input2);
                    document.querySelector('#formularz').submit();
                    swal(
                        'Usunięte!',
                        'Kryterium zostało usunięte',
                        'success'
                    )
                }
            });
        }

        $( document ).ajaxComplete(function() {
            critGl = Array.from(document.querySelectorAll('.gl-crits'));
            critGl.forEach(gl => gl.addEventListener('click', critGlHandle));
        });

        headGl.forEach(gl => gl.addEventListener('click', headGlHandle));
        headers.forEach(headers => headers.addEventListener('click', handleClick));
        /**************************END FIRST STAGE******************************/

        /****************************SECOND STAGE*******************************/
        //function responsible for seting value of variable starting with "adding" which determine if item should be added or deleted
        function handleButton(e) {
            if(e.target.id == 'firstHeaderAdd') {
                addingHeader = true;
                rowBetween.classList.remove('inactive');
                critInpDiv.classList.add('inactive');
                headerInpDiv.classList.remove('inactive');
                relatedHeader.classList.add('inactive');
                row4.classList.remove('inactive');
            }
            else if(e.target.id == 'firstHeaderDelete') {
                addingHeader = false;
                row4.classList.remove('inactive');
                critInpDiv.classList.add('inactive');
                rowBetween.classList.add('inactive');
                relatedHeader.classList.add('inactive');
            }

            else if(e.target.id == 'firstCritAdd') {
                addingCrit = true;
                rowBetween.classList.remove('inactive');
                headerInpDiv.classList.add('inactive');
                critInpDiv.classList.remove('inactive');
                relatedHeader.classList.remove('inactive');
                row4.classList.remove('inactive');
            }
            else if(e.target.id == 'firstCritDelete') {
                addingCrit = false;
                row4.classList.remove('inactive');
                headerInpDiv.classList.add('inactive');
                relatedHeader.classList.add('inactive');
                rowBetween.classList.add('inactive');
            }
        }

        buttons.forEach(button => button.addEventListener('click', handleButton));

        /***********************END SECOND STAGE ***************************/

        /*************************THIRD STAGE*******************************/
        //AFTER SELECTION OF CRIT/HEADER SHOW SUBMIT BUTTONS
        function handleSelect1(e) {
            var selected = e.target;
            headerVal =  selected.options[selected.selectedIndex].value;
            row4.classList.remove('inactive');
            if(headerVal == '0' || headerVal == 0) {
                row4.classList.add('inactive');
            }
        }

        function handleSelect2(e) {
            var selected = e.target;
            critVal =  selected.options[selected.selectedIndex].value;
            row4.classList.remove('inactive');
            if(critVal == '0' || critVal == 0) {
                row4.classList.add('inactive');
            }
        }

        //Show/hide 'akceptuj' button
        function headSelectChange(e) {
            row4.classList.remove('inactive');
            if(e.target.value == '') {
                row4.classList.add('inactive');
            }
        }

        function critSelectChange(e) {
            row4.classList.remove('inactive');
            if(e.target.value == '') {
                row4.classList.add('inactive');
            }
        }

        headerInp.addEventListener('input', headSelectChange);
        critInp.addEventListener('input', critSelectChange);

        selects1.forEach(select => select.addEventListener('change', handleSelect1));
        selects2.forEach(select => select.addEventListener('change', handleSelect2));

        /*****************************END THIRD STAGE************************/

        /*******************************FOURTH STAGE*************************/
        //Handle submit (add hidden input with info about adding/deleting)
        function handleHeaderSubmit(e) {
            e.preventDefault();
            var $input = $('<input type="hidden" name="addingHeader" value="' + addingHeader + '">');
            $('.btn-crit').after($input);

            if(addingHeader) {
                if($('.headerInp').val() != null && $('.headerInp').val() != '') {
                    document.querySelector('#formularz').submit();
                }
            }
            else {
                if($('.selectItem1').val() != 0) {
                    document.querySelector('#formularz').submit();
                }
            }
        }

        function handleCritSubmit(e) {
            e.preventDefault();
            var $input = $('<input type="hidden" name="addingCrit" value="' + addingCrit + '">');
            $('.btn-crit').after($input);

            if(addingCrit) {
                if($('.critInp').val() != null && $('.critInp').val() != '') {
                    if($('.headerRelated').val() != 0) {
                        document.querySelector('#formularz').submit();

                    }
                    else {
                        swal('Wybierz nagłówek dla którego ma być dodane nowe kryterium');
                    }
                }
            }
            else {
                if($('.selectedItem2').val() != 0) {
                    document.querySelector('#formularz').submit();

                }
            }
        }

        headerSubmit.addEventListener('click', handleHeaderSubmit);
        critSubmit.addEventListener('click', handleCritSubmit);

        /****************************END FOURTH STAGE************************/
    });
</script>
@endsection
