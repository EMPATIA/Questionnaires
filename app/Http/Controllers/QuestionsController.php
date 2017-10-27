<?php

namespace App\Http\Controllers;

use App\Form;
use App\One\One;
use App\Question;
use App\QuestionGroup;
use App\QuestionOption;
use App\QuestionType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuestionsController extends Controller
{
    protected $keysRequired = [
        'question_group_key',
        'question_type_key',
        'mandatory',
        'position'
    ];


    /**
     * @SWG\Tag(
     *   name="Question Method",
     *   description="Everything about Questions Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="questionsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="questionsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="question_key", format="string", type="string"),
     *           @SWG\Property(property="question_group_id", format="integer", type="integer"),
     *           @SWG\Property(property="question_type_id", format="integer", type="integer"),
     *           @SWG\Property(property="question", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="mandatory", format="integer", type="integer"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="reuse_question_options", format="integer", type="integer"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Questions
     * Returns the list of all Questions
     * @return list of all
     */
    public function index(Request $request)
    {
        try {
            $questions = Question::all();
            return response()->json(['data' => $questions], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Questions list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/question/{question_key}",
     *  summary="Shows a Question Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Method"},
     *
     * @SWG\Parameter(
     *      name="question_key",
     *      in="path",
     *      description="Question Method Key",
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
     *      description="Shows the Question data",
     *      @SWG\Schema(ref="#/definitions/questionsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question not Found",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Question",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Question
     * Returns the attributes of the Question
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show(Request $request, $questionKey)
    {
        try {
            $question = Question::with("questionType", "questionGroup", "questionGroup.form")->whereQuestionKey($questionKey)->firstOrFail();

            $correctOptions = explode(",", $question->correctOption);

            $correct = [];
            foreach($correctOptions as $correctOption){
                $correct [] = QuestionOption::whereId($correctOption)->first();
            }

            //Get title from Forms Translation
            if(!($question->translation($request->header('LANG-CODE')))) {
                if (!$question->translation($request->header('LANG-CODE-DEFAULT'))) {
                    // return response()->json(['error' => 'No translation found'], 404);
                }
            }

            $questionTranslations = $question->questionTranslations()->get();
            $question['translations'] = $questionTranslations;
            $question['correctOptions'] = $correct;

            return response()->json($question, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to return Question'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="questionsCreate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"question_group_key", "question_type_key", "question", "description", "mandatory", "position"},
     *           @SWG\Property(property="question_group_key", format="string", type="string"),
     *           @SWG\Property(property="question_type_key", format="string", type="string"),
     *           @SWG\Property(property="question", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="mandatory", format="integer", type="integer"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="reuse_question_options", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/question",
     *  summary="Create a Question Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Method"},
     *
     *  @SWG\Parameter(
     *      name="Question",
     *      in="body",
     *      description="Question Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionsCreate")
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
     *      description="The newly created Question",
     *      @SWG\Schema(ref="#/definitions/questionsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Question",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store a new Question in the database
     * Return the Attributes of the Question
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        $entityKey = $request->header('X-ENTITY-KEY');
        try {
            do {
                $rand = str_random(32);
                if (!($exists = Question::whereQuestionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $questionGroupId = QuestionGroup::whereQuestionGroupKey($request->json('question_group_key'))->firstOrFail()->id;
            $questionTypeId = QuestionType::whereQuestionTypeKey($request->json('question_type_key'))->firstOrFail()->id;

            $question = Question::whereQuestionGroupId($questionGroupId)
                ->orderBy('position', 'desc')
                ->first();

            $lastPosition = !is_null($question) ? $question->position+1 : '0';
            $question = Question::create(
                [
                    'question_key' => $key,
                    'question_group_id' => $questionGroupId,
                    'question_type_id' => $questionTypeId,
                    'mandatory' => $request->json('mandatory'),
                    'position' => $lastPosition,
                    'reuse_question_options' => empty($request->json('reuse_question_options')) ? false : $request->json('reuse_question_options')
                ]
            );

           foreach ($request->json('translations') as $translation){
               if(isset($translation['question']) && isset($translation['description'])){
                    $question->questionTranslations()->create(
                        [
                            'question' => $translation['question'],
                            'description' => empty($translation['description']) ? "" : $translation['description'],
                            'language_code' => $translation['language_code']
                        ]
                    );
                }
            }

            return response()->json($question, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Question '.$e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *
     * @SWG\Put(
     *  path="/question/{question_key}",
     *  summary="Update a Question Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Method"},
     *
     *  @SWG\Parameter(
     *      name="Question",
     *      in="body",
     *      description="Question Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionsCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="question_key",
     *      in="path",
     *      description="Question Key",
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
     *      description="The Updated Question",
     *      @SWG\Schema(ref="#/definitions/questionsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question not Found",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Question",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Question
     * Return the Attributes of the Question Group
     * @param Request $request
     * @param $questionKey
     * @return mixed
     * @internal param $id
     */
    public function update(Request $request, $questionKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        $questionTypeId = QuestionType::whereQuestionTypeKey($request->json('question_type_key'))->firstOrFail()->id;

        try {
            $translationsOld=[];
            $translationsNew=[];

            $question = Question::whereQuestionKey($questionKey)->firstOrFail();
            $translationsId = $question->questionTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            $question->question_type_id         = $questionTypeId;
            $question->mandatory                = $request->json('mandatory');
            $question->position                 = $request->json('position');
            if( $request->json('reuse_question_options') != NULL ){
                $question->reuse_question_options   = $request->json('reuse_question_options');
            }

            foreach ($request->json('translations') as $translation){
                if(isset($translation['language_code']) && isset($translation['question'])){
                    $questionTranslation = $question->questionTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if(empty($questionTranslation)){
                        $questionTranslation = $question->questionTranslations()->create(
                            [
                                'question' => $translation['question'],
                                'description' => empty($translation['description']) ? " " : $translation['description'],
                                'language_code' => $translation['language_code']
                            ]
                        );
                    }
                    else{
                        $questionTranslation->question = $translation['question'];
                        $questionTranslation->description = empty($translation['description']) ? "" : $translation['description'];
                        $questionTranslation->save();
                    }
                    $translationsNew[] = $questionTranslation->id;
                }
            }

            $deleteTranslations = array_diff($translationsOld,$translationsNew);
            foreach($deleteTranslations as $deleteTranslation){
                $deleteId = $question->questionTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            $question->correctOption = "";

            $question->save();

            return response()->json($question, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question'.$e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="questionDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/question/{question_key}",
     *  summary="Delete Question Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Method"},
     *
     * @SWG\Parameter(
     *      name="question_key",
     *      in="path",
     *      description="Question Key",
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
     *      @SWG\Schema(ref="#/definitions/questionDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question not Found",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Question",
     *      @SWG\Schema(ref="#/definitions/questionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Question
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $questionKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $question = Question::whereQuestionKey($questionKey)->firstOrFail();
            $question->delete();
            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Question'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request of all Question Options from a Question
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $questionId
     */
    public function questionOptionList(Request $request, $questionKey)
    {
        try {
            $question = Question::whereQuestionKey($questionKey)->firstOrFail();
            $questionOptions = $question->questionOptions()->with("questionOptionTranslations")->orderBy('position', 'asc')->get();
            foreach($questionOptions as $questionOption){
                if (!($questionOption->translation($request->header('LANG-CODE')))) {
                    if (!$questionOption->translation($request->header('LANG-CODE-DEFAULT'))){
                        $questionOptionTranslation = $questionOption->translations()->first();
                        if(!empty($questionOptionTranslation)){
                            $questionOption->translation($questionOptionTranslation->language_code);
                        }
                    }
                }
            }
            return response()->json($questionOptions, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Questions List'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update a positions from Questions
     *
     * @param Request $request
     * @return mixed
     */
    public function updatePosition(Request $request)
    {
        ONE::verifyToken($request);
        $keysRequired = ["data"];

        ONE::verifyKeysRequest($keysRequired, $request);

        try {
            $arrayQuestions = $request->json('data');
            foreach ($arrayQuestions as $arrayQuestion) {
                if (isset($arrayQuestion['key']) && isset($arrayQuestion['position'])) {
                    Question::whereQuestionKey($arrayQuestion['key'])->update(array('position' => $arrayQuestion['position']));
                }
            }
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function changeGroup(Request $request)
    {
        ONE::verifyToken($request);
        $keysRequired = ["data", "question_key", "question_group_key"];

        ONE::verifyKeysRequest($keysRequired, $request);

        try {
            $question = Question::whereQuestionKey($request->json('question_key'))->firstOrFail();
            $originQuestionGroup = $question->questionGroup()->firstOrFail();

            $destinyQuestionGroup = QuestionGroup::whereQuestionGroupKey($request->json('question_group_key'))->firstOrFail();

            $question->update(['question_group_id' => $destinyQuestionGroup->id]);
            $questionGroupQuestions = $originQuestionGroup->questions()->orderBy('position')->get();

            $position = 1;
            foreach ($questionGroupQuestions as $questionGroupQuestion) {
                $questionGroupQuestion->update(['position' => $position]);
                $position++;
            }

            $arrayQuestions = $request->json('data');
            foreach ($arrayQuestions as $arrayQuestion) {
                if (isset($arrayQuestion['key']) && isset($arrayQuestion['position'])) {
                    Question::whereQuestionKey($arrayQuestion['key'])->update(array('position' => $arrayQuestion['position']));
                }
            }
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestionDependencies(Request $request, $questionKey)
    {
        ONE::verifyToken($request);

        try {
            $question = Question::whereQuestionKey($questionKey)->firstOrFail();
            $form = $question->questionGroup->form->first();
            $dependencies = Question::whereQuestionGroupId($question->question_group_id)->where('position', '>', $question->position)
                ->orderby('position', 'asc')
                ->get()
                ->toArray();

            $questionGroups = QuestionGroup::where('id','>',$question->question_group_id)->whereFormId($form->id)->get();

            foreach ($questionGroups as $questionGroup){
                $dependencies = array_merge($dependencies,$questionGroup->questions()->get()->toArray());
            }

            return response()->json($dependencies, 200);
        } catch
        (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Dependencies not Found'], 404);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to retrieve Questions Dependencies'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function reuse(Request $request, $questionKey)
    {
        ONE::verifyToken($request);

        try {
            $question = Question::whereQuestionKey($questionKey)->firstOrFail();
            if($question->reuse_question_options){
                $question->reuse_question_options  = false;
            } else {
                $question->reuse_question_options  = true;
            }

            $question->save();

            return response()->json($question, 200);
        } catch
        (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReuseOptions(Request $request, $formKey)
    {
        ONE::verifyToken($request);

        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();

            $questions = $form->questionsThroughQuestionGroups()->whereReuseQuestionOptions(true)->get();

            $questionOptions = [];
            foreach ($questions as $question){
                $questionOptions[$question->question_key] = $question->questionOptions()->get();
            }

            return response()->json(['data' => $questionOptions], 200);
        } catch
        (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Options'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
