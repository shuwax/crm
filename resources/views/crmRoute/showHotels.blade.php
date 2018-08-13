{{--/*--}}
{{--*@category: CRM,--}}
{{--*@info: This view shows list of available hotels (DB table: "hotels"),--}}
{{--*@controller: CrmRouteController,--}}
{{--*@methods: showHotelsAjax, showHotelsGet, findHotel, uploadHotelFilesAjax, downloadHotelFiles--}}
{{--*/--}}


@extends('layouts.main')
@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')

    <style>
        .colorComment {
            background: #fffc8b !important;
        }
        .colorTurnedOff {
            background: #c500002e !important;
        }
        .dropdown-menu {
            left: 0px !important;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>

{{--Header page --}}
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <div class="alert gray-nav ">Podgląd Hoteli</div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                   Wybierz hotel
                </div>
                <div class="panel-body">
                    <div class="alert alert-info">
                        Moduł <strong>Podgląd hoteli</strong> pozwala dodawać oraz edytować hotele. Każdy hotel może zostać włączony/wyłączony przyciskami <button class='btn btn-danger'><span class='glyphicon glyphicon-off'></span> Wyłącz</button> <button class='btn btn-success'><span class='glyphicon glyphicon-off'></span> Włącz</button>
                        Po naciśnieciu przycisku <button class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button> można podejżeć wszystkie informacje dotyczące hotelu. <br>
                        Listy <strong>Województwo</strong>, <strong>Miasto</strong> oraz <strong>Kod pocztowy</strong> są listami wielokrotnego wyboru.
                    </div>
                    @if($hotelId == 0)
                    <div class="row">
                        <div class="col-md-12">
                            <button data-toggle="modal" class="btn btn-default hotelToModal" id="NewHotelModal"
                                    data-target="#HotelModal" data-id="1" title="Nowy Hotel"
                                    style="margin-bottom: 14px">
                                <span class="glyphicon glyphicon-plus"></span> <span>Dodaj Hotel</span>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="voivode">Województwo</label>
                                <select name="voivode" id="voivode" class="form-control select2-container" multiple="multiple">
                                    {{--<option value="0">Wybierz</option>--}}
                                    @foreach($voivodes as $voivode)
                                        <option value="{{$voivode->id}}">{{$voivode->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city">Miasto</label>
                                <select name="city" id="city" class="form-control select2-container" multiple="multiple">
                                    {{--<option value="0">Wybierz</option>--}}
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zipCode">Kod pocztowy</label>
                                <select name="zipCode" id="zipCode" class="form-control select2-container" multiple="multiple">
                                    {{--<option value="0">Wybierz</option>--}}
                                    @foreach($zipCode as $item)
                                        <option>{{$item->zip_code}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-md-4">
                            <form action="/showHotels" method="get">
                                <button type="submit" class="btn btn-block btn-info">Pokaż wszystkie hotele</button>
                            </form>
                        </div>
                    </div>
                    @endif
                        <div class="col-mg-12">
                            <table id="datatable" class="thead-inverse table row-border table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Nazwa</th>
                                    <th>Wojewodztwo</th>
                                    <th>Miasto</th>
                                    <th>Ulica</th>
                                    <th>Kod Pocztowy</th>
                                    <th>Akcja</th>
                                    <th>Podgląd</th>
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



<div id="HotelModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal_title">Dodaj Hotel<span id="modalHotel"></span></h4>
            </div>
            <div class="modal-body">
                <form id="newHotelForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-container">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nazwa Hotelu</label>
                                    <input type="text" id="name" class="form-control" name="name" placeholder="Tutaj wprowadź nazwę hotelu" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="voivode">Województwo</label>
                                    <select name="voivodeAdd" id="voivodeAdd" class="form-control selectpicker" data-element="voivode" required>
                                        <option value="0">Wybierz</option>
                                        @foreach($voivodes as $voivode)
                                            <option value ="{{$voivode->id}}">{{$voivode->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">Miasto</label>
                                    <select name="cityAdd" id="cityAdd" class="form-control selectpicker" required>
                                        <option value="0">Wybierz</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="street">Ulica</label>
                                    <input type="text" name="street" id="street" class="form-control" placeholder="Nazwa Ulicy" value="">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="myLabel">Kod pocztowy:</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <input type="text" id="zipCode1" class="form-control zipCode" placeholder="_" aria-describedby="basic-addon1" style="text-align: center; padding: 1px">
                                                <span class="input-group-addon" id="basic-addon1" style="padding: 0px"></span>
                                                <input type="text" id="zipCode2" class="form-control zipCode" placeholder="_" aria-describedby="basic-addon1" style="text-align: center; padding: 1px">
                                                <span class="input-group-addon" id="basic-addon1" style="padding: 3px;">-</span>
                                                <input type="text" id="zipCode3" class="form-control zipCode" placeholder="_" aria-describedby="basic-addon1" style="text-align: center; padding: 1px">
                                                <span class="input-group-addon" id="basic-addon1"style="padding: 0px"></span>
                                                <input type="text" id="zipCode4" class="form-control zipCode" placeholder="_" aria-describedby="basic-addon1" style="text-align: center; padding: 1px">
                                                <span class="input-group-addon" id="basic-addon1"style="padding: 0px"></span>
                                                <input type="text" id="zipCode5" class="form-control zipCode" placeholder="_" aria-describedby="basic-addon1" style="text-align: center; padding: 1px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="parking">Parking</label>
                                    <select name="parking" id="parking" class="form-control selectpicker" required>
                                        <option value="-1">Wybierz</option>
                                        <option value="0">NIE</option>
                                        <option value="1">TAK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="clientsExceptions">Wykluczeni klienci</label>
                                    <select name="clientsExceptions" id="clientsExceptions" class="selectpicker form-control"
                                            multiple="multiple" title="Wybierz klientów..." data-width="100%" data-live-search=”true”>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}">{{$client->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{--<div class="col-md-3">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="hourBid">Typ stawki</label>--}}
                                    {{--<select name="bidType" id="bidType" class="form-control selectpicker" required>--}}
                                        {{--<option value="0">Wybierz</option>--}}
                                        {{--<option value="1">Godzinowa</option>--}}
                                        {{--<option value="2">Dzienna</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="hourBid">Stawka godzinowa</label>--}}
                                    {{--<input type="number" name="hourBid" id="hourBid" class="form-control" placeholder="Stawka godzinowa (zł)" min="0" value="" disabled>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="dailyBid">Stawka dzienna</label>--}}
                                    {{--<input type="number" name="dailyBid" id="dailyBid" class="form-control" placeholder="Stawka dzienna (zł)" min="0" value="" disabled>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentMethod">Forma płatności</label>
                                    <select name="paymentMethod" id="paymentMethod" class="form-control selectpicker" required>
                                        <option value="0">Wybierz</option>
                                        @foreach($paymentMethods as $paymentMethod)
                                            <option value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{--<div class="col-md-4">
                                <label for="order">Zamówienie</label>
                                <input type="file" name="order" id="order" class="form-control" style="padding-bottom: 3em">
                            </div>--}}
                            <div class="col-md-4">
                                <label for="invoiceTemplate">Nowy szablon faktury</label>
                                <input type="file" name="invoice_template" id="invoiceTemplate" class="form-control file" style="padding-bottom: 3em">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="comment">Uwagi</label>
                                    <input type="text" name="comment" id="comment" class="form-control" placeholder="Tutaj wprowadź krótki komentarz max 255 znaków" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1em">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Kontakty
                                    </div>
                                    <div class="panel-body">
                                        <div class="alert alert-info" role="alert">Zaznaczenie telefonu i adresu mailowego określa te kontakty jako sugerowane.</div>
                                        <div class="contactsContainer">
                                            <div class="row">
                                                <div class="col-md-6 phonesContainer">
                                                    <div class="row"  style="margin-top: 1em">
                                                        <div class="col-md-12">
                                                            <label>Numery telefonów</label>
                                                        </div>
                                                    </div>
                                                    <div class="row"  style="margin-top: 1em">
                                                        <div class="col-md-10" style="text-align: right">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" id="addPhoneNumberButton" class="btn btn-success btn-block"><span class="glyphicon glyphicon-plus"></span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mailsContainer">
                                                    <div class="row"  style="margin-top: 1em">
                                                        <div class="col-md-12">
                                                            <label>Adresy mailowe</label>
                                                        </div>
                                                    </div>
                                                    <div class="row"  style="margin-top: 1em">
                                                        <div class="col-md-10" style="text-align: right">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" id="addEmailButton" class="btn btn-success btn-block"><span class="glyphicon glyphicon-plus"></span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type='submit' class="btn btn-default form-control" id="saveHotel">
                                    <span class='glyphicon glyphicon-plus'></span> Dodaj Hotel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>{{--modal-body end--}}
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="0" id="hotelId"/>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('/js/dataTables.bootstrap.min.js')}}"></script>
    <script>

        //flaga dodania nowego hotelu - po poprawnym wykonaniu ajaxa wyswietli sie komunikat
        var addNewHotelFlag = false;
        var editHotelFlag = false;
        var saveHotelFlag = false;
        var hotelStatus = 1;
        var city = 0;/*
        let zipCode2 = document.getElementById('zipCode2');
        let zipCode1 = document.getElementById('zipCode1');*/

        function clearContent(container) {
            container.innerHTML = '';
        }
        // czyszczenie modalu
        function clearModal(preview = false) {
            cityId = 0;
            addNewHotelFlag = false;
            editHotelFlag = false;

            let nameInput = $("#name");
            let streetInput = $('#street');
            let voivodeAddInput = $('#voivodeAdd');
            let cityAddInput = $('#cityAdd');
            let commentInput = $('#comment');
            let hotelIdInput = $('#hotelId');
            let parkingInput = $('#parking');
            let paymentMethodInput = $('#paymentMethod');
            let invoiceTemplateInput = $('#invoiceTemplate');
            let clientsExceptionsInput = $('#clientsExceptions');
            let zipCodeInputs = $('.zipCode');
            // let bidType = $('#bidType');
            // let hourBidInput = $('#hourBid');
            // let dailyBidInput = $('#dailyBid');
            let contactInputs = $('.contactsContainer .contact');

            let addPhoneNumberButton = $('#addPhoneNumberButton');
            let addEmailButton = $('#addEmailButton');
            let saveHotelButton = $('#HotelModal #saveHotel');

            if(!preview){
                invoiceTemplateInput.closest('div').css('display','block');
                saveHotelButton.closest('div').css('display','block');
                addPhoneNumberButton.closest('div').css('display','block');
                addEmailButton.closest('div').css('display','block');
                saveHotelButton.first().text('');
                saveHotelButton.first().prop('class','btn btn-default form-control');
                saveHotelButton.first().append($('<span class="glyphicon glyphicon-plus"></span>'));
                saveHotelButton.first().append(' Dodaj Hotel');
            }else{
                invoiceTemplateInput.closest('div').css('display','none');
                saveHotelButton.closest('div').css('display','none');
                addPhoneNumberButton.closest('div').css('display','none');
                addEmailButton.closest('div').css('display','none');
            }
            $('.invoiceTemplate_file').remove();
            $('#HotelModal .modal-title').first().text('Dodaj Hotel');


            nameInput.val("");
            streetInput.val("");
            voivodeAddInput.val(0);
            cityAddInput.val(0);
            commentInput.val("");
            hotelIdInput.val(0);
            parkingInput.val(-1);
            paymentMethodInput.val(0);
            invoiceTemplateInput.val("");
            clientsExceptionsInput.val('');
            zipCodeInputs.val("");
            // hourBidInput.val("");
            // dailyBidInput.val("");
            contactInputs.remove();

            nameInput.prop('readonly',preview);
            streetInput.prop('readonly',preview);
            // bidType.prop('disabled',preview);
            voivodeAddInput.prop('disabled',preview);
            cityAddInput.prop('disabled',preview);
            commentInput.prop('readonly',preview);
            parkingInput.prop('disabled',preview);
            paymentMethodInput.prop('disabled',preview);
            clientsExceptionsInput.prop('disabled',preview);
            zipCodeInputs.prop('readonly',preview);
            // hourBidInput.prop('readonly',preview);
            // dailyBidInput.prop('readonly',preview);

            // bidType.selectpicker('refresh');
            voivodeAddInput.selectpicker('refresh');
            cityAddInput.selectpicker('refresh');
            parkingInput.selectpicker('refresh');
            paymentMethodInput.selectpicker('refresh');
            clientsExceptionsInput.selectpicker('refresh');
        }
        //Walidacja Zapisu
        function saveNewHotel() {
            let DEFFERED = $.Deferred();

            var name = $("#name").val();
            //var price = $('#price').val();
            var street = $('#street').val();
            var voivode = $('#voivodeAdd').val();
            var city = $('#cityAdd').val();
            var comment = $('#comment').val();
            var validate = true;
            let parking = $('#parking').val();
            let paymentMethodId = $('#paymentMethod').val();
            // let dailyBid = $('#dailyBid').val();
            // let hourBid = $('#hourBid').val();
            let hotelId = $('#hotelId').val();
            // let bidType = $('#bidType').val();
            let zipCode ='';
            $('.zipCode').each(function( key, item ) {
                zipCode += item.value;
            });

            // if(bidType == 0){
            //     swal('Wybierz typ stawki')
            //     validate = false;
            // }else{
            //     if(bidType == 2 && (dailyBid == 0 || dailyBid.trim().length == 0 || dailyBid == '')){
            //         swal('Wybierz stawkę dzienną');
            //         validate = false;
            //     }else if (bidType == 1 && (hourBid == 0 || hourBid.trim().length == 0 || hourBid == '')){
            //         swal('Wybierz stawkę godzinową');
            //         validate = false;
            //     }
            // }
            if (zipCode.trim().length < 5) {
                validate = false;
                swal("Podaj kod pocztowy");
            }
            if(parking == -1){
                swal('Wybierz pole z parkingiem');
                validate = false;
            }
            if(paymentMethodId == 0){
                swal('Wybierz formę płatności');
                validate = false;
            }
            if (name.trim().length == 0) {
                swal('Wprowadź nazwę hotelu!');
                validate = false;
            }
            if (street.trim().length == 0) {
                swal('Wprowadź ulicę!');
                validate = false;
            }
            if (voivode == 0) {
                swal('Wybierz województwo!');
                validate = false;
            }
            if (city == 0 || city=='') {
                swal('Wybierz miasto!');
                validate = false;
            }
            $('.hotelPhoneNumber').each(function(key, item){
                if(item.value === ''){
                    swal("Wpisz numery telefonów do wszystkich pól");
                    validate = false;
                    return false;
                }else if(isNaN(item.value)){
                    swal("Podane numery muszą być w formacie liczbowym");
                    validate = false;
                    return false;
                }else if(String(item.value).length !== 9){
                    swal("Numery muszą składać się z 9 cyfr");
                    validate = false;
                    return false;
                }
            });
            $('input[name="hotelPhoneNumber"]').each(function(key, item){
                if(item.checked == true){
                    return false;
                }
                if(key == $('input[name="hotelPhoneNumber"]').length-1) {
                    swal("Zaznacz sugerowany numer telefonu");
                    validate = false;
                }
            });
            $('.hotelEmail').each(function(key, item){
                if(item.value === ''){
                    swal("Wpisz maile do wszystkich pól");
                    validate = false;
                    return false;
                }else{
                    var re = /\S+@\S+\.\S+/;
                    if(! re.test(item.value)){
                        swal("Podane maile muszą być w odpowiednim formacie");
                        validate = false;
                        return false;
                    }
                }
            });

            $('input[name="hotelEmail"]').each(function(key, item){
                if(item.checked == true){
                    return false;
                }
                if(key == $('input[name="hotelEmail"]').length-1) {
                    swal("Zaznacz sugerowany email");
                    validate = false;
                }
            });
            if(validate) {
                saveHotelFlag = true;
                $('#saveHotel').prop('disabled', true);
                let phones = [];
                let emails = [];
                $('.mailsContainer .contact .input-group').each(function(key, item){
                    let radio = $(item).find('input[name="hotelEmail"]');
                    let email = $(item).find('.hotelEmail');
                    emails.push({id:email.data('id'), new: email.data('new'), value: email.val(), suggested: radio.prop('checked')});
                });
                $('.phonesContainer .contact .input-group').each(function(key, item){
                    let radio = $(item).find('input[name="hotelPhoneNumber"]');
                    let phone = $(item).find('.hotelPhoneNumber');
                    phones.push({id:phone.data('id'), new: phone.data('new'), value: phone.val(), suggested: radio.prop('checked')});
                });
                $.ajax({
                    type: "POST",
                    url: "{{route('api.saveNewHotel')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'voivode':      voivode,
                        'name':         name,
                        'price': 0,
                        'street':       street,
                        'city':         city,
                        'zipCode':      zipCode,
                        'hotelId':      hotelId,
                        'comment' :     comment,
                        'hotelStatus' : hotelStatus,
                        'dailyBid':     1,
                        'hourBid' :     1,
                        'paymentMethodId': paymentMethodId,
                        'parking' :     parking,
                        'bidType': 0,
                        'phones' : phones,
                        'emails' : emails,
                        'clientsExceptions' : $('#clientsExceptions').val()
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
                        $('#saveHotel').prop('disabled', false);
                        DEFFERED.reject();
                        return DEFFERED.promise();
                    }
                }).done(function (response) {
                    $('#HotelModal').modal('hide');
                    if (addNewHotelFlag) {
                        $.notify({
                            icon: 'glyphicon glyphicon-ok',
                            message: 'Dodano nowy hotel <strong>' + name + '</strong>'
                        }, {
                            type: "success"
                        });
                        addNewHotelFlag = false;
                    }
                    if (editHotelFlag) {
                        $.notify({
                            icon: 'glyphicon glyphicon-ok',
                            message: 'Edytowano hotel <strong>' + name + '</strong>'
                        }, {
                            type: "success"
                        });
                        editHotelFlag = false;
                    }
                    $('#saveHotel').prop('disabled', false);
                    DEFFERED.resolve();
                    return DEFFERED.promise();
                });
            }else{
                DEFFERED.reject();
            }
            return DEFFERED.promise();
        }


        document.addEventListener('DOMContentLoaded', function(event) {
            $('#NewHotelModal').on('click',function () {
                hotelStatus = 1;
                clearModal();
                $('#addPhoneNumberButton').closest('.row').before(createNewHotelContact('hotelPhoneNumber'));
                $('#addEmailButton').closest('.row').before(createNewHotelContact('hotelEmail'));
                addNewHotelFlag = true;
            });
            // $('#bidType').on('change',function () {
            //    if($(this).val() == 0){
            //        $('#hourBid').prop('disabled',true);
            //        $('#dailyBid').prop('disabled',true);
            //    }else if($(this).val() == 1){
            //        $('#hourBid').prop('disabled',false);
            //        $('#dailyBid').prop('disabled',true);
            //    }else{
            //        $('#hourBid').prop('disabled',true);
            //        $('#dailyBid').prop('disabled',false);
            //    }
            // });

            $('#HotelModal').on('hidden.bs.modal', function () {
                hotelStatus = 1;
                if(saveHotelFlag){
                    table.ajax.reload();
                    saveHotelFlag = false;
                }
            });
            let voivodeeId = [];
            let cityId = [];
            let zipCode = [];
            table = $('#datatable').DataTable({
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "drawCallback": function( settings ) {
                },
                "rowCallback": function (row, data, index) {
                    if (data.status == 0) {
                        $(row).addClass('colorTurnedOff');
                    }
                    if(typeof data.comment === typeof '' && data.comment !== '' && data.comment !== null){
                        $(row).addClass('colorComment');
                    }
                    return row;
                },"fnDrawCallback": function (settings) {
                    /**
                     * Edycja Hotelu
                     */
                    $('.button-edit-hotel').on('click', function () {
                        let hotel_id = $(this).data('id');
                        $.ajax({
                            type: "POST",
                            url: "{{ route('api.findHotel') }}", // do zamiany
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'hotelId': hotel_id
                            },
                            success: function (response) {
                                clearModal();

                                $('#HotelModal .modal-title').first().text('Edycja Hotelu');

                                let saveHotelButton = $('#HotelModal #saveHotel');
                                saveHotelButton.first().text('');
                                saveHotelButton.first().prop('class', 'btn btn-success form-control');
                                saveHotelButton.first().append($('<span class="glyphicon glyphicon-save"></span>'));
                                saveHotelButton.first().append(' Zapisz Hotel');

                                fillHotelInformations(response);

                                $('#HotelModal').modal('show');
                            }
                        });
                    });

                    $('.button-preview-hotel').click(function () {
                        let hotel_id = $(this).data('id');
                        $.ajax({
                            type: "POST",
                            url: "{{ route('api.findHotel') }}", // do zamiany
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'hotelId': hotel_id
                            },
                            success: function (response) {
                                clearModal(true);

                                $('#HotelModal .modal-title').first().text('Podgląd Hotelu');

                                fillHotelInformations(response,true);

                                $('#HotelModal').modal('show');
                            }
                        });
                    });

                    /**
                     * Zmiana statusu hotelu
                     */
                    $('.button-status-hotel').on('click',function () {
                        let thisButton = $(this);
                        let hotelId = thisButton.data('id');
                        hotelStatus = thisButton.data('status');
                        let nameOfAction = "";
                        if(hotelStatus == 0)
                            nameOfAction = "Tak, wyłącz Hotel";
                        else
                            nameOfAction = "Tak, włącz Hotel";
                        swal({
                            title: 'Jesteś pewien?',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: nameOfAction
                        }).then((result) => {
                            if (result.value) {
                                thisButton.prop('disabled',true);
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('api.changeStatusHotel') }}", // do zamiany
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        'hotelId'   : hotelId
                                    },
                                    success: function (response) {
                                        $.notify({
                                            icon: 'glyphicon glyphicon-ok',
                                            message: 'Status hotelu został zmieniony'
                                        }, {
                                            type: "info"
                                        });
                                        const thisRow = thisButton[0].parentElement.parentElement;
                                        if(hotelStatus == 0){

                                            thisButton.removeClass('btn-danger');
                                            thisButton.addClass('btn-success');
                                            thisButton.data('status', 1);
                                            thisButton.text('Włącz');
                                            thisButton.prepend("<span class='glyphicon glyphicon-off'></span> ");

                                            thisRow.classList.add('colorTurnedOff');
                                        }else {
                                            thisButton.removeClass('btn-success');
                                            thisButton.addClass('btn-danger');
                                            thisButton.data('status', 0);
                                            thisButton.text('Wyłącz');
                                            thisButton.prepend("<span class='glyphicon glyphicon-off'></span> ");
                                            thisRow.classList.remove('colorTurnedOff');
                                        }
                                        thisButton.prop('disabled', false);
                                    }
                                });
                            }})
                    });

                },
                "ajax": {
                    'url': "{{ route('api.showHotelsAjax') }}",
                    'type': 'POST',
                    'data': function (d) {
                        d.hotelId = '{{$hotelId}}';
                        d.voivode = voivodeeId;
                        d.zipCode = zipCode;
                        d.city = cityId;
                        d.status = [1,0]
                    },
                    'headers': {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                },"columns":[
                    {"data":function (data, type, dataToSet) {
                                return data.name;
                        },"name":"name","orderable": true
                    },
                    {"data": function(data, type, dataToSet) {
                            return data.voivodeName;
                        },"name":"voivodeName", "orderable": true
                    },
                    {"data": function(data, type, dataToSet) {
                            return data.cityName;
                        },"name":"cityName", "orderable": true
                    },
                    {"data":function (data, type, dataToSet) {
                            return data.street;
                        },"name":"street","orderable": true
                    },
                    {"data": function(data,type,dataToSet){
                        return data.zip_code;
                        },name:"zip_code"
                    },
                    {"data":function (data, type, dataToSet) {
                            let returnButton = "<button class='button-edit-hotel btn btn-info btn-block'  data-id=" + data.id + "><span class='glyphicon glyphicon-edit'></span> Edycja</button>";
                            if (data.status == 1)
                                returnButton += "<button class='button-status-hotel btn btn-danger btn-block' data-id=" + data.id + " data-status=0 ><span class='glyphicon glyphicon-off'></span> Wyłącz</button>";
                            else
                                returnButton += "<button class='button-status-hotel btn btn-success btn-block' data-id=" + data.id + " data-status=1 ><span class='glyphicon glyphicon-off'></span> Włącz</button>";
                            return returnButton;
                        },"orderable": false, "searchable": false
                    },
                    {"data" : function (data){
                            let spanButton = $(document.createElement('span')).addClass('glyphicon glyphicon-search');
                            let previewButton = $(document.createElement('button')).addClass('button-preview-hotel btn btn-default btn-block').attr('data-id', data.id).append(spanButton);
                            return previewButton.prop('outerHTML');
                        },"orderable": false, "searchable": false
                    }
                ]
            });

            $('#voivodeAdd').on('change',function (e) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('api.getCitiesNames') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "id": $(this).val()
                    },
                    success: function(response, status) {
                        let cityInput = document.getElementById('cityAdd');
                        clearContent(cityInput);
                        let zeroValueOption = document.createElement('option');
                        zeroValueOption.value = 0;
                        zeroValueOption.textContent = "Wybierz";
                        cityInput.appendChild(zeroValueOption);

                        for(var i = 0; i < response.length; i++) {
                            let optionC = document.createElement('option');
                            optionC.value = response[i].id;
                            optionC.textContent = response[i].name;
                            if(response[i].id == city){
                                optionC.selected = true;
                            }
                            cityInput.appendChild(optionC);
                        }
                        $(cityInput).selectpicker('refresh');
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
                        $('#saveHotel').prop('disabled', false);
                    }
                })
            });


            $('.zipCode').on('input', function(e){
                let $thisZipCode = $(e.target);
                let $zipCodes = $('.zipCode');
                let $stringValue = String($thisZipCode.val()).replace('-', '');
                let $value = parseInt($stringValue);
                if (isNaN($value)) {
                        $thisZipCode.val('');
                } else {
                        if ($zipCodes.index($thisZipCode) < 4) {
                            if (String($value).length === 1) {

                                $thisZipCode.val($value);
                                //focus and select next zipCode input
                                $index = $zipCodes.index($thisZipCode) + 1;
                                if ($index !== $zipCodes.length) {
                                    $($zipCodes.get($index)).focus();
                                }
                            } else {
                                count = 0;
                                for (; count < String($value).length; count++) {
                                    $($zipCodes.get(count)).val(String($value).charAt(count));
                                }
                                if (count < $zipCodes.length) {
                                    $($zipCodes.get(count)).focus();
                                    $($zipCodes.get(count)).select();
                                } else
                                    $thisZipCode.blur();
                            }
                        } else {
                            if (String($value).length === 1) {
                                $thisZipCode.val($value);
                            } else
                                $thisZipCode.val(String($value).charAt(0));
                        }
                }
            }).focus(function(e){
                $(e.target).select();
            }).keyup(function (e) {
                if(e.keyCode == 8 && $(e.target).val().length === 0){
                    let $zipCodes = $('.zipCode');
                    let $index = $zipCodes.index($(e.target)) - 1;
                    if ($index >= 0) {
                        $($zipCodes.get($index)).focus();
                        $($zipCodes.get($index)).select();
                    }
                }
            });

            $('#addPhoneNumberButton').click(function (e) {
                $(e.target).closest('.row').before(createNewHotelContact('hotelPhoneNumber'));
            });

            $('#addEmailButton').click(function (e) {
                $(e.target).closest('.row').before(createNewHotelContact('hotelEmail'));
            });

            $('#invoiceTemplate').change(function(e){
                let allowedExtensions = <?php echo $validHotelInvoiceTemplatesExtensions; ?>;
                if(allowedExtensions.indexOf(getFileExtension($(e.target).prop('files')[0].name)) === -1){
                    $(e.target).val('');
                    swal({
                        title: 'Zły format pliku',
                        text: 'Dostępne formaty: '+allowedExtensions.toString(),
                        type: 'warning'
                    });
                }
            });


            $('#newHotelForm').submit(function (e) {
                e.preventDefault();
                saveNewHotel().done(function(){
                    //let orderFileInput = $('#order');
                    let invoiceTemplateFileInput = $('#invoiceTemplate');
                    /*if(orderFileInput.prop("files").length !== 0){
                        formData.append(orderFileInput.prop('name'), orderFileInput.prop("files")[0]);
                        uploadFiles = true;
                    }*/
                    let formData = new FormData();
                    let uploadFiles = false;
                    if(invoiceTemplateFileInput.prop("files").length !== 0){
                        let fileNames = [];
                        formData.append(invoiceTemplateFileInput.prop('name'), invoiceTemplateFileInput.prop("files")[0]);
                        fileNames.push(invoiceTemplateFileInput.prop('name'));
                        uploadFiles = true;

                        formData.append('fileNames', JSON.stringify(fileNames));
                    }
                    if(uploadFiles)
                        uploadFilesAjax(formData);
                });
            });

            $('#voivode').select2();
            $('#city').select2();
            $('#zipCode').select2();

            $('#voivode').on('select2:select select2:unselect', function (e) {
                let voivodeInp = document.querySelector('#voivode');
                voivodeeId = $('#voivode').val();
                cityId = [];
                clearSelection('city');
                table.ajax.reload();
            });

            $('#city').on('select2:select select2:unselect', function (e) {
                let cityInp = document.querySelector('#city');
                cityId = $('#city').val();
                voivodeeId = [];
                clearSelection('voivode');
                table.ajax.reload();
            });

            $('#zipCode').on('select2:select select2:unselect', function (e) {
                zipCode = $('#zipCode').val();
                table.ajax.reload();
            });

            function downloadFilesAjax(hotel_id){
                swal({
                    title: 'Wysyłanie żądania pobrania pliku...',
                    text: 'To może chwilę potrwać',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    onOpen: () => {
                        swal.showLoading();
                        $.ajax({
                            type: "POST",
                            url: "{{route('api.downloadHotelFilesAjax')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {'hotel_id': hotel_id}
                        }).done(function (response){
                            swal.close();
                        }).error(function (jqXHR, textStatus, thrownError) {
                            swal.close();
                            console.log(jqXHR);
                            console.log('textStatus: ' + textStatus);
                            console.log('hrownError: ' + thrownError);
                            swal({
                                type: 'error',
                                title: 'Błąd ' + jqXHR.status,
                                text: 'Wystąpił błąd: ' + thrownError+' "'+jqXHR.responseJSON.message+'"',
                            });
                        });
                    }
                });
            }

            function uploadFilesAjax(formData, fileNames){
                swal({
                    title: 'Wysyłanie pliku...',
                    text: 'To może chwilę potrwać',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    onOpen: () => {
                        swal.showLoading();
                        $.ajax({
                            type: "POST",
                            url: "{{route('api.uploadHotelFilesAjax')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: false,
                            processData: false,
                            data: formData
                        }).done(function (response){
                            swal.close();
                            if(response === 'success' ){
                                $.notify({
                                    icon: 'glyphicon glyphicon-ok',
                                    message: 'Wysłano szablon faktury <strong>' + $("#name").val() + '</strong>'
                                }, {
                                    type: "success"
                                });
                            }else if(response === 'fail'){
                                $.notify({
                                    icon: 'glyphicon glyphicon-remove',
                                    message: 'Nie udało się wysłać szablonu faktury <strong>' + $("#name").val() + '</strong>'
                                }, {
                                    type: "danger"
                                });
                            }
                        }).error(function (jqXHR, textStatus, thrownError) {
                            swal.close();
                            console.log(jqXHR);
                            console.log('textStatus: ' + textStatus);
                            console.log('hrownError: ' + thrownError);
                            swal({
                                type: 'error',
                                title: 'Błąd ' + jqXHR.status,
                                text: 'Wystąpił błąd: ' + thrownError+' "'+jqXHR.responseJSON.message+'"',
                            });
                            $('#saveHotel').prop('disabled', false);
                        });
                    }
                    });
            }

            function fillHotelInformations(response, preview = false){
                editHotelFlag = true;

                let hotel = response.hotel;

                $('#hotelId').val(hotel.id);
                let invoiceTemplatePath = hotel.invoice_template_path;

                //create container with invoice template filename if exists
                if(invoiceTemplatePath !== ""){
                    let fileLabel = $(document.createElement('label')).text('Szablon faktury');
                    let fileSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-file');
                    //let buttonDownloadInvoice = $(document.createElement('button')).prop('type','button').addClass('btn btn-default').prop('id','downloadInvoiceTemplate').text(' Pobierz').prepend(fileSpan);
                    let buttonDownloadInvoice = $(document.createElement('a')).attr('href','/downloadHotelFiles/'+hotel.id).addClass('btn btn-default').prop('id','downloadInvoiceTemplate').text(' Pobierz').prepend(fileSpan);

                    let buttonSpanAddon = $(document.createElement('span')).addClass('input-group-btn').append(buttonDownloadInvoice);
                    let fileInput = $(document.createElement('input')).addClass('form-control').prop('type','text').prop('readonly', true).val(invoiceTemplatePath);
                    let inputGroup = $(document.createElement('div')).addClass('input-group').append(buttonSpanAddon).append(fileInput);
                    let fileColumn = $(document.createElement('div')).addClass('col-md-4 invoiceTemplateFile').append(fileLabel).append(inputGroup);
                    $('div.col-md-4').has('#invoiceTemplate').after(fileColumn);
                }

                $("#name").val(hotel.name);
                //$('#price').val(response.price);
                $('#street').val(hotel.street);
                hotelStatus = hotel.status;
                $('#voivodeAdd').val(hotel.voivode_id);
                $('#comment').val(hotel.comment);
                $('#paymentMethod').val(hotel.payment_method_id == null ? 0 : hotel.payment_method_id);
                $('#parking').val(hotel.parking == null ? -1 : hotel.parking);
                // $('#bidType').val(hotel.bidType).trigger('change');
                /*if(preview)
                    $('#bidType').prop('disabled',true);
                else
                    $('#bidType').prop('disabled',false);*/
                // $('#hourBid').val(hotel.hour_bid);
                // $('#dailyBid').val(hotel.daily_bid);

                $('#clientsExceptions').val(response.clientsExceptions);


                $('#paymentMethod').selectpicker('refresh');
                $('#parking').selectpicker('refresh');
                $('#clientsExceptions').selectpicker('refresh');

                zipCode = String(hotel.zip_code);
                if (zipCode != "null") {
                    length = zipCode.length;
                    for (var i = 0; i < 5 - length; i++) {
                        zipCode = "0".concat(zipCode);
                    }
                    let zipCodeInputs = $('.zipCode');
                    for (var i = 0; i < 5; i++)
                        $(zipCodeInputs.get(i)).val(zipCode.slice(i, i + 1));
                }
                else {
                    $('.zipCode').val('');
                }

                let contacts = response.contacts;
                let isPhoneNumber = false;
                let isEmail = false;
                $.each(contacts, function (index, contact) {
                    var contactData = {
                        id: contact.id,
                        value: contact.contact,
                        suggested: contact.suggested === 1
                    };
                    if (contact.type === 'mail') {
                        $('#addEmailButton').closest('.row').before(createNewHotelContact('hotelEmail', contactData, preview));
                        isEmail = true;
                    }
                    if (contact.type === 'phone') {
                        $('#addPhoneNumberButton').closest('.row').before(createNewHotelContact('hotelPhoneNumber', contactData, preview));
                        isPhoneNumber = true;
                    }
                });

                if (!isEmail && !preview)
                    $('#addEmailButton').closest('.row').before(createNewHotelContact('hotelEmail'));
                if (!isPhoneNumber && !preview)
                    $('#addPhoneNumberButton').closest('.row').before(createNewHotelContact('hotelPhoneNumber'));
                city = hotel.city_id;
                $('#voivodeAdd').trigger("change");
            }

            function clearSelection(element) {
                if(element == 'city') {
                    $('#city').val(null).trigger('change');
                }
                else {
                    $('#voivode').val(null).trigger('change');
                }

            }
        });

        function getFileExtension(fname) {
            return fname.slice((fname.lastIndexOf(".") - 1 >>> 0) + 2);
        }

        /**
         * Method creates container with radio box, input and removal button
         *
         * @param className - class name of generated inputs
         * @param data - data  example  { id:[val], value:[val], suggested:[val]}
         * @returns {*|jQuery} DOM element
         */
        function createNewHotelContact(className, data = null, preview = false){
            let placeHolder = '';
            if(className == 'hotelPhoneNumber')
                placeHolder = 'Wpisz numer telefonu';
            if(className == 'hotelEmail')
                placeHolder = 'Wpisz adres email';

            var inputs = $('.'+className);

            let radioInput = $(document.createElement('input')).prop('type','radio').prop('name',className);
            let radioSpan = $(document.createElement('span')).addClass('input-group-addon').append(radioInput);
            let newContactInput = $(document.createElement('input')).addClass('form-control').addClass(className).prop('placeholder', placeHolder);
            let inputGroup = $(document.createElement('div')).addClass('input-group').append(radioSpan).append(newContactInput);

            let newContactColumn = $(document.createElement('div')).addClass('col-md-10').append(inputGroup);

            let newContainer = $(document.createElement('div')).addClass('row contact').css('margin-top','1em').append(newContactColumn);
            if(inputs.length === 0)
                radioInput.prop('checked', true);
            else if(!preview){
                let span = $(document.createElement('span')).addClass('glyphicon glyphicon-minus');
                let button = $(document.createElement('button')).addClass('btn btn-danger btn-block').prop('type','button').append(span).click(removeHotelContact);
                let buttonColumn = $(document.createElement('div')).addClass('col-md-2').append(button);
                newContainer.append(buttonColumn);
            }

            if(data !== null){
                radioInput.data('id', data.id);
                radioInput.prop('checked', data.suggested);
                radioInput.prop('readonly', preview);
                radioInput.attr('data-new', false);
                newContactInput.data('id', data.id);
                newContactInput.val(data.value);
                newContactInput.attr('data-new', false);
                newContactInput.prop('readonly', preview);

                if(preview && className == 'hotelEmail'){
                    let inputView = $(document.createElement('input')).attr('type','hidden').attr('name','view').val('cm');
                    let inputFs = $(document.createElement('input')).attr('type','hidden').attr('name','fs').val('1');
                    let inputTo = $(document.createElement('input')).attr('type','hidden').attr('name','to').val(data.value);
                    let span = $(document.createElement('span')).addClass('glyphicon glyphicon-envelope');
                    let button = $(document.createElement('button')).addClass('btn btn-info btn-block').prop('type','submit').append(span);
                    let form = $(document.createElement('form')).attr('action','https://mail.google.com/mail').attr('target','_blank').attr('method','get')
                        .append(inputView).append(inputFs).append(inputTo).append(button);
                    //let link = $(document.createElement('a')).attr('href','https://mail.google.com/mail/?view=cm&fs=1&to='+data.value).append(button);
                    let buttonColumn = $(document.createElement('div')).addClass('col-md-2').append(form);
                    newContainer.append(buttonColumn);
                }

            }else{
                radioInput.data('id', 'new_'+(inputs.length+1));
                radioInput.attr('data-new', true);
                newContactInput.data('id', 'new_'+(inputs.length+1));
                newContactInput.attr('data-new', true);
            }

            return newContainer;
        }

        /**
         * Function remove contact inputs after alert confirmation
         */
        function removeHotelContact(){
            swal({
                title: 'Czy na pewno?',
                text: "Wybrany kontakt zostanie usunięty",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Tak, usuń!'
            }).then((result) => {
                if (result.value) {
                    $(this).closest('.row').remove();
                }
            });
        }
    </script>
@endsection
