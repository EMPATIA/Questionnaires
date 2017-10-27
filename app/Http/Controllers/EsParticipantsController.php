<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\EventSchedule;
use App\EsParticipant;
use App\One\One;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;


class EsParticipantsController extends Controller
{
    /**
     * Fields that are required for Store or Update
     *
     * @var Array 
     */
    protected $keysRequired = [
        'name'
    ];

    /**
     * @SWG\Tag(
     *   name="EsParticipant Method",
     *   description="Everything about EsParticipants Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="esParticipantsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="esParticipantsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="key", format="string", type="string"),
     *           @SWG\Property(property="event_schedule_id", format="integer", type="integer"),
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="user_key", format="string", type="string"),
     *           @SWG\Property(property="created_by", format="string", type="string"),
     *           @SWG\Property(property="updated_by", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */
    
    /**
     * Requests a list of Participants.
     * Returns the list of Participants.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */    
    public function index(Request $request)
    {
        try {
            $participants = EsParticipant::all();
            return response()->json(['data' => $participants], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Participants list'], 500);
        }     
        
        return response()->json(['error' => 'Unauthorized' ], 401);           
    }

    /**
     *
     * @SWG\Get(
     *  path="/esParticipant/{key}",
     *  summary="Shows a EsParticipant Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"EsParticipant Method"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="EsParticipant Method Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Shows the Participant data",
     *      @SWG\Schema(ref="#/definitions/esParticipantsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Participant not Found",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Participant",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request a specific Participant.
     * Returns the details of a specific Participant.
     * 
     * @param $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $key)
    {
            try {
            $participant = EsParticipant::whereKey($key)->firstOrFail();

            return response()->json($participant, 200);
        } 
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Participant not Found'], 404);
        } 
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Participant'], 500);
        }      
        
        return response()->json(['error' => 'Unauthorized' ], 401);      
    }

    /**
     *  @SWG\Definition(
     *   definition="esParticipantsCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"name"},
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="periods", type="array", @SWG\Items(type="integer")),
     *           @SWG\Property(property="questions", type="array", @SWG\Items(type="integer"))
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/esParticipant/{key}",
     *  summary="Create a EsParticipant Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"EsParticipant Method"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Event Schedule Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="EsParticipant",
     *      in="body",
     *      description="EsParticipant Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/esParticipantsCreate")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="The newly created EsParticipant",
     *      @SWG\Schema(ref="#/definitions/esParticipantsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Error in storing attendance | Failed to store participation",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  )
     * )
     *
     */
    
    /** 
     * Store a newly created Participant in storage. 
     * Returns the details of the newly created Participant.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $EventScheduleKey)
    {
        $userToken = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        do {
            $rand = str_random(32);

            if (!($exists = EsParticipant::whereKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);        
        
        try {
            $eventSchedule = EventSchedule::whereKey($EventScheduleKey)->firstOrFail();   
            
            $count = $eventSchedule->participants()->where("user_key","=",$userToken)->count();
            
            if($count == 0 && $eventSchedule->closed == 0){
                $data = ['key' => $key,
                         'name' => $request->json('name'),
                         'user_key' => $userToken];
                $participant = $eventSchedule->participants()->create($data);
                
                // Periods Sync
                if( is_array($request->json('periods'))){
                    $participant->periods()->sync($request->json('periods'));
                }
                
                // Questions Sync
                if( is_array($request->json('questions'))){
                    $participant->questions()->sync($request->json('questions'));
                }                

                return response()->json($participant, 201); 
            }else{
                 return response()->json(['error' => "Error in storing attendance"], 500);
            }
        }
        catch(Exception $e){
            return response()->json(['error' => "Failed to store participation"], 500);
        }                
        
        return response()->json(['error' => 'Unauthorized'], 401);              
    }

    /**
     *  @SWG\Definition(
     *   definition="esParticipantsUpdate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"participant_id", "name"},
     *           @SWG\Property(property="participant_id", format="integer", type="integer"),
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="periods", type="array", @SWG\Items(type="integer")),
     *           @SWG\Property(property="questions", type="array", @SWG\Items(type="integer"))
     *       )
     *   }
     * )
     *
     * @SWG\Put(
     *  path="/esParticipant/{key}",
     *  summary="Update a EsParticipant Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"EsParticipant Method"},
     *
     *  @SWG\Parameter(
     *      name="EsParticipant",
     *      in="body",
     *      description="EsParticipant Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/esParticipantsUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Event Schedule Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The Updated EsParticipant",
     *      @SWG\Schema(ref="#/definitions/esParticipantsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Participant not Found",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="This is already closed",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update the  Participant in storage.
     * Returns the details of the updated Participant.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $EventScheduleKey)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $eventSchedule = EventSchedule::whereKey($EventScheduleKey)->firstOrFail();  

            if($eventSchedule->closed == 0){                 
                $participant = $eventSchedule->participants()->findOrFail($request->json('participant_id'));
                
                $participant->name = $request->json('name');
                $participant->save();                     

                // Periods Sync
                if( is_array($request->json('periods'))){
                    $participant->periods()->sync($request->json('periods'));
                }            

                // Questions Sync
                if( is_array($request->json('questions'))){
                    $participant->questions()->sync($request->json('questions'));
                }              

                return response()->json($participant, 200);
            } else {
                return response()->json(['error' => "This is already closed"], 500);
            } 
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Participant not Found'], 404);
        } 
        catch (Exception $e) {
            return response()->json(['error' =>$e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="esParticipantDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/esParticipant/{key}",
     *  summary="Delete EsParticipant Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"EsParticipant Method"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="EsParticipant Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/esParticipantDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Participant not Found",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Participant",
     *      @SWG\Schema(ref="#/definitions/esParticipantsMethodErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Remove the specified Participant from storage.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $key)
    {
        ONE::verifyToken($request);

        try {
            $participant = EsParticipant::whereKey($key)->firstOrFail();
            $participant->delete();
            return response()->json('OK', 200);
        } 
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Participant not Found'], 404);
        } 
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Participant'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    
    /** 
     * Store a newly created Participant in storage. 
     * Returns the details of the newly created Participant.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function anonymousStore(Request $request, $eventScheduleKey)
    {
        ONE::verifyKeysRequest($this->keysRequired, $request);
        
        do {
            $rand = str_random(32);

            if (!($exists = EsParticipant::whereKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);        
        
        try {
            $eventSchedule = EventSchedule::whereKey($eventScheduleKey)->firstOrFail();   
            
            if( $eventSchedule->closed == 0){
                $data = ['key' => $key,
                         'name' => $request->json('name')];
                $participant = $eventSchedule->participants()->create($data);
                
                // Periods Sync
                if( is_array($request->json('periods'))){
                    $participant->periods()->sync($request->json('periods'));
                }
                
                // Questions Sync
                if( is_array($request->json('questions'))){
                    $participant->questions()->sync($request->json('questions'));
                }                

                return response()->json($participant, 201); 
            }else{
                 return response()->json(['error' => "Error in storing attendance"], 500);
            }
        }
        catch(Exception $e){
            return response()->json(['error' => "Failed to store participation"], 500);
        }                
        
        return response()->json(['error' => 'Unauthorized'], 401);              
    }
    
    

    /**
     * Update the  Participant in storage.
     * Returns the details of the updated Participant.
     *
     * @param Request $request
     * @param $eventScheduleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function anonymousUpdate(Request $request, $eventScheduleKey)
    {
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $eventSchedule = EventSchedule::whereKey($eventScheduleKey)->firstOrFail();     
     
            if($eventSchedule->closed == 0){                 
                $participant = $eventSchedule->participants()->findOrFail($request->json('participant_id'));

                $participant->name = $request->json('name');
                $participant->save();                            
                                
                // Periods Sync
                if( is_array($request->json('periods'))){
                    $participant->periods()->sync($request->json('periods'));
                }            

                // Questions Sync
                if( is_array($request->json('questions'))){
                    $participant->questions()->sync($request->json('questions'));
                }              

                return response()->json($participant, 200);
            } else {
                return response()->json(['error' => "This is already closed"], 500);
            } 
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Participant not Found'], 404);
        } 
        catch (Exception $e) {
            return response()->json(['error' =>$e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }    
    
    /**
     * Remove the specified Participant from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function anonymousDestroy(Request $request, $key)
    {
        try {
            $participant = EsParticipant::whereKey($key)->firstOrFail();
            $participant->delete();
            return response()->json('OK', 200);
        } 
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Participant not Found'], 404);
        } 
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Participant'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }    
 
}
