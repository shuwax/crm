{{--/*--}}
{{--*@category: CRM --}}
{{--*@info: This view shows invoices of selected or all campaigns--}}
{{--*@controller: CrmRouteController --}}
{{--*@methods: getCampaignsInvoices, getCampaignsInvoicesDatatableAjax, uploadCampaignInvoiceAjax, getHotelContacts --}}
{{--*/--}}

@extends('layouts.main')
@section('style')
@endsection
@section('content')
    <style>
        .dropdown-menu{
            left: 0px;
        }
    </style>
    <div class="page-header">
        <div class="alert gray-nav ">Lista faktur @if($routeId != 0 ) @if($client != null)
                - {{$client->route_name}}@endif @endif</div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Faktury poszczególnych kampanii
        </div>
        <div class="panel-body">
            @if($routeId == 0)
                <div class="row">
                    <div class="col-md-5">
                        <label class="">Klilent</label>
                        <select id="clientSelect" class="selectpicker form-control">
                            <option value="0">Wybierz</option>
                            @if(isset($clients) and $clients !== null)
                                @foreach($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="">Status</label>
                        <select id="invoiceStatusSelect" class="selectpicker form-control">
                            <option value="0">Wybierz</option>
                            @if(isset($invoiceStatuses) and $invoiceStatuses !== null)
                                @foreach($invoiceStatuses as $invoiceStatus)
                                    <option value="{{$invoiceStatus->id}}">{{$invoiceStatus->name_pl}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="">Od</label>
                        <input id="firstDateInputFilter" type="date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="">Do</label>
                        <input id="lastDateInputFilter" type="date" class="form-control">
                    </div>
                </div>
            @endif
            <div class="row" style="margin-top: 1em">
                <div class="col-md-12">
                    <table id="invoicesDatatable" class="thead-inverse table table-striped row-border">
                        <thead>
                        <tr>
                            <th>Klient</th>
                            <th>Trasa</th>
                            <th>Hotel</th>
                            <th>Data pokazu</th>
                            <th>Faktura</th>
                            <th>Status</th>
                            <th>Kara</th>
                            <th>Data wysłania faktury</th>
                            <th>Data opłacenia faktury</th>
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
@endsection

@section('script')
    <script>
        $(document).ready(() => {
            let clientSelect = $('#clientSelect');
            let invoiceStatusSelect = $('#invoiceStatusSelect');
            let firstDateInputFilter = $('#firstDateInputFilter');
            let lastDateInputFilter = $('#lastDateInputFilter');
            let actualCampaignID = 0;
            @if(isset($routeId))
                @if($routeId == 0)
                    let firstDate = new Date('{{$firstDate}}');
                    let lastDate = new Date('{{$lastDate}}');
                    if(firstDate>lastDate)
                        lastDate=firstDate;
                    firstDateInputFilter.val(getFormatedDate(firstDate));
                    lastDateInputFilter.val(getFormatedDate(lastDate));
                @endif
            @endif
            var invoiceDatatable = $('#invoicesDatatable').DataTable({
                    width: '100%',
                autoWidth: true,
                processing: true,
                serverSide: true,
                scrollCollapse: true,
                scrollY: '45vh',
                scrollX: true,
                ajax: {
                    url: "{{route('api.getCampaignsInvoicesDatatableAjax')}}",
                    type: 'POST',
                    data: function (data) {
                        data.routeId = "{{$routeId}}";
                        data.clientId = clientSelect.val();
                        data.invoiceStatusId = invoiceStatusSelect.val();
                        data.firstDateInputFilter = firstDateInputFilter.val();
                        data.lastDateInputFilter = lastDateInputFilter.val();
                    },
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Polish.json"
                },
                rowCallback: function( row, data ) {
                    if(data.penalty < 0){
                        $(row).css('background','#c500002e');
                    }
                },
                columns: [
                    {
                        data: function (data, type, val) {
                            return data.client_name
                        }, name: 'client_name'
                    },
                    {
                        data: function (data, type, val) {
                            return data.route_name
                        }, name: 'route_name'
                    },
                    {
                        data: function (data, type, val) {
                            return data.hotel_name
                        }, name: 'hotel_name'
                    },
                    {
                        data: function (data, type, val) {
                            return data.date
                        }, name: 'date'
                    },
                    {
                        data: function (data, type, val) {
                            let invoiceSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-cloud-download');
                            let invoiceButton = $(document.createElement('button')).addClass('btn btn-primary btn-block').append(invoiceSpan);
                            let form = $(document.createElement('form')).attr('action','/downloadCampaignInvoicePDF/'+data.id).attr('method','get').append(invoiceButton);
                            if (data.invoice_path === null || data.invoice_path === '') {
                                invoiceButton.prop('disabled', true);
                            }
                            return form.prop('outerHTML');
                        }, name: 'invoice'
                    },
                    {
                        data: function (data, type, val) {
                            let divStatus = $(document.createElement('div')).text(data.name_pl).css('text-align','center');
                            let color = '';
                            if (data.invoice_status_id == 1) {
                                color = '#d9534f';
                                divStatus.css('color', 'white');
                            }
                            if (data.invoice_status_id == 2) {
                                color = '#f0ad4e';
                                divStatus.css('color', 'white');
                            }
                            if (data.invoice_status_id == 3) {
                                color = '#5bc0de';
                                divStatus.css('color', 'white');
                            }
                            if (data.invoice_status_id == 4) {
                                color = '#5cb85c';
                                divStatus.css('color', 'white');
                            }
                            divStatus.css('background-color', color);
                            divStatus.css('padding', '10px 3px');
                            divStatus.css('border-radius', '10px');
                            divStatus.css('font-weight', 'bold');

                            return divStatus.prop('outerHTML');
                        }, name: 'invoice_status_id'
                    },
                    {
                        data: function (data, type, val) {
                            let divPenalty = $(document.createElement('div')).text(data.penalty + ' zł');
                            if(data.penalty > 0 ){
                                divPenalty.css('color','red');
                                divPenalty.css('font-weight', 'bold');
                            }
                            return divPenalty.prop('outerHTML');
                        }, name: 'penalty',className:'penaltyCoast'
                    },
                    {
                        data: function (data, type, val) {
                            return data.invoice_send_date
                        }, name: 'invoice_send_date'
                    },
                    {
                        data: function (data, type, val) {
                            return data.invoice_payment_date
                        }, name: 'invoice_payment_date'
                    },
                    {
                        data: function (data, type, val) {

                            let actionSpan = $(document.createElement('span'));
                            let actionButton = $(document.createElement('button'))
                                .attr('data-campaign_id', data.id)
                                .attr('data-client_id', data.client_id)
                                .attr('data-client_name', data.client_name)
                                .attr('data-hotel_name',data.hotel_name)
                                .prop('type', 'button').addClass('btn btn-block');//.attr('data-toggle', 'modal').attr('data-target', '#myModal');
                            actionButton.addClass('btn-info');

                            let buttonGroup = $(document.createElement('div'));
                            if (data.invoice_status_id == 1) {
                                //actionButton.addClass('btn-danger');
                                actionButton.addClass('uploadInvoice');
                                actionButton.text(' Wrzuć fakturę');
                                actionSpan.addClass('glyphicon glyphicon-cloud-upload');
                            }
                            if (data.invoice_status_id == 2) {
                                //actionButton.addClass('btn-warning');
                                let actionSpan2 = $(document.createElement('span'));
                                let actionButton2 = $(document.createElement('button'))
                                    .attr('data-campaign_id', data.id)
                                    .attr('data-client_id', data.client_id)
                                    .attr('data-client_name', data.client_name)
                                    .attr('data-hotel_name',data.hotel_name)
                                    .prop('type', 'button').addClass('btn btn-block btn-info');
                                actionButton2.addClass('uploadInvoice');
                                actionButton2.text(' Wrzuć fakturę');
                                actionSpan2.addClass('glyphicon glyphicon-cloud-upload');
                                actionButton2.prepend(actionSpan2);
                                buttonGroup.append(actionButton2);

                                actionButton.addClass('sendInvoice');
                                actionButton.text(' Wyślij fakturę');
                                actionSpan.addClass('glyphicon glyphicon-envelope');
                            }
                            if (data.invoice_status_id == 3) {
                               // actionButton.addClass('btn-success');
                                actionButton.addClass('confirmPayment');
                                actionSpan.addClass('glyphicon glyphicon-unchecked');
                                actionButton.text(' Potwierdź zapłatę');
                            }
                            if (data.invoice_status_id == 4) {
                                return '';
                               // actionButton.addClass('btn-default');
                                //actionButton.attr('id', 'noAction');
                                //actionSpan.addClass('glyphicon glyphicon-check');
                                //actionButton.text('Opłacone');
                                //actionButton.prop('disabled', true);
                            }
                            actionButton.prepend(actionSpan);
                            return buttonGroup.append(actionButton).prop('outerHTML');
                        }, orderable: false, searchable: false, name: 'action'
                    }
                ],
                    fnDrawCallback: function () {

                    //handle uploadInvoiceButton
                        $('.uploadInvoice').click(function (e) {
                            let modalContent = $('#' + modalIdString + ' .modal-content');
                            setModalSize(modalIdString, 2);
                            clearModalContent(modalContent);

                            //--------- create modal content -------------//
                            //--------- modal header ------------//
                            let modalTitle = $(document.createElement('h4')).addClass('modal-titlel').text('Wrzucanie faktury na serwer');
                            let modalHeader = $(document.createElement('div')).addClass('modal-header').append(modalTitle);
                            //--------- modal body ------------------//
                            let inputLabel = $(document.createElement('label')).text('Wybierz fakturę');
                            let input = $(document.createElement('input')).addClass('form-control').prop('type', 'file').attr('name', 'campaign_invoice').attr('id', 'invoiceInput').css('padding-bottom', '3em');
                            let inputColumn = $(document.createElement('div')).addClass('col-md-12').append(inputLabel).append(input);
                            let row1 = $(document.createElement('div')).addClass('row').append(inputColumn);

                            let uploadSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-cloud-upload');
                            let uploadButton = $(document.createElement('button')).addClass('btn btn-block btn-info')
                                .click(function () {
                                    handleUploadInvoiceButtonClick($(e.target).data('campaign_id'));
                                })
                                .prop('type', 'button').text(' Wrzuć fakturę').prepend(uploadSpan);
                            let uploadColumn = $(document.createElement('div')).addClass('col-md-12').append(uploadButton);
                            let buttonRow = $(document.createElement('div')).addClass('row').append(uploadColumn).css('margin-top', '1em');
                            let modalBody = $(document.createElement('div')).addClass('modal-body')
                                .append(getCampaignInfoRow(e))
                                .append(row1).append(buttonRow);
                            modalContent.prepend(modalBody).prepend( modalHeader);
                            //--------- end creating modal content -------------//

                            //handle file input
                            input.change(function (e) {
                                //on change check file extensions if is valid
                                let allowedExtensions = <?php echo $validCampaignInvoiceExtensions ?>;
                                if (allowedExtensions.indexOf(getFileExtension($(e.target).prop('files')[0].name)) === -1) {
                                    $(e.target).val('');
                                    swal({
                                        title: 'Zły format pliku',
                                        text: 'Dostępne formaty: ' + allowedExtensions.toString(),
                                        type: 'warning'
                                    });
                                }
                            });

                            myModal.modal('show');
                        });
                        //set zero actualCampaignID
                        $('#myModal').on('hidden.bs.modal',function(){
                            actualCampaignID = 0;
                        });
                        //handle sendInvoiceButton
                        $('.sendInvoice').click(function (e) {
                            let hotelId = $(e.target).data('client_id');
                            actualCampaignID =  $(e.target).data('campaign_id');
                            swal({
                                title: 'Ładowawnie...',
                                text: 'To może chwilę zająć',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                allowEnterKey: false,
                                onOpen: () => {
                                    swal.showLoading();
                                    getHotelInfoAjax(hotelId, function (data) {
                                        let modalContent = $('#' + modalIdString + ' .modal-content');
                                        setModalSize(modalIdString, 3);
                                        clearModalContent(modalContent);

                                        //--------- create modal content -------------//
                                        //--------- modal header ------------//
                                        let modalTitle = $(document.createElement('h4')).addClass('modal-titlel').text('Wysyłanie faktury mailem do klienta');
                                        let modalHeader = $(document.createElement('div')).addClass('modal-header').append(modalTitle);
                                        //--------- modal body ------------------//
                                        let labelSpan = $(document.createElement('span')).addClass('input-group-addon').text('Do');
                                        let contactsSelect = $(document.createElement('select')).addClass('selectpicker form-control').attr('id','emailSelect').prop('multiple', true).prop('title','Wybierz adresy mailowe');
                                        let inputGroup = $(document.createElement('div')).addClass('input-group').append(labelSpan).append(contactsSelect);
                                        let contactsColumn = $(document.createElement('div')).addClass('col-md-12').append(inputGroup);
                                        let row2 = $(document.createElement('div')).addClass('row').append(contactsColumn);

                                        let sendSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-envelope');
                                        let sendButton = $(document.createElement('button')).addClass('btn btn-block btn-info')
                                            .click(function () {
                                                handleSendInvoiceButtonClick(e);
                                            })
                                            .prop('type', 'button').text(' Wyślij fakturę').prepend(sendSpan);
                                        let sendColumn = $(document.createElement('div')).addClass('col-md-12').append(sendButton);
                                        let buttonRow = $(document.createElement('div')).addClass('row').append(sendColumn).css('margin-top', '1em');

                                        if((data.payment_mail  == null || data.payment_mail == '') && (data.manager_mail  == null || data.manager_mail == '')){
                                            let option = $(document.createElement('option')).val('0').text('Brak adresu email').prop('disabled',true);
                                            contactsSelect.append(option);
                                            contactsSelect.selectpicker('refresh');
                                            sendButton.prop('disabled',true);
                                        }else{
                                            if(!(data.payment_mail  == null || data.payment_mail == '')) {
                                                let option = $(document.createElement('option')).val(data.payment_mail).text(data.payment_mail);
                                                option.attr('data-subtext', 'Mail płatności');
                                                contactsSelect.append(option);
                                                contactsSelect.val(data.payment_mail);
                                            }
                                            if(!(data.payment_mail  == null || data.payment_mail == '')) {
                                                let option = $(document.createElement('option')).val(data.manager_mail).text(data.manager_mail);
                                                option.attr('data-subtext', 'Mail szefa');
                                                contactsSelect.append(option);
                                            }
                                            contactsSelect.selectpicker('refresh');
                                        }


                                        let messageTitleInput = $(document.createElement('input')).attr('id','messageTitleInput').addClass('form-control').attr('type','text').attr('placeholder','Tytuł wiadomości');
                                        let messageTitleColumn = $(document.createElement('div')).addClass('col-md-6').append(messageTitleInput);
                                        let row3 = $(document.createElement('div')).addClass('row').append(messageTitleColumn).css('margin-top','1em');

                                let messageInput = $(document.createElement('textarea')).attr('id','messageTitlemailSelecteInput').addClass('form-control').attr('placeholder','Treść wiadomości')
                                    .css({
                                        'resize':'none',
                                        'height':'45vh'
                                    });
                                let messageColumn = $(document.createElement('div')).addClass('col-md-12').append(messageInput);
                                let row4 = $(document.createElement('div')).addClass('row').append(messageColumn).css('margin-top','1em');

                                        let inovoiceColumn = $(document.createElement('div')).addClass('col-md-12').text('');
                                        let row5 = $(document.createElement('div')).addClass('row').append(inovoiceColumn).css('margin-top','1em');

                                        let modalBody = $(document.createElement('div')).addClass('modal-body')
                                            .append(getCampaignInfoRow(e))
                                            .append(row2).append(row3).append(row4).append(row5)
                                            .append(buttonRow);
                                        modalContent.prepend(modalBody).prepend(modalHeader);
                                        //--------- end creating modal content -------------//

                                        myModal.modal('show');
                                    }).then(function () {
                                        swal.close();
                                    });
                                }
                            });
                        });

                        $('.confirmPayment').click(function (e) {
                            let modalContent = $('#' + modalIdString + ' .modal-content');
                            let campaignID =  $(e.target).data('campaign_id');
                            setModalSize(modalIdString, 2);
                            clearModalContent(modalContent);

                            //--------- create modal content -------------//
                            //--------- modal header ------------//
                            let modalTitle = $(document.createElement('h4')).addClass('modal-titlel').text('Potwierdzenie opłacenia faktury');
                            let modalHeader = $(document.createElement('div')).addClass('modal-header').append(modalTitle);
                            //--------- modal body ------------------//

                            let dateTimeLabel = $(document.createElement('label')).text('Wybierz datę, godzinę i minutę zapłaty');
                            let calendarSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-calendar');
                            let inputGroupAddonSpan = $(document.createElement('span')).addClass('input-group-addon').append(calendarSpan);
                            let input = $(document.createElement('input')).addClass('form-control').attr('id','datetimepicker').attr('type','text');
                            let inputGroupDate = $(document.createElement('div')).addClass('input-group date').append(input).append(inputGroupAddonSpan);
                            let formGroup = $(document.createElement('div')).addClass('form-group').append(inputGroupDate);
                            let dateTimeColumn = $(document.createElement('div')).addClass('col-md-12').append(dateTimeLabel).append(formGroup);
                            let row1 = $(document.createElement('div')).addClass('row').append(dateTimeColumn).css('margin-top','1em');
                            let penalty = $(this).closest('tr').find('.penaltyCoast').text().split(" ");
                            penalty = parseInt(penalty[0]);
                            let row2 = '';
                            if(penalty > 0){
                                let penaltyLabel = $(document.createElement('label')).text('Naliczono karę na kworę');
                                let penaltyInput = $(document.createElement('input')).addClass('form-control').attr('id','penaltyInput').attr('type','number').attr('value',penalty);
                                let inputPenaltyGroup = $(document.createElement('div')).addClass('col-md-12').append(penaltyLabel).append(penaltyInput);
                                 row2 = $(document.createElement('div')).addClass('row').append(inputPenaltyGroup).css('margin-top','1em');
                            }

                            let confirmSpan = $(document.createElement('span')).addClass('glyphicon glyphicon-check');
                            let confirmButton = $(document.createElement('button')).addClass('btn btn-block btn-info')
                                .click(function () {
                                    handleConfirmPaymentButtonClick(e);
                                })
                                .prop('type', 'button').text(' Potwierdź zapłatę').prepend(confirmSpan);
                            let sendColumn = $(document.createElement('div')).addClass('col-md-12').append(confirmButton);
                            let buttonRow = $(document.createElement('div')).addClass('row').append(sendColumn).css('margin-top', '1em');
                            let modalBody = $(document.createElement('div')).addClass('modal-body')
                                .append(getCampaignInfoRow(e))
                                .append(row1)
                                .append(row2)
                                .append(buttonRow);
                            modalContent.prepend(modalBody).prepend(modalHeader);
                            //--------- end creating modal content -------------//

                            myModal.modal('show');

                            input.datetimepicker({
                                inline: true,
                                sideBySide: true,
                                locale: 'pl'
                            });
                            input.val('{{ date('Y-m-d G:i') }}');
                        });
                    }
            });

            function getCampaignInfoRow(e){
                let clientName = $(document.createElement('h3')).text($(e.target).data('client_name'));
                let hotelName = $(document.createElement('h4')).text($(e.target).data('hotel_name'));
                let campaignInfoColumn = $(document.createElement('div')).addClass('col-md-12').append(clientName).append(hotelName);
                return $(document.createElement('div')).addClass('row').append(campaignInfoColumn);
            }
            clientSelect.change(function () {
                invoiceDatatable.ajax.reload();
            });
            invoiceStatusSelect.change(function () {
                invoiceDatatable.ajax.reload();
            });
            firstDateInputFilter.change(function () {
                invoiceDatatable.ajax.reload();
            });
            lastDateInputFilter.change(function () {
                invoiceDatatable.ajax.reload();
            });

            //method gets value of file input to upload file to server
            function handleUploadInvoiceButtonClick(campaignId) {
                let invoiceInput = $('#invoiceInput').val();
                let valid = true;
                if (invoiceInput === '' || invoiceInput === null) {
                    swal('Wybierz fakturę z dysku');
                    valid = false;
                }
                if (valid) {
                    swal({
                        title: 'Czy na pewno?',
                        text: 'Czy chcesz wysłać wybraną fakturę na serwer?',
                        confirmButtonText: 'Tak, wyślij!',
                        showCancelButton: true,
                        type: 'warning'
                    }).then((result) => {
                        if (result.value) {
                            myModal.modal('hide');
                            let invoiceFileInput = $('#invoiceInput');
                            let formData = new FormData();
                            let uploadFiles = false;

                            //if file input has chosen files
                            if (invoiceFileInput.prop("files").length !== 0) {
                                let fileNames = [];
                                formData.append(invoiceFileInput.prop('name'), invoiceFileInput.prop("files")[0]);
                                fileNames.push(invoiceFileInput.prop('name'));
                                uploadFiles = true;

                                formData.append('fileNames', JSON.stringify(fileNames));
                                formData.append('campaignId', campaignId.toString());
                            }
                            if (uploadFiles)
                                uploadFilesAjax(formData);
                                invoiceDatatable.ajax.reload();
                        }
                    });
                }
            }

            function handleSendInvoiceButtonClick(e) {
                let selectedEmails = $('#emailSelect').val();
                let messageTitle = $('#messageTitleInput').val();
                let messageBody = $('#messageTitlemailSelecteInput').val();
                if(selectedEmails == null){
                    swal('Wybierz adresy mailowe');
                }else{
                    swal({
                        title: 'Czy na pewno?',
                        text: 'Czy chcesz wysłać wiadomość z załączoną fakturą na podane adresy?',
                        confirmButtonText: 'Tak, wyślij!',
                        showCancelButton: true,
                        type: 'warning'
                    }).then((result) => {

                        $.ajax({
                           type: 'POST',
                           url: "{{ route('api.sendMailWithInvoice') }}",
                           headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                           data:{
                               selectedEmails: selectedEmails,
                               messageTitle: messageTitle,
                               messageBody: messageBody,
                               actualCampaignID: actualCampaignID
                           },
                           success: function (response) {
                               invoiceDatatable.ajax.reload();
                               myModal.modal('hide');
                           } 
                        });

                    });
                }
            }


            function handleConfirmPaymentButtonClick(e) {
                let dateTime = $('#datetimepicker').val();
                let penalty = $('#penaltyInput').val();
                if(penalty == undefined)
                    penalty = 0;
                if(dateTime==""){
                    swal('Podaj czas opłacenia faktury');
                }else{
                    let button = $(e.target);
                    let dateTime = $('#datetimepicker').val();
                    swal({
                        title: 'Czy na pewno?',
                        text: 'Czy chcesz potwierdzić opłacenie faktury w '+dateTime +' przez '+ $(e.target).data('hotel_name') +'?',
                        confirmButtonText: 'Tak, potwierdź!',
                        showCancelButton: true,
                        type: 'warning'
                    }).then((result) => {
                        confirmPaymentAjax(button.data('campaign_id'), dateTime,penalty)
                            .then(function (result) {
                                myModal.modal('hide');
                                if(result === 'success'){
                                    invoiceDatatable.ajax.reload();
                                }else if(result === 'error'){
                                    swal('Błąd','Coś poszło nie tak','error');
                                }
                            });
                    });
                }
            }

            let myModal = createModal(modalIdString);
            $('.panel.panel-default').append(myModal);

            resizeDatatablesOnMenuToggle([invoiceDatatable]);
        });

        let modalIdString = 'myModal';

        function createModal(modalIdString) {
            let modalHeader = $(document.createElement('div')).addClass('modal-header');
            let modaBody = $(document.createElement('div')).addClass('modal-body');
            let modalContent = $(document.createElement('div')).addClass('modal-content');
            clearModalContent(modalContent);
            modalContent.prepend(modaBody).prepend(modalHeader);
            let modalDialog = $(document.createElement('div')).addClass('modal-dialog').append(modalContent);
            return $(document.createElement('div')).addClass('modal fade').attr('id', modalIdString).attr('role', 'dialog').append(modalDialog);
        }

        // sets modal size. if 1 - small, 2 - large
        function setModalSize(modalIdString, modalSize) {
            let modalDialog = $('#'+modalIdString+' .modal-dialog').removeClass().addClass('modal-dialog');
            if(modalSize === 1){
                modalDialog.addClass('modal-sm');
            }else if(modalSize === 2){
                modalDialog.addClass('modal-md');
            }else if(modalSize === 3){
                modalDialog.addClass('modal-lg');
            }
        }

        function clearModalContent(modalContent) {
            let modalCloseButton = $(document.createElement('button')).addClass('btn btn-default').prop('type', 'button').attr('data-dismiss', 'modal').text('Zamknij');
            let modalFooter = $(document.createElement('div')).addClass('modal-footer').append(modalCloseButton);
            modalContent.text('').append(modalFooter);
        }

        function getFileExtension(fname) {
            return fname.slice((fname.lastIndexOf(".") - 1 >>> 0) + 2);
        }

        function uploadFilesAjax(formData) {
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
                        url: "{{route('api.uploadCampaignInvoiceAjax')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        contentType: false,
                        processData: false,
                        data: formData
                    }).done(function (response) {
                        swal.close();
                        if (response === 'success') {
                            $.notify({
                                icon: 'glyphicon glyphicon-ok',
                                message: 'Wysłano fakturę na serwer'
                            }, {
                                type: "success"
                            });
                        } else if (response === 'fail') {
                            $.notify({
                                icon: 'glyphicon glyphicon-remove',
                                message: 'Nie udało się wysłać faktury na serwer'
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
                            text: 'Wystąpił błąd: ' + thrownError + ' "' + jqXHR.responseJSON.message + '"',
                        });
                    });
                }
            });
        }

        function getHotelInfoAjax(clientId, callback){
            return $.ajax({
                type: "POST",
                url: "{{route('api.getClientInfoAjax')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    clientId: clientId
                }
            }).done((response)=>{
                callback(response);
            }).error(function (jqXHR, textStatus, thrownError) {
                swal.close();
                console.log(jqXHR);
                console.log('textStatus: ' + textStatus);
                console.log('hrownError: ' + thrownError);
                swal({
                    type: 'error',
                    title: 'Błąd ' + jqXHR.status,
                    text: 'Wystąpił błąd: ' + thrownError + ' "' + jqXHR.responseJSON.message + '"',
                });
            });
        }

        function confirmPaymentAjax(campaignId, dateTime, penalty){
            return $.ajax({
                type: "POST",
                url: "{{route('api.confirmPaymentAjax')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    campaignId: campaignId,
                    dateTime: dateTime,
                    penalty : penalty
                }
            }).done((response)=>{
                return response;
            }).error(function (jqXHR, textStatus, thrownError) {
                swal.close();
                console.log(jqXHR);
                console.log('textStatus: ' + textStatus);
                console.log('hrownError: ' + thrownError);
                swal({
                    type: 'error',
                    title: 'Błąd ' + jqXHR.status,
                    text: 'Wystąpił błąd: ' + thrownError + ' "' + jqXHR.responseJSON.message + '"',
                });
            });
        }


        function  getFormatedDate(date) {
            let day = date.getDate();
            let month = date.getMonth()+1;
            let dateString = date.getFullYear()+'-';
            if(month < 10){
                dateString += '0'
            }
            dateString += month+'-';
            if(day < 10){
                dateString += '0'
            }
            dateString += day;
            return dateString;
        }
    </script>
@endsection
