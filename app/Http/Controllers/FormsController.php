<?php

namespace App\Http\Controllers;

use App\Form;
use App\FormConfiguration;
use App\FormReply;
use App\FormReplyAnswer;
use App\One\One;
use App\Question;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FormsController extends Controller
{
    protected $keysRequired = [
        'public',
        'start_date'
    ];

    /**
     * @SWG\Tag(
     *   name="Form Method",
     *   description="Everything about Forms Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="formsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="formsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="form_key", format="string", type="string"),
     *           @SWG\Property(property="entity_key", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="public", format="integer", type="integer"),
     *           @SWG\Property(property="created_by", format="string", type="string"),
     *           @SWG\Property(property="start_date", format="date", type="string"),
     *           @SWG\Property(property="end_date", format="date", type="string"),
     *           @SWG\Property(property="link", format="string", type="string"),
     *           @SWG\Property(property="file_id", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     *
     *
     *
     *
     */

    /**
     * Request list of all Forms
     * Returns the list of all Forms
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $forms = Form::whereEntityKey($request->header('X-ENTITY-KEY'))->get();

            foreach ($forms as $key => $form) {

                $formConfigurations = $form->formConfigurations()->get();

                if (!empty($formConfigurations)) {
                    foreach ($formConfigurations as $formConfiguration) {
                        if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
                            if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                        $formConfiguration['value'] = $formConfiguration->pivot->value;
                        $formConfiguration = array_except($formConfiguration, 'pivot');
                    }
                    $form['form_configurations'] = $formConfigurations;
                }

                if (!($form->translation($request->header('LANG-CODE')))) {
                    if (!$form->translation($request->header('LANG-CODE-DEFAULT'))){
                        $formTranslation = $form->formTranslations()->first();

                        if(!empty($formTranslation)){
                            $form->translation($formTranslation->language_code);
                        }
                    }
                }
            }
            return response()->json(['data' => $forms], 200);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to retrieve the Forms list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/form/{form_key}",
     *  summary="Shows a Form Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Method"},
     *
     * @SWG\Parameter(
     *      name="form_key",
     *      in="path",
     *      description="Form Method Key",
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
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE",
     *      in="header",
     *      description="Entity Key",
     *      required=false,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE-DEFAULT",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Shows the Form data",
     *      @SWG\Schema(ref="#/definitions/formsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form not Found",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Form",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Form
     * Returns the attributes of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show(Request $request, $formKey)
    {
        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();
            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $formConfigurations = $form->formConfigurations()->get();
                foreach ($formConfigurations as $formConfiguration) {
                    if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
                        if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                    $formConfiguration['value'] = $formConfiguration->pivot->value;
                    $formConfiguration = array_except($formConfiguration, 'pivot');
                }

                $form['form_configurations'] = $formConfigurations;

                $formTranslations = $form->formTranslations()->get();
                if (!($form->translation($request->header('LANG-CODE')))) {
                    if (!$form->translation($request->header('LANG-CODE-DEFAULT'))) {
                        // return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }
            $form['translations'] = $formTranslations;
            return response()->json($form, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Form'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="formsCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"title", "description", "public", "start_date","end_date","link","file_id","configurations"},
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string"),
     *           @SWG\Property(property="public", format="integer", type="integer"),
     *           @SWG\Property(property="start_date", format="date", type="string"),
     *           @SWG\Property(property="end_date", format="date", type="string"),
     *           @SWG\Property(property="link", format="string", type="string"),
     *           @SWG\Property(property="file_id", format="string", type="string"),
     *           @SWG\Property(
     *              property="configurations",
     *              type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="form_configuration_key", format="string", type="string"),
     *                          @SWG\Property(property="value", format="string", type="string")
     *                  )
     *           ),
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/form",
     *  summary="Create a Form Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Method"},
     *
     *  @SWG\Parameter(
     *      name="Form",
     *      in="body",
     *      description="Form Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formsCreate")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
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
     *      response=201,
     *      description="The newly created Form",
     *      @SWG\Schema(ref="#/definitions/formsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Form",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store a new Form in the database
     * Return the Attributes of the Form created
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {

        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        $entityKey = $request->header('X-ENTITY-KEY');

        if ($entityKey) {
            try {
                do {
                    $rand = str_random(32);
                    if (!($exists = Form::whereFormKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $form = Form::create(
                    [
                        'form_key' => $key,
                        'entity_key' => $entityKey,
                        'public' => $request->json('public'),
                        'created_by' => $userKey,
                        'start_date' => $request->json('start_date'),
                        'end_date' => $request->json('end_date'),
                        'link' => $request->json('link'),
                        'file_id' => $request->json('file_id')
                    ]
                );

                foreach($request->json('translations') as $f_translation){
                    if(isset($f_translation['title']) && isset($f_translation['description'])){
                        $form->formTranslations()->create(
                            [
                                'title' => $f_translation['title'],
                                'description' => empty($f_translation['description']) ? "" : $f_translation['description'],
                                'language_code' => $f_translation['language_code']
                            ]
                        );

                    }
                }

                if (!empty($request->json('configurations'))) {
                    $formConfigurations = $request->json('configurations');
                    foreach ($formConfigurations as $formConfiguration) {
                        $form->formConfigurations()->attach(FormConfiguration::whereFormConfigurationKey($formConfiguration['form_configuration_key'])->firstOrFail()->id, ['value' => $formConfiguration['value']]);
                    }
                }

                return response()->json($form, 201);

            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *
     * @SWG\Put(
     *  path="/form/{form_key}",
     *  summary="Update a Form Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Method"},
     *
     *  @SWG\Parameter(
     *      name="Form",
     *      in="body",
     *      description="Form Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formsCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="form_key",
     *      in="path",
     *      description="Form Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
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
     *      description="The Updated Form",
     *      @SWG\Schema(ref="#/definitions/formsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form not Found",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Form",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Form
     * Return the Attributes of the Form Updated
     * @param Request $request
     * @param $formKey
     * @return mixed
     * @internal param $id
     */
    public function update(Request $request, $formKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();
            $translationsOld=[];
            $translationsNew=[];


            $translationsId = $form->formTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $form->public = $request->json('public');
                $form->start_date = $request->json('start_date');
                $form->end_date = $request->json('end_date');
                $form->link = $request->json('link');
                $form->file_id = $request->json('file_id');


                foreach($request->json('translations') as $translation){
                    if(isset($translation['language_code']) && isset($translation['title'])){
                        $formTranslation = $form->formTranslations()->whereLanguageCode($translation['language_code'])->first();
                        if(empty($formTranslation)){
                            $formTranslation = $form->formTranslations()->create(
                                [
                                    'title' => $translation['title'],
                                    'description' => empty($translation['description']) ? "" : $translation['description'],
                                    'language_code' => $translation['language_code']
                                ]
                            );
                        }
                        else{
                            $formTranslation->title = $translation['title'];
                            $formTranslation->description = empty($translation['description']) ? "" : $translation['description'];
                            $formTranslation->save();
                        }
                    }
                    $translationsNew[] = $formTranslation->id;
                }

                $deleteTranslations = array_diff($translationsOld,$translationsNew);
                foreach ($deleteTranslations as $deleteTranslation){
                    $deleteId = $form->formTranslations()->whereId($deleteTranslation)->first();
                    $deleteId->delete();
                }

                $form->save();

                if (!empty($request->json('configurations'))) {
                    $formConfigurationId = [];
                    $formConfigurations = $request->json('configurations');
                    foreach ($formConfigurations as $formConfiguration) {
                        $id = FormConfiguration::whereFormConfigurationKey($formConfiguration['form_configuration_key'])->firstOrFail()->id;
                        $formConfigurationId[$id]['value'] = $formConfiguration['value'];
                    }
                    $form->formConfigurations()->sync($formConfigurationId);
                }

                return response()->json($form, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Form'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="formDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/form/{form_key}",
     *  summary="Delete Form Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Method"},
     *
     * @SWG\Parameter(
     *      name="form_key",
     *      in="path",
     *      description="Form Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
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
     *      @SWG\Schema(ref="#/definitions/formDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form not Found",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Form",
     *      @SWG\Schema(ref="#/definitions/formsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $formKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();
            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $formConfigurations = $form->formConfigurations()->get();

                if (!empty($formConfigurations)) {
                    foreach ($formConfigurations as $formConfiguration) {
                        $form->formConfigurations()->detach($formConfiguration->id);
                    }
                }
                $form->delete();
                return response()->json('Ok', 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Form'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function questionGroupList(Request $request, $formKey)
    {
        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $questionGroups = $form->questionGroups()->orderBy('position', 'asc')->get();
                foreach($questionGroups as $questionGroup){

                    // Group translations
                    if (!($questionGroup->translation($request->header('LANG-CODE')))) {
                        if (!$questionGroup->translation($request->header('LANG-CODE-DEFAULT'))){
                            $questionGroupTranslation = $questionGroup->translations()->first();
                            if(!empty($questionGroupTranslation)){
                                $questionGroup->translation($questionGroupTranslation->language_code);
                            }
                        }
                    }

                    // Question translations
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

                    $questionGroup['questions'] = $questions;
                }


                return response()->json(["data" => $questionGroups], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     *
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function getQuestionnaire(Request $request, $formKey)
    {
        try {
            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions',
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            //Get the translation sent in the request
            if(!($form->translation($request->header('LANG_CODE')))){
                if(!($form->translation($request->header('LANG_CODE_DEFAULT')))){
                    //  return response()->json(['error' => 'No translation found'], 404);
                }
            }
            foreach($form->questionGroups as $questionGroup){
                if(!($questionGroup->translation($request->header('LANG_CODE')))){
                    if(!($questionGroup->translation($request->header('LANG_CODE_DEFAULT')))){
                        //   return response()->json(['error' => 'No translation found'], 404);
                    }
                }
                foreach ($questionGroup->questions as $question){
                    if(!($question->translation($request->header('LANG_CODE')))){
                        if(!($question->translation($request->header('LANG_CODE_DEFAULT')))){
                            //    return response()->json(['error' => 'No translation found'], 404);
                        }
                    }

                    foreach ($question->questionOptions as $questionOption){
                        if(!($questionOption->translation($request->header('LANG_CODE')))){
                            if(!($questionOption->translation($request->header('LANG_CODE_DEFAULT')))){
                                //    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }
            }

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
                return response()->json($form, 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     *
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function getAnswers(Request $request, $formKey)
    {
        try {
            $form = Form::with([
                'formReplies'  => function ($query) {$query->whereCompleted(1);},
                'formReplies.formReplyAnswers',

            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
                return response()->json($form, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function formConstruction(Request $request, $formKey)
    {
        try {

            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions'          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
//            $formConfigurations = $form->formConfigurations()->get();

//            foreach ($formConfigurations as $formConfiguration) {
//                if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
//                    if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
//                        return response()->json(['error' => 'No translation found'], 404);
//                }
//                $formConfiguration['value'] = $formConfiguration->pivot->value;
//                $formConfiguration = array_except($formConfiguration, 'pivot');
//            }

                /*
                 * Verify if the Form can be used by anonymous
                 */
                if (!$form->public && empty(ONE::verifyLogin($request))) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } elseif (!$form->public) {
                    $userKey = ONE::verifyLogin($request);
                    $formReply = FormReply::whereCreatedBy($userKey)->whereFormId($form->id)->first();
                } else {
                    $formReply = null;
                }

                //Get the translation sent in the request
                if(!($form->translation($request->header('LANG_CODE')))){
                    if(!($form->translation($request->header('LANG_CODE_DEFAULT')))){
                        //  return response()->json(['error' => 'No translation found'], 404);
                    }
                }
                foreach($form->questionGroups as $questionGroup){
                    if(!($questionGroup->translation($request->header('LANG_CODE')))){
                        if(!($questionGroup->translation($request->header('LANG_CODE_DEFAULT')))){
                            //   return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                    foreach ($questionGroup->questions as $question){
                        if(!($question->translation($request->header('LANG_CODE')))){
                            if(!($question->translation($request->header('LANG_CODE_DEFAULT')))){
                                //    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }

                        foreach ($question->questionOptions as $questionOption){
                            if(!($questionOption->translation($request->header('LANG_CODE')))){
                                if(!($questionOption->translation($request->header('LANG_CODE_DEFAULT')))){
                                    //    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                    }
                }



                $data = $form->toArray();
                $dependencies = array();
                foreach ($data['question_groups'] as &$qGroup) {
                    foreach ($qGroup['questions'] as &$q) {
                        $allQuestionsAnswered = true;
                        if (in_array($q['question_key'], $dependencies)) {
                            $q['hidden'] = true;
                        } else {
                            $q['hidden'] = false;
                        }
                        /*Verify anwser*/
                        if (!empty($formReply)) {

                            if (strtoupper(preg_replace('/\s+/', '', $q['question_type']['name'])) === 'CHECKBOX') {
                                $answer = FormReplyAnswer::select('question_option_id')
                                    ->whereQuestionId($q['id'])
                                    ->whereFormReplyId($formReply->id)
                                    ->distinct()
                                    ->get()
                                    ->pluck('question_option_id')->toArray();
                                if (sizeof($answer) == 0) {
                                    $allQuestionsAnswered = false;
                                }
                                $q['reply'] = $answer;

                            } else {
                                $answer = FormReplyAnswer::whereQuestionId($q['id'])->whereFormReplyId($formReply->id)->first();
                                if (!empty($answer) && (!empty($answer->answer) || $answer->question_option_id > 0)) {
                                    $q['reply'] = empty($answer->answer) ? $answer->question_option_id : $answer->answer;

                                } else {
                                    $allQuestionsAnswered = false;
                                    $q['reply'] = null;
                                }
                            }
                        } else {
                            $allQuestionsAnswered = false;
                            $q['reply'] = null;
                        }
                        $qGroup['all_questions_answered'] = $allQuestionsAnswered;
                        foreach ($q['question_options'] as $qOption) {
                            foreach ($qOption['dependencies'] as $dependency) {
                                $dependencies[] = $dependency['question_key'];
                            }
                        }
                    }
                }
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request  the list of Answers of form
     * Returns the answers of the Form Reply
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function formAnswers(Request $request, $formKey)
    {
        try {
            $form = Form::with(['formReplies.formReplyAnswers'])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
                return response()->json($form, 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Form Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @param $formReplyKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function statisticsByFormReply(Request $request, $formKey, $formReplyKey)
    {
        try {

            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions',
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $formReply = FormReply::whereFormReplyKey($formReplyKey)->whereFormId($form->id)->first();

                //Get the translation sent in the request
                if(!($form->translation($request->header('LANG_CODE')))){
                    if(!($form->translation($request->header('LANG_CODE_DEFAULT')))){
                        //  return response()->json(['error' => 'No translation found'], 404);
                    }
                }
                foreach($form->questionGroups as $questionGroup){
                    if(!($questionGroup->translation($request->header('LANG_CODE')))){
                        if(!($questionGroup->translation($request->header('LANG_CODE_DEFAULT')))){
                            //   return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                    foreach ($questionGroup->questions as $question){
                        if(!($question->translation($request->header('LANG_CODE')))){
                            if(!($question->translation($request->header('LANG_CODE_DEFAULT')))){
                                //    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }

                        foreach ($question->questionOptions as $questionOption){
                            if(!($questionOption->translation($request->header('LANG_CODE')))){
                                if(!($questionOption->translation($request->header('LANG_CODE_DEFAULT')))){
                                    //    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                    }
                }

                $data = $form->toArray();
                $dependencies = array();
                foreach ($data['question_groups'] as &$qGroup) {
                    foreach ($qGroup['questions'] as &$q) {
                        $allQuestionsAnswered = true;
                        if (in_array($q['question_key'], $dependencies)) {
                            $q['hidden'] = true;
                        } else {
                            $q['hidden'] = false;
                        }
                        /*Verify anwser*/
                        if (!empty($formReply)) {

                            if (strtoupper(preg_replace('/\s+/', '', $q['question_type']['name'])) === 'CHECKBOX') {
                                $answer = FormReplyAnswer::select('question_option_id')
                                    ->whereQuestionId($q['id'])
                                    ->whereFormReplyId($formReply->id)
                                    ->distinct()
                                    ->get()
                                    ->pluck('question_option_id')->toArray();
                                if (sizeof($answer) == 0) {
                                    $allQuestionsAnswered = false;
                                }
                                $q['reply'] = $answer;

                            } else {
                                $answer = FormReplyAnswer::whereQuestionId($q['id'])->whereFormReplyId($formReply->id)->first();
                                if (!empty($answer) && (!empty($answer->answer) || $answer->question_option_id > 0)) {
                                    $q['reply'] = empty($answer->answer) ? $answer->question_option_id : $answer->answer;

                                } else {
                                    $allQuestionsAnswered = false;
                                    $q['reply'] = null;
                                }
                            }

                        } else {
                            $allQuestionsAnswered = false;
                            $q['reply'] = null;
                        }
                        $qGroup['all_questions_answered'] = $allQuestionsAnswered;
                        foreach ($q['question_options'] as $qOption) {
                            foreach ($qOption['dependencies'] as $dependency) {
                                $dependencies[] = $dependency['question_key'];
                            }
                        }
                    }
                }
                $data["formReply"] = $formReply;
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request, $formKey)
    {
        try {

            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions',
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $data = $form->toArray();
                $dependencies = array();
                foreach ($data['question_groups'] as &$qGroup) {
                    foreach ($qGroup['questions'] as &$q) {
                        $allQuestionsAnswered = true;
                        if (in_array($q['question_key'], $dependencies)) {
                            $q['hidden'] = true;
                        } else {
                            $q['hidden'] = false;
                        }

                        $q["answers"] = 1;
                        $stats = [];

                        $total = FormReplyAnswer::whereQuestionId($q['id'])->count();

                        $q["total_count"] = $total;

                        if (!empty($q['question_options'])) {
                            /*
                            dd($q['question_options']);
                            foreach($q['question_options'] as $option){
                               $q['question_options'][$option['id']] = 0; // FormReplyAnswer::whereQuestionOptionId($option['id'])->count();
                            }
                             */

                            for ($i = 0; $i < count($q['question_options']); $i++) {
                                $count = FormReplyAnswer::whereQuestionId($q['id'])->whereQuestionOptionId($q['question_options'][$i]['id'])->count();
                                $q['question_options'][$i]["count"] = $count;
                                if ($total != 0) {
                                    $q['question_options'][$i]["count_percentage"] = $count * 100 / $total;
                                }
                            }
                        }

                        $qGroup['all_questions_answered'] = $allQuestionsAnswered;
                        foreach ($q['question_options'] as $qOption) {
                            foreach ($qOption['dependencies'] as $dependency) {
                                $dependencies[] = $dependency['question_key'];
                            }
                        }
                    }
                }
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of completed and not completed statistics counters of the Form
     * Returns the list of completed and not completed statistics counters of the Form
     *
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function completed(Request $request, $formKey)
    {
        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $data["completed"] = $form->formReplies()->whereCompleted(1)->count();
                $data["not_completed"] = $form->formReplies()->whereCompleted(0)->count();

                return response()->json($data, 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve completed and not completed statistics counters'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of completed and not completed statistics counters of the Form
     * Returns the list of completed and not completed statistics counters of the Form
     *
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function completedUserList(Request $request, $formKey)
    {
        try {
            $form = Form::whereFormKey($formKey)->firstOrFail();
            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
                $data = $form->formReplies()->get(array('created_by', 'completed'));
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve completed and not completed statistics counters'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $formId
     */
    public function showByUser(Request $request, $formKey, $userKey)
    {
        try {

            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions',
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {
//            $formConfigurations = $form->formConfigurations()->get();

//            foreach ($formConfigurations as $formConfiguration) {
//                if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
//                    if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
//                        return response()->json(['error' => 'No translation found'], 404);
//                }
//                $formConfiguration['value'] = $formConfiguration->pivot->value;
//                $formConfiguration = array_except($formConfiguration, 'pivot');
//            }

                /*
                 * Verify if the Form can be used by anonymous
                 */
                $formReply = FormReply::whereCreatedBy($userKey)->whereFormId($form->id)->first();

                $data = $form->toArray();
                $dependencies = array();
                foreach ($data['question_groups'] as &$qGroup) {
                    foreach ($qGroup['questions'] as &$q) {
                        $allQuestionsAnswered = true;
                        if (in_array($q['question_key'], $dependencies)) {
                            $q['hidden'] = true;
                        } else {
                            $q['hidden'] = false;
                        }
                        /*Verify anwser*/
                        if (!empty($formReply)) {

                            if (strtoupper(preg_replace('/\s+/', '', $q['question_type']['name'])) === 'CHECKBOX') {
                                $answer = FormReplyAnswer::select('question_option_id')
                                    ->whereQuestionId($q['id'])
                                    ->whereFormReplyId($formReply->id)
                                    ->distinct()
                                    ->get()
                                    ->pluck('question_option_id')->toArray();
                                if (sizeof($answer) == 0) {
                                    $allQuestionsAnswered = false;
                                }
                                $q['reply'] = $answer;

                            } else {
                                $answer = FormReplyAnswer::whereQuestionId($q['id'])->whereFormReplyId($formReply->id)->first();
                                if (!empty($answer) && (!empty($answer->answer) || $answer->question_option_id > 0)) {
                                    $q['reply'] = empty($answer->answer) ? $answer->question_option_id : $answer->answer;

                                } else {
                                    $allQuestionsAnswered = false;
                                    $q['reply'] = null;
                                }
                            }

                        } else {
                            $allQuestionsAnswered = false;
                            $q['reply'] = null;
                        }
                        $qGroup['all_questions_answered'] = $allQuestionsAnswered;
                        foreach ($q['question_options'] as $qOption) {
                            foreach ($qOption['dependencies'] as $dependency) {
                                $dependencies[] = $dependency['question_key'];
                            }
                        }
                    }
                }
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     * Request list of all Question Groups of the Form
     * Returns the list of all Question Groups of the Form
     * @param Request $request
     * @param $formKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function statisticsByForm(Request $request, $formKey)
    {
        try {

            $form = Form::with([
                'questionGroups'                                    => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions'                          => function ($query) {$query->orderBy('position', 'asc');},
                'questionGroups.questions.questionOptions',
                'questionGroups.questions.questionOptions.icon',
                'questionGroups.questions.questionType',
                'questionGroups.questions.questionOptions.dependencies',
            ])->whereFormKey($formKey)->firstOrFail();

            if($form->entity_key == $request->header('X-ENTITY-KEY')) {

                $formReplies = FormReply::whereFormId($form->id)->get();

                //Get the translation sent in the request
                if(!($form->translation($request->header('LANG_CODE')))){
                    if(!($form->translation($request->header('LANG_CODE_DEFAULT')))){
                        //  return response()->json(['error' => 'No translation found'], 404);
                    }
                }
                foreach($form->questionGroups as $questionGroup){
                    if(!($questionGroup->translation($request->header('LANG_CODE')))){
                        if(!($questionGroup->translation($request->header('LANG_CODE_DEFAULT')))){
                            //   return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                    foreach ($questionGroup->questions as $question){
                        if(!($question->translation($request->header('LANG_CODE')))){
                            if(!($question->translation($request->header('LANG_CODE_DEFAULT')))){
                                //    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }

                        foreach ($question->questionOptions as $questionOption){
                            if(!($questionOption->translation($request->header('LANG_CODE')))){
                                if(!($questionOption->translation($request->header('LANG_CODE_DEFAULT')))){
                                    //    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                    }
                }

                $answers = [];
                foreach ($formReplies as $formReply){
                    $data = $form->toArray();
                    $answers[$formReply->id]["created_by"] = $formReply->created_by;
                    $dependencies = array();
                    foreach ($data['question_groups'] as &$qGroup) {
                        foreach ($qGroup['questions'] as &$q) {

                            $allQuestionsAnswered = true;
                            if (in_array($q['question_key'], $dependencies)) {
                                $q['hidden'] = true;
                            } else {
                                $q['hidden'] = false;
                            }

                            /* Verify anwser */
                            if (!empty($formReply)) {

                                if (strtoupper(preg_replace('/\s+/', '', $q['question_type']['name'])) === 'CHECKBOX') {
                                    $answer = FormReplyAnswer::select('question_option_id')
                                        ->whereQuestionId($q['id'])
                                        ->whereFormReplyId($formReply->id)
                                        ->distinct()
                                        ->get()
                                        ->pluck('question_option_id')->toArray();
                                    if (sizeof($answer) == 0) {
                                        $allQuestionsAnswered = false;
                                    }
                                    $answers[$formReply->id]["answers"][$q['id']] = $answer;

                                } else {
                                    $answer = FormReplyAnswer::whereQuestionId($q['id'])->whereFormReplyId($formReply->id)->first();
                                    if (!empty($answer) && (!empty($answer->answer) || $answer->question_option_id > 0)) {
                                        $answers[$formReply->id]["answers"][$q['id']] = empty($answer->answer) ? $answer->question_option_id : $answer->answer;

                                    } else {
                                        $allQuestionsAnswered = false;
                                        $answers[$formReply->id]["answers"][$q['id']] = null;
                                    }
                                }

                            } else {
                                $allQuestionsAnswered = false;
                                $answers[$formReply->id]["answers"][$q['id']] = null;
                            }
                            $qGroup['all_questions_answered'] = $allQuestionsAnswered;
                            foreach ($q['question_options'] as $qOption) {
                                foreach ($qOption['dependencies'] as $dependency) {
                                    $dependencies[] = $dependency['question_key'];
                                }
                            }
                        }
                    }
                }

                $data["formReplies"] = $answers;
                return response()->json($data, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Question Groups List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestionnaires(Request $request)
    {
        try {
            $formsKeys = $request->json('forms_keys');
            $forms = Form::whereIn('form_key', $formsKeys)->get()->keyBy('form_key');

            foreach ($forms as $item) {
                $item->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
            }

            return response()->json(['data' => $forms], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Forms not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Forms'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}