<?php

namespace App\Http\Controllers;

use App\Form;
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

class QuestionGroupsController extends Controller
{
    protected $keysRequired = [];

    /**
     * @SWG\Tag(
     *   name="Question Group Method",
     *   description="Everything about Question Groups Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="questionGroupsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="questionGroupsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="question_group_key", format="string", type="string"),
     *           @SWG\Property(property="form_id", format="integer", type="integer"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Question Groups
     * Returns the list of all Questions Groups
     * @param Request $request
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $questionGroups = QuestionGroup::all();
            return response()->json($questionGroups, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Groups list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/questionGroup/{question_group_key}",
     *  summary="Shows a Question Group Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Group Method"},
     *
     * @SWG\Parameter(
     *      name="question_group_key",
     *      in="path",
     *      description="Question Group Method Key",
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
     *      description="Shows the Question Group data",
     *      @SWG\Schema(ref="#/definitions/questionGroupsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Group not Found",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Question Group
     * Returns the attributes of the Question Group
     * @param Request $request
     * @param $questionGroupKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show(Request $request, $questionGroupKey)
    {

        try{
            $questionGroup = QuestionGroup::with('form')->whereQuestionGroupKey($questionGroupKey)->first();

            //Get title from Forms Translation
            if(!($questionGroup->translation($request->header('LANG-CODE')))) {
                if (!$questionGroup->translation($request->header('LANG-CODE-DEFAULT'))) {
                    // return response()->json(['error' => 'No translation found'], 404);
                }
            }

            $questionGroupTranslations = $questionGroup->questionGroupTranslations()->get();
            $questionGroup['translations'] = $questionGroupTranslations;

            return response()->json($questionGroup, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Group'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="questionGroupsCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"title", "description", "form_key"},
     *           @SWG\Property(property="form_key", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string")
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/questionGroup",
     *  summary="Create a Question Group Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Group Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Group",
     *      in="body",
     *      description="Question Group Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionGroupsCreate")
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
     *      description="The newly created Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Store a new Question Group in the database
     * Return the Attributes of the Question Group created
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            do {
                $rand = str_random(32);
                if (!($exists = QuestionGroup::whereQuestionGroupKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            try{
                $form = Form::whereFormKey($request->json('form_key'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Form not Found'], 404);
            }

            $questionGroup = QuestionGroup::whereFormId($form->id)
                ->orderBy('position', 'desc')
                ->first();

            $lastPosition = !is_null($questionGroup) ? $questionGroup->position+1 : '0';

            $questionGroup = QuestionGroup::create(
                [
                    'question_group_key'   => $key,
                    'form_id'               => $form->id,
                    'position'              => $lastPosition
                ]
            );

            foreach ($request->json('translations') as $translation){
                if(isset($translation['title']) && isset($translation['description'])){
                    $questionGroup->questionGroupTranslations()->create(
                        [
                            'title' => $translation['title'],
                            'description' => empty($translation['description']) ? "" : $translation['description'],
                            'language_code' => $translation['language_code']
                        ]
                    );
                }
            }

            return response()->json($questionGroup, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Question Group'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     *
     *  @SWG\Definition(
     *   definition="questionGroupsUpdate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"title", "description", "form_key"},
     *           @SWG\Property(property="form_key", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="position", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     * @SWG\Put(
     *  path="/questionGroup/{question_group_key}",
     *  summary="Update a Question Group Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Group Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Group",
     *      in="body",
     *      description="Question Group Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionGroupsUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="question_group_key",
     *      in="path",
     *      description="Question Group Key",
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
     *      description="The Updated Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Group not Found",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Question Group
     * Return the Attributes of the Question Group Updated
     * @param Request $request
     * @param $questionGroupKey
     * @return mixed
     * @internal param $id
     */
    public function update(Request $request, $questionGroupKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $questionGroup = QuestionGroup::whereQuestionGroupKey($questionGroupKey)->first();
            $translationsOld=[];
            $translationsNew=[];

            $translationsId = $questionGroup->questionGroupTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            $questionGroup->position    = $request->json('position');

            foreach ($request->json('translations') as $translation){
                if(isset($translation['language_code']) && isset($translation['title'])){
                    $questionGroupTranslation = $questionGroup->questionGroupTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if(empty($questionGroupTranslation)){
                        $questionGroupTranslation = $questionGroup->questionGroupTranslations()->create(
                            [
                                'title' => $translation['title'],
                                'description' => empty($translation['description']) ? "" : $translation['description'],
                                'language_code' => $translation['language_code']
                            ]
                        );
                    }
                    else{
                        $questionGroupTranslation->title = $translation['title'];
                        $questionGroupTranslation->description = empty($translation['description']) ? "" : $translation['description'];
                        $questionGroupTranslation->save();
                    }
                }
                $translationsNew[]=$questionGroupTranslation->id;
            }

            $deleteTranslations =  array_diff($translationsOld,$translationsNew);
            foreach ($deleteTranslations as $deleteTranslation){
                $deleteId = $questionGroup->questionGrouptranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            $questionGroup->save();

            return response()->json($questionGroup, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question Group'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="questionGroupDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/questionGroup/{question_group_key}",
     *  summary="Delete Question Group Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Group Method"},
     *
     * @SWG\Parameter(
     *      name="question_group_key",
     *      in="path",
     *      description="Question Group Key",
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
     *      @SWG\Schema(ref="#/definitions/questionGroupDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Group not Found",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Question Group",
     *      @SWG\Schema(ref="#/definitions/questionGroupsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Question Group
     * @param Request $request
     * @param $questionGroupKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $questionGroupKey)
    {
        ONE::verifyToken($request);

        try{
            $questionGroup = QuestionGroup::whereQuestionGroupKey($questionGroupKey)->first();
            $questionGroup->delete();
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Question Group'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the existing Question from Question Group
     * @param Request $request
     * @param $questionGroupKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function questionList(Request $request, $questionGroupKey)
    {
        try{
            $questionGroup = QuestionGroup::whereQuestionGroupKey($questionGroupKey)->first();
            $questions = $questionGroup->questions()->orderBy('position', 'asc')->get();
            foreach($questions as $question){
                if (!($question->translation($request->header('LANG-CODE')))) {
                    if (!$question->translation($request->header('LANG-CODE-DEFAULT'))){
                        $questionTranslation = $question->translations()->first();
                        if(!empty($questionTranslation)){
                            $question->translation($questionTranslation->language_code);
                        }
                    }
                }
            }
            return response()->json($questions, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Questions list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the existing Question and Question Option from Question Group
     * @param Request $request
     * @param $questionGroupKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function questionInfo(Request $request, $questionGroupKey)
    {
        try{
            $questionGroup = QuestionGroup::whereQuestionGroupKey($questionGroupKey)->first();
            $question = $questionGroup->questions()->with(['questionOptions'])->orderBy('position', 'asc')->get();
            return response()->json($question, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Questions List'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update a positions from Question Groups
     *
     * @param Request $request
     * @return mixed
     */
    public function updatePosition(Request $request)
    {
        ONE::verifyToken($request);
        $keysRequired = ["data"];

        ONE::verifyKeysRequest($keysRequired, $request);

        try{
            $arrayQuestionGroups = $request->json('data');
            foreach($arrayQuestionGroups as $arrayQuestionGroup ){
                if(isset($arrayQuestionGroup['question_group_key']) && isset($arrayQuestionGroup['position']) ){
                    QuestionGroup::whereQuestionGroupKey($arrayQuestionGroup['question_group_key'])->update(array('position' => $arrayQuestionGroup['position']));
                }
            }

            return response()->json('OK', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question Group'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    /**
     * @param Request $request
     * @param $questionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestionDependencies(Request $request, $questionGroupKey)
    {
        ONE::verifyToken($request);

        try {
            $questionGroup = QuestionGroup::with('form')->whereQuestionGroupKey($questionGroupKey)->first();
            $form = $questionGroup->form->first();
            
            $dependencies = Question::whereQuestionGroupId($questionGroup->id)
                ->orderby('position', 'asc')
                ->get()
                ->toArray();

            $questionGroups = QuestionGroup::where('id','>',$questionGroup->id)->whereFormId($form->id)->get();

            foreach ($questionGroups as $questionGroup){
                $dependencies = array_merge($dependencies, $questionGroup->questions()->get()->toArray());
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
}
