@extends('layouts.main')
@section('content')
    {{--**********************************************************************************--}}
    {{--THIS PAGE SHOWS AVAILABLE TEMPLATES FOR AUDITS && ALLOWS TO ADD OR REMOVE TEMPLATE--}}
    {{--**********************************************************************************--}}
    <style>
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
            <div class="alert alert-danger">Przed usunięciem szablonu należy usunąć wszystkie nagłówki i kryteria z nim powiązane!</div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Nazwa szablonu</th>
                    <th>Usuń</th>
                    <th>Podgląd</th>
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $temp)
                    @if($temp->id != 0 && $temp->isActive == 1)
                    <tr>
                        <td>{{$temp->name}}</td>
                        <td><span style="font-size:2em;color:red;" class="glyphicon glyphicon-remove gl" data-tempid="{{$temp->id}}"></span></td>
                        <td><a href="{{URL::to("editAudit")}}/{{$temp->id}}">Podgląd</a></td>
                    </tr>
                    @endif
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <form action="{{URL::to('/addTemplate')}}" method="POST" id="formularz">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="button" value="Dodaj szablon audytu" class="btn btn-info" id="templateAddButton">
                <div class="form-group">
                    <label for="templateName">Podaj nazwę nowego szablonu</label>
                    <input type="text" name="templateName" id="templateName" class="form-control">
                </div>
                <div class="form-group">
                    <input type="submit" value="Dodaj" id="submitButton" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        var inputContainer = document.getElementsByClassName('form-group')[0];
        var templateNameInput = document.getElementById('templateName');
        var templateSubmitButton = document.getElementById('submitButton');
        var templateAddButton = document.getElementById('templateAddButton');
        var removeIcons = Array.from(document.getElementsByClassName('gl'));
        var templateForm = document.getElementById('formularz');

        //Hide input and submit button
        inputContainer.classList.add('inactive');
        templateSubmitButton.classList.add('inactive');

        //Show input
        function templateAddButtonHandler(e) {
            inputContainer.classList.remove('inactive');
        }

        //Function responsible for submit
        function templateSubmitButtonHandler(e) {
            e.preventDefault();
            if(templateNameInput.value != '' || templateNameInput.value != null) {
                templateForm.submit();
            }
        }

        // Show/hide submit button
        function templateNameInputHandler(e) {
            if(templateNameInput.value == '' || templateNameInput.value == null) {
                templateSubmitButton.classList.add('inactive');
            }
            else {
                templateSubmitButton.classList.remove('inactive');
            }
        }

        //Function responsible for removing template
        function iconClickHandler(e) {
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
                    $('#submitButton').after('<input type="hidden" value="true" name="isAdding">');
                    $('#submitButton').after('<input type="hidden" value="' + e.target.dataset.tempid + '" name="idToDelete">');
                    templateForm.submit();
                swal(
                    'Usunięte!',
                    'Szablon został usunięty',
                    'success'
                )
            }
            });
        }

        //Event Listeners
        templateAddButton.addEventListener('click', templateAddButtonHandler);
        templateNameInput.addEventListener('input', templateNameInputHandler);
        templateSubmitButton.addEventListener('click', templateSubmitButtonHandler);
        removeIcons.forEach(glyphicon => glyphicon.addEventListener('click', iconClickHandler));

    });
</script>
@endsection