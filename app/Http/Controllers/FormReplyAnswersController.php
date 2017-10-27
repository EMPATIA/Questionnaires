<?php

namespace App\Http\Controllers;

use App\FormReplyAnswer;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FormReplyAnswersController extends Controller
{
    protected $keysRequired = [
        'form_id'
    ];
    /**
     * @SWG\Tag(
     *   name="Form Reply Answer",
     *   description="Everything about Form Reply Answers",
     * )
     *
     *  @SWG\Definition(
     *      definition="formReplyAnswerErrorDefault",
     *      @SWG\Property(property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="formReplyAnswerCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"form_id"},
     *           @SWG\Property(property="form_id", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     *  @SWG\Definition(
     *   definition="formReplyAnswerReply",
     *   type="object",
     *   allOf={
     *      @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="form_reply_answer_key", format="string", type="string"),
     *           @SWG\Property(property="form_reply_id", format="integer", type="integer"),
     *           @SWG\Property(property="question_id", format="integer", type="integer"),
     *           @SWG\Property(property="question_option_id", format="integer", type="integer"),
     *           @SWG\Property(property="answer", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string"),
     *           @SWG\Property(property="deleted_at", format="date", type="string"))
     *   }
     * )
     *
     *  @SWG\Definition(
     *     definition="formReplyAnswerDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     */

    /**
     * Request list of all Form Reply Answers
     * Returns the list of all Form Reply Answers
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $formReplyAnswers = FormReplyAnswer::all();
            return response()->json($formReplyAnswers, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Form Reply Answers list'], 500);
        }
    }

    /**
     * Request all Answers for a question
     * Returns the list of answers
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function listAnswers(Request $request, $questionId)
    {
        try{
            $questionAnswers = FormReplyAnswer::whereQuestionId($questionId)->get();
            return response()->json($questionAnswers, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply Answer not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Get(
     *  path="/formReplyAnswer/{form_reply_answer_key}",
     *  summary="Show a form reply answer",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Answer"},
     *
     *  @SWG\Parameter(
     *      name="form_reply_answer_key",
     *      in="path",
     *      description="Form reply answer Key",
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
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Form Reply Answer data",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Reply Answer not Found",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  )
     * )
     */

    /**
     * Request of one Form Reply Answer
     * Returns the attributes of the Form Reply Answer
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show(Request $request, $formReplyAnswerKey)
    {
        try{
            $formReplyAnswer = FormReplyAnswer::whereFormReplyAnswerKey($formReplyAnswerKey)->firstOrFail();
            return response()->json($formReplyAnswer, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply Answer not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/formReplyAnswer",
     *  summary="Create a Form Reply Answer",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Answer"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Form Reply Answer Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerCreate")
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
     *      description="the newly created Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Reply Answer not found",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  )
     * )
     */


    /**
     * Store a new Form Reply Answers in the database
     * Return the Attributes of the Form Reply Answers
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        //VERIFY PERMISSIONS

        try{
            do {
                $rand = str_random(32);
                if (!($exists = FormReplyAnswer::whereFormReplyAnswerKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $formReplyAnswer = FormReplyAnswer::create(
                [
                    'form_reply_answer_key' => $key,
                    'form_id'               => $request->json('form_id'),
                    'created_by'            => $userKey,
                ]
            );
            return response()->json($formReplyAnswer, 201);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Form Reply Answer'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/formReplyAnswer/{form_reply_answer_key}",
     *  summary="Update a Form Reply Answer",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Answer"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Form Reply Answer Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="form_reply_answer_key",
     *      in="path",
     *      description="Form Reply Answer Key",
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
     *      description="The updated Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Form Reply Answer not Found",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  )
     * )
     */

    /**
     * Update a existing Form Reply Answers
     * Return the Attributes of the Form Reply Answers
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $formReplyAnswerKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        //VERIFY PERMISSIONS

        try{
            $formReplyAnswer = FormReplyAnswer::whereFormReplyAnswerKey($formReplyAnswerKey)->firstOrFail();

            $formReplyAnswer->form_id    = $request->json('form_id');
            $formReplyAnswer->created_by = $userKey;

            $formReplyAnswer->save();

            return response()->json($formReplyAnswer, 200);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update Form Reply Answer'], 500);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply Answer not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/formReplyAnswer/{form_reply_answer_key}",
     *  summary="Delete a Form reply answer",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Answer"},
     *
     * @SWG\Parameter(
     *      name="form_reply_answer_key",
     *      in="path",
     *      description="Form reply answer Key",
     *      required=true,
     *      type="integer"
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
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Reply Answer not Found",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Form Reply Answer",
     *      @SWG\Schema(ref="#/definitions/formReplyAnswerErrorDefault")
     *  )
     * )
     */

    /**
     * Delete existing Form Reply Answer
     * @param Request $request
     * @param $formReplyAnswerKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $formReplyAnswerKey)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $formReplyAnswer = FormReplyAnswer::whereFormReplyAnswerKey($formReplyAnswerKey)->firstOrFail();
            $formReplyAnswer->delete();
            return response()->json('Ok', 200);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete Form Reply Answer'], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply Answer not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
