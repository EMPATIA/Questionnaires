<?php

namespace App\Http\Controllers;

use App\Form;
use App\FormReply;
use App\FormReplyAnswer;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FormRepliesController extends Controller
{
    protected $keysRequired = [
        'form_key'
    ];

    /**
     * @SWG\Tag(
     *   name="Form Reply Method",
     *   description="Everything about Form Replies Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="formRepliesMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="formRepliesReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="form_reply_key", format="string", type="string"),
     *           @SWG\Property(property="form_id", format="integer", type="integer"),
     *           @SWG\Property(property="completed", format="integer", type="integer"),
     *           @SWG\Property(property="step", format="integer", type="integer"),
     *           @SWG\Property(property="created_by", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Form Replies
     * Returns the list of all Form Replies
     * @param Request $request
     * @param $formKey
     * @return list of all
     */
    public function index(Request $request, $formKey)
    {
        try{

            $form = Form::whereFormKey($formKey)->firstOrFail();

            $formReplies = $form->formReplies()->get();
            return response()->json(['data' => $formReplies], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Form Replies list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/formReply/{form_reply_key}",
     *  summary="Shows a Form Reply Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Method"},
     *
     * @SWG\Parameter(
     *      name="form_reply_key",
     *      in="path",
     *      description="Form Reply Method Key",
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
     *      description="Shows the Form Reply data",
     *      @SWG\Schema(ref="#/definitions/formRepliesReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Reply not Found",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Form Reply",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Form Reply
     * Returns the attributes of the Form Reply
     * @param Request $request
     * @param $formReplyKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show(Request $request, $formReplyKey)
    {
        try{
            $formReply = FormReply::whereFormReplyKey($formReplyKey)->firstOrFail();
            return response()->json($formReply, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Form Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *  @SWG\Definition(
     *   definition="formRepliesCreate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"form_key", "question_replies", "completed"},
     *           @SWG\Property(property="form_key", format="string", type="string"),
     *           @SWG\Property(property="completed", format="boolean", type="boolean"),
     *           @SWG\Property(property="step", format="integer", type="integer"),
     *           @SWG\Property(
     *              property="question_replies",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="question_id", format="integer", type="integer"),
     *                          @SWG\Property(property="string", format="string", type="string"),
     *                      )
     *                  )
     *           )
     *
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/formReply",
     *  summary="Create a Form Reply Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Method"},
     *
     *  @SWG\Parameter(
     *      name="Form Reply",
     *      in="body",
     *      description="Form Reply Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formRepliesCreate")
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
     *      description="The newly created Form Reply",
     *      @SWG\Schema(ref="#/definitions/formRepliesReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Form Reply",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Form Reply in the database
     * Return the Attributes of the Form Reply
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        try{
            ONE::verifyKeysRequest($this->keysRequired, $request);
            ONE::verifyKeysRequest(['question_replies'], $request);
            do {
                $rand = str_random(32);
                if (!($exists = FormReply::whereFormReplyKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $form = Form::whereFormKey($request->json('form_key'))->firstOrFail();

            if ($form->public){
                $userKey = !empty($request->json('username')) ? $request->json('username') : "anonymous";
            }else{
                try {
                    $userKey = ONE::verifyToken($request);
                } catch(Exception $e){
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }

            if (FormReply::whereFormId($form->id)->whereCreatedBy($userKey)->exists() && $userKey != "anonymous"){

                $formReply = FormReply::whereFormId($form->id)->whereCreatedBy($userKey)->firstOrFail();
                $questionReplies = $request->json('question_replies');
                foreach ($questionReplies as $questionReply) {
                    if (!empty($questionReply['question_id'])) {

                        if ((isset($questionReply['question_option_id']) && empty($questionReply['question_option_id'])) || isset($questionReply['answer']) && empty($questionReply['answer'])){
                            FormReplyAnswer::whereFormReplyId($formReply->id)->whereQuestionId($questionReply['question_id'])->delete();
                        }

                        if (isset($questionReply['question_option_id']) && is_array($questionReply['question_option_id'])) {
                            if(FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->exists()){
                                $allOptions = FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->pluck('id')->toArray();
                                foreach ($questionReply['question_option_id'] as $question_option_id) {
                                    $formReplyAnswerId = FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->whereQuestionOptionId($question_option_id)->first();
                                    if (!$formReplyAnswerId){
                                        FormReplyAnswer::create(
                                            [
                                                'form_reply_id' => $formReply->id,
                                                'question_id' => $questionReply['question_id'],
                                                'question_option_id' => $question_option_id,
                                                'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                            ]
                                        );
                                    }else{
                                        $deleteKey = array_search($formReplyAnswerId->id, $allOptions);
                                        if(!is_null($deleteKey)){
                                            unset($allOptions[$deleteKey]);
                                        }
                                    }
                                }

                                if(!is_null($allOptions)){
                                    foreach ($allOptions as $optionToRemove){
                                        FormReplyAnswer::destroy($optionToRemove);
                                    }
                                }
                            }else{
                                foreach ($questionReply['question_option_id'] as $question_option_id) {
                                    FormReplyAnswer::create(
                                        [
                                            'form_reply_id' => $formReply->id,
                                            'question_id' => $questionReply['question_id'],
                                            'question_option_id' => $question_option_id,
                                            'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                        ]
                                    );
                                }
                            }
                        } elseif ( array_key_exists('question_option_id',$questionReply) && ($questionReply['question_option_id'] == null || $questionReply['question_option_id']  === 'null')){
                            $allOptions = FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->pluck('id')->toArray();
                            if(!is_null($allOptions)){
                                foreach ($allOptions as $optionToRemove){
                                    FormReplyAnswer::destroy($optionToRemove);
                                }
                            }
                        }else{
                            if((isset($questionReply['question_option_id']) && !empty($questionReply['question_option_id'])) || (isset($questionReply['answer']) && !empty($questionReply['answer']))){
                                if (FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->exists()) {
                                    $formReplyAnswer = FormReplyAnswer::whereQuestionId($questionReply['question_id'])->whereFormReplyId($formReply->id)->first();

                                    $formReplyAnswer->update(
                                        [
                                            'form_reply_id' => $formReply->id,
                                            'question_id' => $questionReply['question_id'],
                                            'question_option_id' => (isset($questionReply['question_option_id'])) ? $questionReply['question_option_id'] : "",
                                            'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                        ]
                                    );
                                } else {
                                    FormReplyAnswer::create(
                                        [
                                            'form_reply_id' => $formReply->id,
                                            'question_id' => $questionReply['question_id'],
                                            'question_option_id' => (isset($questionReply['question_option_id'])) ? $questionReply['question_option_id'] : "",
                                            'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }
                $formReply->completed = empty($request->json('completed')) ?  null : $request->json('completed');
                $formReply->step = empty($request->json('step')) ?  null : $request->json('step');
                // $formReply->location = empty($request->json('location')) ?  null : $request->json('location');

                $formReply->save();

            } else {

                $formReply = FormReply::create(
                    [
                        'form_id'           => $form->id,
                        'form_reply_key'    => $key,
                        'created_by'        => $userKey
                    ]
                );
                $questionReplies = $request->json('question_replies');
                foreach ($questionReplies as $questionReply) {
                    if (!empty($questionReply['question_id'])) {
                        if (isset($questionReply['question_option_id']) && is_array($questionReply['question_option_id'])) {
                            foreach ($questionReply['question_option_id'] as $question_option_id) {
                                FormReplyAnswer::create(
                                    [
                                        'form_reply_id' => $formReply->id,
                                        'question_id' => $questionReply['question_id'],
                                        'question_option_id' => $question_option_id,
                                        'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                    ]
                                );
                            }
                        } else {
                            FormReplyAnswer::create(
                                [
                                    'form_reply_id' => $formReply->id,
                                    'question_id' => $questionReply['question_id'],
                                    'question_option_id' => (isset($questionReply['question_option_id'])) ? $questionReply['question_option_id'] : "",
                                    'answer' => (isset($questionReply['answer'])) ? $questionReply['answer'] : "",
                                ]
                            );
                        }
                    }
                }
                $formReply->completed = empty($request->json('completed')) ?  null : $request->json('completed');
                $formReply->step = empty($request->json('step')) ?  null : $request->json('step');
                $formReply->location = empty($request->json('location')) ?  null : $request->json('location');

                $formReply->save();

            }


            return response()->json($formReply, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch(Exception $e){
            dd($e);
            return response()->json(['error' => 'Failed to store new Form Reply'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update a existing Form Reply
     * Return the Attributes of the Form Reply
     * @param Request $request
     * @param $formReplyKey
     * @return mixed
     * @internal param $id
     */
    public function update(Request $request, $formReplyKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        //VERIFY PERMISSIONS

        try{
            $formReply = FormReply::whereFormReplyKey($formReplyKey)->firstOrFail();

            $formReply->form_id     = $request->json('form_id');
            $formReply->created_by  = $userKey;
            $formReply->completed   = empty($request->json('completed')) ? $formReply->completed = null : $formReply->$request->json('completed');
            $formReply->step        = empty($request->json('step')) ? $formReply->step = null : $formReply->$request->json('step');
            // $formReply->location    = empty($request->json('location')) ? $formReply->location = null : $formReply->$request->json('location');

            $formReply->save();

            return response()->json($formReply, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Form Reply'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="formReplyDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/formReply/{form_reply_key}",
     *  summary="Delete a Form Reply",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Reply Method"},
     *
     * @SWG\Parameter(
     *      name="form_reply_key",
     *      in="path",
     *      description="Form Reply Key",
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
     *      @SWG\Schema(ref="#/definitions/formReplyDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Reply not Found",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Form Reply",
     *      @SWG\Schema(ref="#/definitions/formRepliesMethodErrorDefault")
     *  )
     * )
     */

    /**
     * Delete existing Form Reply
     * @param Request $request
     * @param $formReplyKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $formReplyKey)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $formReply = FormReply::whereFormReplyKey($formReplyKey)->firstOrFail();
            $formReply->delete();
            return response()->json('Ok', 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Form Reply'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request to verify if User reply the Form
     * Returns the attributes of the Form Reply
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function verifyReply(Request $request, $formKey)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $form = Form::whereFormKey($formKey)->first();

            $formReply = FormReply::whereFormId($form->id)->whereCreatedBy($userKey)->first();

            if (!is_null($formReply) && $formReply->completed){
                return response()->json(['response' => true], 200);
            } else{
                return response()->json(['response' => false], 200);
            }
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Form Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request to verify if User reply the Form
     * Returns the attributes of the Form Reply
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function formUserAnswers(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $formReply = FormReply::whereCreatedBy($userKey)->firstOrFail();
            $data = FormReply::with(['formReplyAnswer'])->findOrFail($formReply->id);
            return response()->json($data, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Reply not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Form Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
