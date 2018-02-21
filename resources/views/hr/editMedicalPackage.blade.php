<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <button class="btn btn-info text-center" id="edit_medical_package" style="width: 100%">
                <span id="span_edit_medical" class="glyphicon glyphicon-plus"></span> <span>Edytuj pakiet medyczny</span>
            </button>
        </div>
    </div>
</div>
<div id="edit_medical_data" style="display: none;">
    <input type="hidden" id="medical_package_active" name="medical_package_active" value="0"/>
    <input type="hidden" id="totalMemberSum" name="totalMemberSum" value="0"/>
    <input type="hidden" name="medical_package_is_edited" value="1"/>
    @foreach($user->medicalPackages->where('deleted', '=', 0) as $package)
        @if($package->family_member == null)
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pakiet:</label>
                        <select class="form-control" name="package_name" id="package_name">
                            <option>Wybierz</option>
                            <option @if($package->package_name == 'STANDARD') selected @endif>STANDARD</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Wariant:</label>
                        <select class="form-control" name="package_variable" id="package_variable">
                            <option>Wybierz</option>
                            <option @if($package->package_variable == 'INDYWIDUALNY') selected @endif>INDYWIDUALNY</option>
                            <option @if($package->package_variable == 'PARTNERSKI') selected @endif>PARTNERSKI</option>
                            <option @if($package->package_variable == 'RODZINNY') selected @endif>RODZINNY</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Dodaj skan umowy:</label>
                        <input id="user_scan" name="user_scan" type="file"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Zakres obowiązywania od:</label>
                        <div class="input-group date form_date col-md-5" data-date="" data-date-format="yyyy-mm-dd" data-link-field="datak" style="width:100%;">
                            <input class="form-control" name="medical_start" id="medical_start" type="text" value="{{$package->month_start}}" readonly >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <a class="btn btn-info" href="/api/getMedicalScan/{{$package->scan_path}}" download="{{$package->scan_path}}">Pobierz skan umowy</a>
                </div>
            </div>
            <hr>
            <div id="user_template_div">
                <input type="hidden" value="{{$package->id}}" name="medical_id[]" />
                <div class="row">
                    <div class="col-md-12">
                        <h3 style="color: #aaa">Dane osobowe pracownika:</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Imie:</label>
                            <input type="text" class="form-control" id="tempate_user_first_name" name="user_first_name[]" placeholder="Imie" value="{{$package->user_first_name}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nazwisko:</label>
                            <input type="text" class="form-control" id="template_user_last_name" name="user_last_name[]" placeholder="Nazwisko" value="{{$package->user_last_name}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Numer telefonu:</label>
                            <input type="number" class="form-control" placeholder="000" id="template_phone_number" name="phone_number[]" value="{{$package->phone_number}}"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>PESEL:</label>
                            <input type="number" placeholder="00000000000" class="form-control" id="pesel" name="pesel[]" value="{{$package->pesel}}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data urodzenia: *<span style="font-size: 10px">Tylko gdy brak PESEL (format u000-00-00).</span></label>
                            <input type="text" placeholder="u0000-00-00" class="form-control" id="birth_date" name="birth_date[]"  value="{{$package->birth_date}}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Miejscowość:</label>
                            <input type="text" class="form-control" placeholder="Miejscowość" name="city[]"  value="{{$package->city}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kod pocztowy:</label>
                            <input type="text" class="form-control" placeholder="00-000" name="postal_code[]"  value="{{$package->postal_code}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ulica:</label>
                            <input type="text" class="form-control" placeholder="Ulica" name="street[]"  value="{{$package->street}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Numer domu:</label>
                            <input type="number" class="form-control" placeholder="000" name="house_number[]" value="{{$package->house_number}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Numer mieszkania:</label>
                            <input type="number" class="form-control" placeholder="000" name="flat_number[]"  value="{{$package->flat_number}}">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Jezeli pakiet jest partnerski -->

        @if($package->package_variable == 'PARTNERSKI' && $package->family_member == 1)
            <div id="partner_selected">
                <div class="check_for_family">
                    <input type="hidden" value="{{$package->id}}" name="medical_id[]" />
                    <div class="row">
                        <div class="col-md-12">
                            <h3 style="color: #aaa">Dane osobowe partnera:</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Imie:</label>
                                <input type="text" class="form-control" name="user_first_name[]" placeholder="Imie" value="{{$package->user_first_name}}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nazwisko:</label>
                                <input type="text" class="form-control" name="user_last_name[]" placeholder="Nazwisko" value="{{$package->user_last_name}}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Numer telefonu:</label>
                                <input type="number" class="form-control" placeholder="000" id="phone_number" name="phone_number[]" value="{{$package->phone_number}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PESEL:</label>
                                <input type="number" placeholder="00000000000" class="form-control" name="pesel[]"  value="{{$package->pesel}}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data urodzenia: *<span style="font-size: 10px">W przypadku braku PESEL (format u000-00-00).</span></label>
                                <input type="text" placeholder="u0000-00-00" class="form-control" name="birth_date[]"  value="{{$package->birth_date}}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Miejscowość:</label>
                                <input type="text" class="form-control" placeholder="Miejscowość" name="city[]"  value="{{$package->city}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kod pocztowy:</label>
                                <input type="text" class="form-control" placeholder="00-000" name="postal_code[]" value="{{$package->postal_code}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ulica:</label>
                                <input type="text" class="form-control" placeholder="Ulica" name="street[]" value="{{$package->street}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Numer domu:</label>
                                <input type="number" class="form-control" placeholder="000" name="house_number[]" value="{{$package->house_number}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Numer mieszkania:</label>
                                <input type="number" class="form-control" placeholder="000" name="flat_number[]"  value="{{$package->flat_number}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Jezeli pakiet jest rodzinny -->
        <div id="old_members">
            @if($package->package_variable == 'RODZINNY' && $package->family_member == 1)

                @php
                    $add_family_by_default = true;
                @endphp

                <div class="check_for_family" id="oldmember{{$package->id}}">
                    <div class="row">
                        <hr>
                        <div class="col-md-6">
                            <h3>Dane osobowe członka rodziny:</h3>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-danger pull-right" style="margin-top: 15px" type="button" onclick="deleteOldMember({{$package->id}})">
                                <span class="glyphicon glyphicon-minus"></span> Usuń członka
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" value="{{$package->id}}" name="medical_id[]"/>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Imie:</label>
                                <input type="text" class="form-control" name="user_first_name[]" placeholder="Imie" value="{{$package->user_first_name}}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nazwisko:</label>
                                <input type="text" class="form-control" name="user_last_name[]" placeholder="Nazwisko" value="{{$package->user_last_name}}"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Numer telefonu:</label>
                                <input type="number" class="form-control" placeholder="000" id="phone_number" name="phone_number[]" value="{{$package->phone_number}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PESEL:</label>
                                <input type="number" placeholder="00000000000" class="form-control" name="pesel[]" value="{{$package->pesel}}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data urodzenia: *<span style="font-size: 10px">W przypadku braku PESEL (format u000-00-00).</span></label>
                                <input type="text" placeholder="u0000-00-00" class="form-control" name="birth_date[]" value="{{$package->birth_date}}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Miejscowość:</label>
                                <input type="text" class="form-control" placeholder="Miejscowość" name="city[]" value="{{$package->city}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kod pocztowy:</label>
                                <input type="text" class="form-control" placeholder="00-000" name="postal_code[]" value="{{$package->postal_code}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ulica:</label>
                                <input type="text" class="form-control" placeholder="Ulica" name="street[]" value="{{$package->street}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Numer domu:</label>
                                <input type="number" class="form-control" placeholder="000" name="house_number[]" value="{{$package->house_number}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Numer mieszkania:</label>
                                <input type="number" class="form-control" placeholder="000" name="flat_number[]" value="{{$package->flat_number}}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    @endforeach
    <div id="partner_selected">

    </div>
    <div id="new_members">

    </div>
    <div class="row" id="family_selected" style="display: @if(isset($add_family_by_default)) block @else none @endif">
        <div class="col-md-12">
            <div class="form-group">
                <button class="btn btn-info" style="width: 100%" id="add_family_member">
                    <span class="glyphicon glyphicon-plus"></span> Dodaj członka rodziny
                </button>
            </div>
        </div>
    </div>
</div>
