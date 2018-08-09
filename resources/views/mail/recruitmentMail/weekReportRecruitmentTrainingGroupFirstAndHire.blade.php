<table style="width:100%;border:1px solid #231f20;border-collapse:collapse;padding:3px">
    <thead style="color:#efd88f">
    <tr>
        <td colspan="2" style="border:1px solid #231f20;text-align:center;padding:3px;background:#231f20;color:#efd88f">
            <font size="6" face="Calibri">Raport Tygodniowy Szkoleń / Zatrudnionych kandydatów</font></td>
        <td colspan="2" style="border:1px solid #231f20;text-align:left;padding:6px;background:#231f20">
            <img src="http://teambox.pl/image/logovc.png" class="CToWUd"></td>
    </tr>
    <tr>
        <td colspan="4" style="border:1px solid #231f20;padding:3px;background:#231f20;color:#efd88f">Raport za okres od {{$start_date}} do {{$stop_date}}</td>
    </tr>
    <tr>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Oddział</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Obecych na pierwszym etapie szkolenia</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Zatrudnionych po szkoleniu</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">% (Zatrudnionych po szkoleniu) / (Obecych na pierwszym etapie szkolenia)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as  $item)
        <tr>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->dep_name.' '.$item->dep_name_type}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->sum_choise_stageOne}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->countHireUserFromFirstTrainingGroup}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->procScore}} %</td>
        </tr>
    @endforeach
    </tbody>
</table>
