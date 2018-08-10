<?php

namespace App\Http\Controllers;

use App\ActivityRecorder;
use App\ClientGiftType;
use App\ClientMeetingType;
use App\Clients;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Panel to managment all client (VIEW)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function clientPanel(){
        $clientGiftType = ClientGiftType::where('status','=',1)->get();
        $clientMeetingType = ClientMeetingType::where('status','=',1)->get();
       return view('crmRoute.clientPanel')
            ->with('clientGiftType',$clientGiftType)
            ->with('clientMeetingType',$clientMeetingType);
    }
    /**
     *  Return all city with info
     */
    public function getClient(Request $request){
        if($request->ajax()){
            if(isset($request->showDisabledClient) && $request->showDisabledClient == 'false')
                $showClient = 1;
            else
                $showClient = '%';
           $clients = Clients::where('status','like',$showClient)->get();
            $clientExtended = $clients->map(function($item) {
                if($item->priority == 1) {
                    $item->priorityName = "Niski";
                }
                else if($item->priority == 2) {
                    $item->priorityName = "Średni";
                }
                else if($item->priority == 3) {
                    $item->priorityName = "Wysoki";
                }
                return $item;
            });
           return datatables($clientExtended)->make(true);
        }
    }


    /**
     *  Return all gift with info
     */
    public function getGiftType(Request $request){
        if($request->ajax()){
            $gift = ClientGiftType::all();
            return datatables($gift)->make(true);
        }
    }

    /**
     *  Return all Meeting with info
     */
    public function getMeetingType(Request $request){
        if($request->ajax()){
            $meeting = ClientMeetingType::all();
            return datatables($meeting)->make(true);
        }
    }

    /**
     * Save new/edit client
     * @param Request $request
     */
    public function saveClient(Request $request){
        if($request->ajax()) {
            $data = [];
            $action = 0;
            if ($request->clientID == 0) {
                // new client
                $client = new Clients();
                $data = ['T'=>'Dodanie nowego klienta'];
                $action = 1;
                /*
                new ActivityRecorder(null, 194, 1);*/
            }
            else { // Edit client
                //new ActivityRecorder(null, 194, 2);
                $client = Clients::find($request->clientID);
                $data = ['T'=>'Edycja klilenta'];
                $action = 1;
            }

            $client->name = $request->clientName;
            $client->priority = $request->clientPriority;
            $client->type = $request->clientType;
            $client->comment = $request->clientComment;
            $client->invoice_name = $request->clientNameInvoice;
            $client->meeting_type_id = $request->clientMeetingType;
            $client->gift_type_id = $request->clientGiftType;
            $client->payment_phone = $request->clientPaymentPhone;
            $client->payment_mail = $request->clientPaymentMail;
            $client->failures_phone = $request->clientFailuresPhone;
            $client->failures_mail = $request->clientFailuresMail;
            $client->schedule_phone = $request->clientSchedulePhone;
            $client->schedule_mail = $request->clientScheduleMail;
            $client->manager_phone = $request->clientManagerPhone;
            $client->manager_mail = $request->clientManagersMail;

            if ($request->clientID == 0) {
                $client->status = 1;
            }

            $client->save();

            new ActivityRecorder(array_merge($data,$client->toArray()), 208, $action);
            return 200;
        }
    }

    /**
     * turn off client change status to 1 disable or 0 avaible
     * @param Request $request
     */
    public function changeStatusClient(Request $request){
        if($request->ajax()){
            $client = Clients::find($request->clientId);
            if($client->status == 0)
                $client->status = 1;
            else
                $client->status = 0;
            $client->save();
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu klienta'], $client->toArray()),208,4);
        }
    }
    /**
     * save new gift
     * @param Request $request
     */
    public function saveNewGift(Request $request){
        if($request->ajax()){
            $newGift = new ClientGiftType();
            $newGift->name = $request->name;
            $newGift->status = 1;
            $newGift->save();
            new ActivityRecorder(array_merge(['T'=>'Dodane nowego upominku'], $newGift->toArray()),208,1);
        }
    }
    /**
     * edit gift
     * @param Request $request
     */
    public function editGift(Request $request){
        if($request->ajax()){
            $gift = ClientGiftType::find($request->id);
            $gift->name = $request->name;
            $gift->save();
            new ActivityRecorder(array_merge(['T'=>'Edycja upominku'], $gift->toArray()),208,2);
        }
    }



    /**
     * turn off gift change status to 1 disable or 0 avaible
     * @param Request $request
     */
    public function changeGiftStatus(Request $request){
        if($request->ajax()){
            $gift = ClientGiftType::find($request->giftId);
            if($gift->status == 0)
                $gift->status = 1;
            else
                $gift->status = 0;
            $gift->save();
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu upominka'], $gift->toArray()),208,4);
        }
    }

    /**
     * saveNewMeeting
     * @param Request $request
     */
    public function saveNewMeeting(Request $request){
        if($request->ajax()){
            $newMeeting = new ClientMeetingType();
            $newMeeting->name = $request->name;
            $newMeeting->status = 1;
            $newMeeting->save();
            new ActivityRecorder(array_merge(['T'=>'Dodane nowego typu trasy'], $newMeeting->toArray()),208,1);
        }
    }


    /**
     * edit Meeting
     * @param Request $request
     */
    public function editMeeting(Request $request){
        if($request->ajax()){

            $meeting = ClientMeetingType::find($request->id);
            $meeting->name = $request->name;
            $meeting->save();
            new ActivityRecorder(array_merge(['T'=>'Edycja typu pokazu'], $meeting->toArray()),208,2);
        }
    }

    /**
     * turn off meeting change status to 1 disable or 0 avaible
     * @param Request $request
     */
    public function changeMeetingStatus(Request $request){
        if($request->ajax()){
            $meeting = ClientMeetingType::find($request->meetingId);
            if($meeting->status == 0)
                $meeting->status = 1;
            else
                $meeting->status = 0;
            $meeting->save();
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu typu pokazu'], $meeting->toArray()),208,4);
        }
    }

    /**
     * find client by id
     */
    public function findClient(Request $request){
        if($request->ajax()){
            $client = Clients::find($request->clientId);
            return $client;
        }
    }


}
