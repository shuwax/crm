<table style="width:100%;border:1px solid #231f20;border-collapse:collapse;padding:3px">
    <thead style="color:#efd88f">
    <tr>
        <td colspan="2" style="border:1px solid #231f20;text-align:center;padding:3px;background:#231f20;color:#efd88f">
            <font size="6" face="Calibri">Raport Dzienny Szkoleń</font></td>
        <td colspan="2" style="border:1px solid #231f20;text-align:left;padding:6px;background:#231f20">
            <img src="http://teambox.pl/image/logovc.png" class="CToWUd"></td>
    </tr>
    <tr>
        <td colspan="4" style="border:1px solid #231f20;padding:3px;background:#231f20;color:#efd88f">Raport dla dnia: {{$start_date}}</td>
    </tr>
    <tr>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Oddział</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Umówionych</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Obecnych</th>
        <th style="border:1px solid #231f20;padding:3px;background:#231f20">Nieobecnych</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as  $item)
        <tr>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->dep_name.' '.$item->dep_name_type}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->sum_choise+$item->sum_absent}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->sum_choise}}</td>
            <td  style="border:1px solid #231f20;text-align:center;padding:3px">{{$item->sum_absent}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
