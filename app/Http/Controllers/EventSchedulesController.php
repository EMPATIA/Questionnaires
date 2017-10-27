<?php

namespace App\Http\Controllers;

use App\EventSchedule;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Class EventSchedulesController
 * @package App\Http\Controllers
 */
class EventSchedulesController extends Controller
{
    /**
     * Fields that are required for Store or Update
     *
     * @var Array
     */
    protected $keysRequired = [
        'title'
    ];

    /**
     * @SWG\Tag(
     *   name="Event Schedule Method",
     *   description="Everything about Forms Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="eventSchedulesMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="eventSchedulesReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="key", format="string", type="string"),
     *           @SWG\Property(property="entity_id", format="integer", type="integer"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="local", format="string", type="string"),
     *           @SWG\Property(property="closed", format="integer", type="integer"),
     *           @SWG\Property(property="public", format="integer", type="integer"),
     *           @SWG\Property(property="created_by", format="string", type="string"),
     *           @SWG\Property(property="updated_by", format="string", type="string"),
     *           @SWG\Property(property="es_period_id", format="integer", type="integer"),
     *           @SWG\Property(property="es_question_id", format="integer", type="integer"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Requests a list of Event Schedules.
     * Returns the list of Event Schedules.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $eventSchedules = EventSchedule::all();
            return response()->json(['data' => $eventSchedules], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Event Schedules list'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/eventSchedule/{event_schedule_key}",
     *  summary="Shows a Event Schedule Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Event Schedule Method"},
     *
     * @SWG\Parameter(
     *      name="event_schedule_key",
     *      in="path",
     *      description="Event Schedule Method Key",
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
     *      description="Shows the Event Schedule data",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Event Schedule not Found",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request a specific Event Schedule.
     * Returns the details of a specific Event Schedule.
     *
     * @param $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $key)
    {
        try {
            $eventSchedule = EventSchedule::whereKey($key)
                ->firstOrFail();

            if($eventSchedule->type_id == 1) {
                $eventSchedule = EventSchedule::with(['periods',
                    'participants' =>
                        function ($query) {
                            $query->with('periods')
                                ->get();
                        }])
                    ->whereKey($key)
                    ->firstOrFail();
                foreach ($eventSchedule->periods as $period){
                    //  begin timezone conversion
                    $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');
                    $period = ONE::timezoneConversion($period, 'UTC', $timezone);
                    //  end timezone conversion
                }

            }else if($eventSchedule->type_id == 2){
                $eventSchedule = EventSchedule::with(['questions',
                    'participants' =>
                        function ($query) {
                            $query->with('questions')
                                ->get();
                        }])
                    ->whereKey($key)
                    ->firstOrFail();
            }

            return response()->json($eventSchedule, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event Schedule not Found'], 404);
        }
        catch (Exception $e) {
            return response()->json(['error' =>$e], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="eventSchedulesCreate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"entity_id", "title", "description", "local", "type_id", "closed", "public"},
     *           @SWG\Property(property="entity_id", format="integer", type="integer"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="local", format="string", type="string"),
     *           @SWG\Property(property="closed", format="integer", type="integer"),
     *           @SWG\Property(property="public", format="integer", type="integer"),
     *           @SWG\Property(
     *              property="periods",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="start_date", format="date", type="string"),
     *                          @SWG\Property(property="end_date", format="date", type="string"),
     *                          @SWG\Property(property="start_time", format="time", type="string"),
     *                          @SWG\Property(property="end_time", format="time", type="string")
     *                  )
     *           ),
     *          @SWG\Property(
     *              property="questions",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="question", format="string", type="string")
     *                  )
     *           ),
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/eventSchedule",
     *  summary="Create a Event Schedule Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Event Schedule Method"},
     *
     *  @SWG\Parameter(
     *      name="Event Schedule",
     *      in="body",
     *      description="Event Schedule Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/eventSchedulesCreate")
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
     *      description="The newly created Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store a newly created Event Schedule in storage.
     * Returns the details of the newly created Event Schedule.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $key = '';
            do {
                $rand = str_random(32);
                if (!($exists = EventSchedule::whereKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $event = EventSchedule::create([
                'key' => $key,
                'entity_id'  => $request->json('entity_id'),
                'title' => $request->json('title'),
                'description' => $request->json('description'),
                'local' => $request->json('local'),
                'type_id' => $request->json('type_id'),
                'closed' => $request->json('closed'),
                'public' => $request->json('public'),
                'created_by' => $userKey
            ]);

            // Store Periods
            if(!empty($request->json('periods')) ){
                foreach ($request->json('periods') as $period){
                    // if (isset($period['start_date']) && isset($period['end_date'])){

                    //  begin timezone conversion
                    $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');
                    $period = ONE::timezoneConversion($period, $timezone);
                    //  end timezone conversion

                    $event->periods()->create([
                        'start_date' => $period['start_date'],
                        'end_date'   => $period['end_date'],
                        'start_time' => $period['start_time'],
                        'end_time'   => $period['end_time'],
                        'updated_by' => $userKey,
                        'created_by' => $userKey
                    ]);
                    //  }
                }
            }

            // Store Questions
            if(!empty($request->json('questions')) ){
                foreach ($request->json('questions') as $question){
                    // if (isset($period['start_date']) && isset($period['end_date'])){
                    $event->questions()->create([
                        'question' => $question['question'],
                        'updated_by' => $userKey,
                        'created_by' => $userKey
                    ]);
                    //  }
                }
            }

            // Event Schedule
            if($request->json('type_id') == 1){
                $eventSchedule = EventSchedule::with(['periods',
                    'participants' =>
                        function ($query) {
                            $query->with('periods')
                                ->get();
                        }
                ])
                    ->whereKey($event->key)
                    ->firstOrFail();
            }elseif($request->json('type_id') == 2){
                $eventSchedule = EventSchedule::with(['questions',
                    'participants' =>
                        function ($query) {
                            $query->with('questions')
                                ->get();
                        }
                ])
                    ->whereKey($event->key)
                    ->firstOrFail();
            }

            return response()->json($eventSchedule, 201);
        }
        catch(Exception $e){
            return response()->json(['error' => 'Error Creating Event Schedule'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *
     *
     *  @SWG\Definition(
     *   definition="eventSchedulesUpdate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"entity_id", "title", "description", "local", "type_id", "closed", "public", "es_period_id", "es_question_id"},
     *           @SWG\Property(property="entity_id", format="integer", type="integer"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="local", format="string", type="string"),
     *           @SWG\Property(property="closed", format="integer", type="integer"),
     *           @SWG\Property(property="public", format="integer", type="integer"),
     *           @SWG\Property(
     *              property="periods",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="start_date", format="date", type="string"),
     *                          @SWG\Property(property="end_date", format="date", type="string"),
     *                          @SWG\Property(property="start_time", format="time", type="string"),
     *                          @SWG\Property(property="end_time", format="time", type="string")
     *                  )
     *           ),
     *          @SWG\Property(
     *              property="questions",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="question", format="string", type="string")
     *                  )
     *           ),
     *           @SWG\Property(property="es_period_id", format="integer", type="integer"),
     *           @SWG\Property(property="es_question_id", format="integer", type="integer"),
     *       )
     *   }
     * )
     *
     * @SWG\Put(
     *  path="/eventSchedule/{event_schedule_key}",
     *  summary="Update a Event Schedule Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Event Schedule Method"},
     *
     *  @SWG\Parameter(
     *      name="Event Schedule",
     *      in="body",
     *      description="Event Schedule Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/eventSchedulesUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="event_schedule_key",
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
     *      description="The Updated Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Event Schedule not Found",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update the  Event Schedule in storage.
     * Returns the details of the updated Event Schedule.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {

            $eventSchedule = EventSchedule::whereKey($key)->firstOrFail();

            $eventSchedule->title = $request->json('title');
            $eventSchedule->description = $request->json('description');
            $eventSchedule->local = $request->json('local');
            $eventSchedule->closed = $request->json('closed');
            $eventSchedule->public = $request->json('public');
            $eventSchedule->es_period_id = $request->json('es_period_id');
            $eventSchedule->es_question_id = $request->json('es_question_id');
            $eventSchedule->updated_by = $userKey;
            $eventSchedule->save();

            // Questions
            if(!empty($request->json('questions')) ){
                foreach ($request->json('questions') as $question){
                    if( isset($question['remove']) && $question['remove'] == "1" ){
                        $eventSchedule->questions()->findOrFail($question['question_id'])->delete();
                    } else {
                        if( $question['question_id'] == ""){
                            $eventSchedule->questions()
                                ->create(['question' => $question['question'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey]);
                        } else {
                            $eventSchedule->questions()
                                ->findOrFail($question['question_id'])
                                ->update([
                                    'question' => $question['question'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey
                                ]);
                        }
                    }
                }
            }

            // Periods
            if(!empty($request->json('periods')) ){
                foreach ($request->json('periods') as $period){

                    if( isset($period['remove']) && $period['remove'] == "1" ){
                        $eventSchedule->periods()->findOrFail($period['period_id'])->delete();
                    } else {

                        //  begin timezone conversion
                        $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');
                        $period = ONE::timezoneConversion($period, $timezone);
                        //  end timezone conversion

                        if(empty($period['period_id'])){
                            $eventSchedule->periods()
                                ->create(['start_date' => $period['start_date'],
                                    'end_date'   => $period['end_date'],
                                    'start_time' => $period['start_time'],
                                    'end_time'   => $period['end_time'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey]);
                        } else {
                            $eventSchedule->periods()
                                ->findOrFail($period['period_id'])
                                ->update([
                                    'start_date' => $period['start_date'],
                                    'end_date'   => $period['end_date'],
                                    'start_time' => $period['start_time'],
                                    'end_time'   => $period['end_time'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey
                                ]);
                        }
                    }
                }
            }

            return response()->json($eventSchedule, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event Schedule not Found'], 404);
        }
        catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="eventScheduleDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/eventSchedule/{event_schedule_key}",
     *  summary="Delete Event Schedule Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Event Schedule Method"},
     *
     * @SWG\Parameter(
     *      name="event_schedule_key",
     *      in="path",
     *      description="Event Schedule Key",
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
     *      @SWG\Schema(ref="#/definitions/eventScheduleDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Event Schedule not Found",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Event Schedule",
     *      @SWG\Schema(ref="#/definitions/eventSchedulesMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Remove the specified Event Schedule from storage.
     * @param $key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        ONE::verifyToken($request);

        try {
            $eventSchedule = EventSchedule::whereKey($key)->firstOrFail();
            $eventSchedule->delete();
            return response()->json('OK', 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event Schedule not Found'], 404);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Event Schedule'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the  Event Schedule in storage.
     * Returns the details of the updated Event Schedule.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDetails(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $eventSchedule = EventSchedule::whereKey($key)->firstOrFail();
            $eventSchedule->title = $request->json('title');
            $eventSchedule->description = $request->json('description');
            $eventSchedule->local = $request->json('local');
            $eventSchedule->updated_by = $userKey;
            $eventSchedule->save();

            return response()->json($eventSchedule, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event Schedule not Found'], 404);
        }
        catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the  Event Schedule in storage.
     * Returns the details of the updated Event Schedule.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePeriods(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $eventSchedule = EventSchedule::whereKey($key)->firstOrFail();
            $eventSchedule->title = $request->json('title');
            $eventSchedule->description = $request->json('description');
            $eventSchedule->local = $request->json('local');
            $eventSchedule->updated_by = $userKey;
            $eventSchedule->save();

            // Update Periods
            if(!empty($request->json('periods')) ){
                foreach ($request->json('periods') as $period){

                    //  begin timezone conversion
                    $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');
                    $period = ONE::timezoneConversion($period, $timezone);
                    //  end timezone conversion

                    if( isset($period['remove']) && $period['remove'] == 1 ){
                        $eventSchedule->periods()->findOrFail($period['period_id'])->delete();
                    } else {
                        if(empty($period['period_id'])){
                            $eventSchedule->periods()
                                ->create(['start_date' => $period['start_date'],
                                    'end_date'   => $period['end_date'],
                                    'start_time' => $period['start_time'],
                                    'end_time'   => $period['end_time'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey]);
                        } else {
                            $eventSchedule->periods()
                                ->findOrFail($period['period_id'])
                                ->update([
                                    'start_date' => $period['start_date'],
                                    'end_date'   => $period['end_date'],
                                    'start_time' => $period['start_time'],
                                    'end_time'   => $period['end_time'],
                                    'updated_by' => $userKey,
                                    'created_by' => $userKey
                                ]);
                        }
                    }
                }
            }
            return response()->json($eventSchedule, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event Schedule not Found'], 404);
        }
        catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


}