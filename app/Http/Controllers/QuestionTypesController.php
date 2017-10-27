<?php

namespace App\Http\Controllers;

use App\One\One;
use App\QuestionType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuestionTypesController extends Controller
{
    protected $keysRequired = [
        'name'
    ];

    /**
     * @SWG\Tag(
     *   name="Question Type Method",
     *   description="Everything about Question Types Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="questionTypesMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="questionTypesReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="question_type_key", format="string", type="string"),
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Question Types
     * Returns the list of all Questions Types
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $questionTypes = QuestionType::all();
            return response()->json($questionTypes, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Types list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/questionType/{question_type_key}",
     *  summary="Shows a Question Type Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Type Method"},
     *
     * @SWG\Parameter(
     *      name="question_type_key",
     *      in="path",
     *      description="Question Type Method Key",
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
     *      description="Shows the Question Type data",
     *      @SWG\Schema(ref="#/definitions/questionTypesReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Type not Found",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Question Type
     * Returns the attributes of the Question Type
     * @param Request $request
     * @param $questionTypeKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show(Request $request, $questionTypeKey)
    {
        try{
            $questionType = QuestionType::whereQuestionTypeKey($questionTypeKey)->first();
            return response()->json($questionType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Groups not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Question Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="questionTypesCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"name"},
     *           @SWG\Property(property="name", format="string", type="string")
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/questionType",
     *  summary="Create a Question Type Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Type Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Type",
     *      in="body",
     *      description="Question Type Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionTypesCreate")
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
     *      description="The newly created Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Store a new Question Type in the database
     * Return the Attributes of the Question Type created
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
                if (!($exists = QuestionType::whereQuestionTypeKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $questionType= QuestionType::create(
                [
                    'question_type_key' => $key,
                ]
            );

            foreach ($request->json('translations') as $translation){
                if(isset($translation['name'])){
                    $questionType->questionTypeTranslations()->create(
                        [
                            'name' => $translation['name']
                        ]
                    );
                }
            }

            return response()->json($questionType, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Question Option'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/questionType/{question_type_key}",
     *  summary="Update a Question Type Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Type Method"},
     *
     *  @SWG\Parameter(
     *      name="Question Type",
     *      in="body",
     *      description="Question Type Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/questionTypesCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="question_type_key",
     *      in="path",
     *      description="Question Type Key",
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
     *      description="The Updated Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Type not Found",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Question Type
     * Return the Attributes of the Question Type Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $questionTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        //VERIFY PERMISSIONS

        try{
            $questionType = QuestionType::whereQuestionTypeKey($questionTypeKey)->first();
            $translationsOld=[];
            $translationsNew=[];

            $translationsId = $questionType->questionTypeTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach ($request->json('translations') as $translation){
                if(isset($translation['language_code']) && isset($translation['name'])) {
                    $questionTypeTranslation = $questionType->questionTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($questionTypeTranslation)) {
                        $questionTypeTranslation = $questionType->questionTypeTranslations()->create(
                            [
                                'name' => $translation['name'],
                                'language_code' => $translation['language_code']
                            ]
                        );
                    } else {
                        $questionTypeTranslation->name = $translation['name'];
                        $questionTypeTranslation->save();
                    }
                }
            }

            $deleteTranslations = array_diff($translationsOld,$translationsNew);
            foreach ($deleteTranslations as $deleteTranslation){
                $deleteId = $questionType->questionTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            $questionType->save();

            return response()->json($questionType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Question Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="questionTypeDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/questionType/{question_type_key}",
     *  summary="Delete Question Type Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Question Type Method"},
     *
     * @SWG\Parameter(
     *      name="question_type_key",
     *      in="path",
     *      description="Question Type Key",
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
     *      @SWG\Schema(ref="#/definitions/questionTypeDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Question Type not Found",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Question Type",
     *      @SWG\Schema(ref="#/definitions/questionTypesMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Question Type
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $questionTypeKey)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $questionType = QuestionType::whereQuestionTypeKey($questionTypeKey)->first();
            $questionType->delete();
            return response()->json('Ok', 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Question Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Question Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
