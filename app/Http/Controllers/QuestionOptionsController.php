<?php

namespace App\Http\Controllers;

use App\Dependency;
use App\Icon;
use App\One\One;
use App\Question;
use App\QuestionGroup;
use App\QuestionOption;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuestionOptionsController extends Controller
{
    protected $keysRequired = [
        'question_key'
    ];

    /**
     * @SWG\Tag(
     *   name="Question Option Method",
     *   description="Everything about Forms Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="questionOptionsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="questionOptionsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="question_option_key", format="string", type="string"),
     *           @SWG\Property(property="icon_id", format="string", type="string"),
     *           @SWG\Property(property="question_id", format="string", type="string"),
     *           @SWG\Property(property="label", format="string", type="string"),
     *           @SWG\Property(property="file_id", format="string", type="string"),
     *           @SWG\Property(property="file_code", format="string", type="string"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Question Options
     * Returns the list of all Questions Options
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $questionOptions = QuestionOption::all();
            return response()->json($questionOptions, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Options list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/questionOption/{question_option_key}",
     *  summary="Shows a Question Option Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Option Method"},
     *
     * @SWG\Parameter(
     *      name="question_option_key",
     *      in="path",
     *      description="Question Option Method Key",
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
     *      description="Shows the Question Option data",
     *      @SWG\Schema(ref="#/definitions/questionOptionsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Option not Found",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * @param Request $request
     * @param $questionOptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $questionOptionKey)
    {
        try{
            $questionOption = QuestionOption::with(["question.questionType", "icon", "dependencies"])->whereQuestionOptionKey($questionOptionKey)->firstOrFail()->toArray();
            $questionOption['list_dependencies'] = Question::whereId($questionOption['question_id'])->get();
            return response()->json($questionOption, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Option not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Option'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="questionOptionsCreate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"icon_key", "question_key", "label"},
     *           @SWG\Property(property="question_key", format="string", type="string"),
     *           @SWG\Property(property="icon_key", format="string", type="string"),
     *           @SWG\Property(property="label", format="string", type="string")
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/questionOption",
     *  summary="Create a Question Option Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Option Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Option",
     *      in="body",
     *      description="Question Option Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionOptionsCreate")
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
     *      description="The newly created Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store a new Question Option in the database
     * Return the Attributes of the Question Option created
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try {
            do {
                $rand = str_random(32);
                if (!($exists = QuestionOption::whereQuestionOptionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $icon = Icon::whereIconKey($request->json('icon_key'))->first();
            $question = Question::whereQuestionKey($request->json('question_key'))->firstOrFail();

            $lastOption = $question->questionOptions()->orderBy('position', 'desc')->first();
            $position = is_null($lastOption) ? 1: ($lastOption->position)+1;

            $questionOption = $question->questionOptions()->create(
                [
                    'question_option_key' => $key,
                    'icon_id' => isset($icon) ? $icon->id : '',
                    'position' => $position
                ]
            );

            if($request->has('correctOption') && !empty($request->has('correctOption'))){
                $aux = $question->correctOption ?? "";
                if(!empty($aux))
                    $question->correctOption = $aux . "," . $questionOption->id;
                else
                    $question->correctOption = $questionOption->id;

                $question->save();
            }

            foreach ($request->json('translations') as $translation){
                if(isset($translation['label'])){
                    $questionOption->questionOptionTranslations()->create(
                        [
                            'label' => $translation['label'],
                            'language_code' => $translation['language_code']
                        ]
                    );
                }
            }

            $dependencies = $request->json('dependencies');

            if (!empty($dependencies)) {
                foreach ($dependencies as $dependency) {
                    do {
                        $rand = str_random(32);
                        if (!($exists = Dependency::whereDependencyKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $questionOption->dependencies()->create(
                        [
                            'dependency_key' => $key,
                            'question_key' => $dependency
                        ]
                    );
                }
            }

            $questionOption = QuestionOption::with('dependencies')->findOrFail($questionOption->id);

            return response()->json($questionOption, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Question Option'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *
     *  @SWG\Definition(
     *   definition="questionOptionsUpdate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"icon_key", "question_key", "label"},
     *           @SWG\Property(property="question_key", format="string", type="string"),
     *           @SWG\Property(property="icon_key", format="string", type="string"),
     *           @SWG\Property(property="label", format="string", type="string"),
     *           @SWG\Property(property="dependencies", type="array", @SWG\Items(type="string"))
     *       )
     *   }
     * )
     *
     * @SWG\Put(
     *  path="/questionOption/{question_option_key}",
     *  summary="Update a Question Option Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Option Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Option",
     *      in="body",
     *      description="Question Option Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionOptionsUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="question_option_key",
     *      in="path",
     *      description="Question Option Key",
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
     *      description="The Updated Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Option not Found",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Question Option
     * Return the Attributes of the Question Option Updated
     * @param Request $request
     * @param $questionOptionKey
     * @return mixed
     * @internal param $id
     */
    public function update(Request $request, $questionOptionKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $icon = Icon::whereIconKey($request->json('icon_key'))->first();
            if($request->has('question_key'))
                $question = Question::whereQuestionKey($request->json('question_key'))->firstOrFail();

            $questionOption = QuestionOption::whereQuestionOptionKey($questionOptionKey)->firstOrFail();
            $translationsOld=[];
            $translationsNew=[];

            $translationsId = $questionOption->questionOptionTranslations()->get();
            foreach($translationsId as $translationId){
                $translationsOld[] = $translationId;
            }

            $questionOption->icon_id    = isset($icon) ? $icon->id : '';

            foreach ($request->json('translations') as $translation){
                if(isset($translation['language_code']) && isset($translation['label'])){
                    $questionOptionTranslation = $questionOption->questionOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if(empty($questionOptionTranslation)){
                        $questionOptionTranslation = $questionOption->questionOptionTranslations()->create(
                            [
                                'label' => $translation['label'],
                                'language_code' => $translation['language_code']
                            ]
                        );
                    } else {
                        $questionOptionTranslation->label = $translation['label'];
                        $questionOptionTranslation->save();
                    }
                    $translationsNew[]=$questionOptionTranslation->id;
                }
            }

            $deleteTranslations = array_diff($translationsOld,$translationsNew);
            foreach ($deleteTranslations as $deleteTranslation){
                $deleteId = $questionOption->questionOptionTranslations()->whereId($deleteTranslation)->first();
                if(!empty($deleteId))
                    $deleteId->delete();
            }

            $questionOption->save();

            if($request->has('correctOption') && !empty($request->has('correctOption'))){
                if($request->has('question_key')){
                    $aux = $question->correctOption ?? "";
                    if(!empty($aux))
                        $question->correctOption = $aux . "," . $questionOption->id;
                    else
                        $question->correctOption = $questionOption->id;

                    $question->save();
                }
            }

            $dependencies = $request->json('dependencies');

            if (!empty($dependencies)) {
                $newDependencies = [];
                $oldDependencies = [];

                $dependenciesOld = $questionOption->dependencies()->get();
                foreach ($dependenciesOld as $dependencyOld){
                    $oldDependencies[] = $dependencyOld->id;
                }

                foreach ($dependencies as $dependency) {

                    if ($questionOption->dependencies()->whereQuestionKey($dependency)->exists()){
                        $newDependency = $questionOption->dependencies()->whereQuestionKey($dependency)->first();
                        $newDependencies[] = $newDependency->id;
                    } else {
                        do {
                            $rand = str_random(32);
                            if (!($exists = Dependency::whereDependencyKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $newDependency = $questionOption->dependencies()->create(
                            [
                                'dependency_key' => $key,
                                'question_key' => $dependency
                            ]
                        );
                        $newDependencies[] = $newDependency->id;
                    }
                }

                $deleteDependencies = array_diff($oldDependencies, $newDependencies);

                foreach ($deleteDependencies as $deleteDependency) {
                    $deleteId = $questionOption->dependencies()->whereId($deleteDependency)->first();
                    $deleteId->delete();
                }
            } else {
                $questionOption->dependencies()->delete();
            }

            return response()->json($questionOption, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Option not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question Option: '.$e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="questionOptionDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/questionOption/{question_option_key}",
     *  summary="Delete Question Option Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Option Method"},
     *
     * @SWG\Parameter(
     *      name="question_option_key",
     *      in="path",
     *      description="Question Option Key",
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
     *      @SWG\Schema(ref="#/definitions/questionOptionDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Option not Found",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Question Option",
     *      @SWG\Schema(ref="#/definitions/questionOptionsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Question Option
     * @param Request $request
     * @param $questionOptionKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $questionOptionKey)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $questionOption = QuestionOption::whereQuestionOptionKey($questionOptionKey)->firstOrFail();
            $questionOption->delete();
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Question Option'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicateReuseOptions(Request $request)
    {
        ONE::verifyToken($request);

        try {
            $question = Question::whereQuestionKey($request->json('question_key'))->firstOrFail();
            $questionToDuplicate = Question::whereQuestionKey($request->json('reuse_question_key'))->firstOrFail();

            $questionOptions = $questionToDuplicate->questionOptions()->get();

            if(!is_null($questionOptions)){
                foreach ($questionOptions as $questionOption){

                    do {
                        $rand = str_random(32);
                        if (!($exists = QuestionOption::whereQuestionOptionKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $option = $question->questionOptions()->create(
                        [
                            'question_option_key' => $key,
                            'label' => $questionOption->label,
                            'icon_id' => ''
                        ]
                    );
                }
            }

            return response()->json($question, 201);
        } catch
        (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to duplicate Question Options'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePosition(Request $request)
    {
        ONE::verifyToken($request);
        $keysRequired = ["data"];

        ONE::verifyKeysRequest($keysRequired, $request);

        try {
            $QuestionOptions = $request->json('data');
            foreach ($QuestionOptions as $QuestionOption) {
                if (isset($QuestionOption['question_option_key']) && isset($QuestionOption['position'])) {
                    QuestionOption::whereQuestionOptionKey($QuestionOption['question_option_key'])->update(array('position' => $QuestionOption['position']));
                }
            }
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
