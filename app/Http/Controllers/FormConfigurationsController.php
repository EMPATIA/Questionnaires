<?php

namespace App\Http\Controllers;

use App\FormConfiguration;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FormConfigurationsController extends Controller
{
    protected $required = [
        'store' => ['code', 'translations'],
        'update' => ['code', 'translations']
    ];


    /**
     * @SWG\Tag(
     *   name="Form Configuration Method",
     *   description="Everything about Forms Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="formConfigurationsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="formConfigurationsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="form_configuration_key", format="string", type="string"),
     *           @SWG\Property(property="code", format="string", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     *
     *
     *   @SWG\Definition(
     *   definition="formConfigurationTranslations",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"language_code", "name"},
     *           @SWG\Property(property="language_code", format="string", type="string"),
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="description", format="string", type="string")
     *       )
     *   }
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $formConfigurations = FormConfiguration::all();

            foreach ($formConfigurations as $formConfiguration) {
                if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
                    if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            return response()->json(['data' => $formConfigurations], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Form Configurations list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/formConfiguration/{form_configuration_key}",
     *  summary="Shows a Form Configuration Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Configuration Method"},
     *
     * @SWG\Parameter(
     *      name="form_configuration_key",
     *      in="path",
     *      description="Form Configuration Method Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE",
     *      in="header",
     *      description="Module Token",
     *      required=false,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE-DEFAULT",
     *      in="header",
     *      description="Module Token",
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
     *      description="Shows the Form Configuration data",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * @param Request $request
     * @param $formConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $formConfigurationKey)
    {
        try {
            $formConfiguration = FormConfiguration::whereFormConfigurationKey($formConfigurationKey)->firstOrFail();

            if (!($formConfiguration->translation($request->header('LANG-CODE')))) {
                if (!$formConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($formConfiguration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $formConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $formConfigurationKey)
    {
        try {
            $formConfiguration = FormConfiguration::whereFormConfigurationKey($formConfigurationKey)->firstOrFail();

            $formConfiguration->translations();

            return response()->json($formConfiguration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Form Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="formConfigurationsCreate",
     *   type="object",
     *   allOf={
     *     @SWG\Schema(
     *           required={"code", "translations"},
     *           @SWG\Property(property="code", format="string", type="string"),
     *           @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/formConfigurationTranslations"))
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/formConfigurations",
     *  summary="Create a Form Configuration Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Configuration Method"},
     *
     *  @SWG\Parameter(
     *      name="Form Configuration",
     *      in="body",
     *      description="Form Configuration Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formConfigurationsCreate")
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
     *      description="The newly created Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                do {
                    $rand = str_random(32);
                    if (!($exists = FormConfiguration::whereFormConfigurationKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $formConfiguration = FormConfiguration::create(
                    [
                        'code' => $request->json('code'),
                        'form_configuration_key' => $key,
                    ]
                );

                foreach ($request->json('translations') as $translation) {
                    if (isset($translation['language_code']) && isset($translation['name'])) {
                        $formConfiguration->formConfigurationTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name'],
                                'description'   => empty($translation['description']) ? null : $translation['description']
                            ]
                        );
                    }
                }

                return response()->json($formConfiguration, 201);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to store new Form Configuration'], 500);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     *
     *
     * @SWG\Put(
     *  path="/formConfigurations/{form_configuration_key}",
     *  summary="Update a Form Configuration Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Configuration Method"},
     *
     *  @SWG\Parameter(
     *      name="Form Configuration",
     *      in="body",
     *      description="Form Configuration Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/formConfigurationsCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="form_configuration_key",
     *      in="path",
     *      description="Form Configuration Key",
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
     *      description="The Updated Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * @param Request $request
     * @param $formConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $formConfigurationKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                $translationsOld = [];
                $translationsNew = [];

                $formConfiguration = FormConfiguration::whereFormConfigurationKey($formConfigurationKey)->firstOrFail();

                $formConfiguration->code = $request->json('code');
                $formConfiguration->save();

                $translationsId = $formConfiguration->formConfigurationTranslations()->get();
                foreach ($translationsId as $translationId) {
                    $translationsOld[] = $translationId->id;
                }

                foreach ($request->json('translations') as $translation) {
                    if (isset($translation['language_code']) && isset($translation['name'])) {
                        $formConfigurationTranslation = $formConfiguration->formConfigurationTranslations()->whereLanguageCode($translation['language_code'])->first();
                        if (empty($formConfigurationTranslation)) {
                            $formConfigurationTranslation = $formConfiguration->formConfigurationTranslations()->create(
                                [
                                    'language_code' => $translation['language_code'],
                                    'name'          => $translation['name'],
                                    'description'   => empty($translation['description']) ? null : $translation['description']
                                ]
                            );
                        } else {
                            $formConfigurationTranslation->name = $translation['name'];
                            $formConfigurationTranslation->description = empty($translation['description']) ? null : $translation['description'];
                            $formConfigurationTranslation->save();
                        }
                    }
                    $translationsNew[] = $formConfigurationTranslation->id;
                }

                $deleteTranslations = array_diff($translationsOld, $translationsNew);
                foreach ($deleteTranslations as $deleteTranslation) {
                    $deleteId = $formConfiguration->formConfigurationTranslations()->whereId($deleteTranslation)->first();
                    $deleteId->delete();
                }

                return response()->json($formConfiguration, 200);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to update Form Configuration'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Form Configuration not Found'], 404);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="formConfigurationDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/formConfigurations/{form_configuration_key}",
     *  summary="Delete Form Configuration Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Form Configuration Method"},
     *
     * @SWG\Parameter(
     *      name="form_configuration_key",
     *      in="path",
     *      description="Form Configuration Key",
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
     *      @SWG\Schema(ref="#/definitions/formConfigurationDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Form Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Form Configuration",
     *      @SWG\Schema(ref="#/definitions/formConfigurationsMethodErrorDefault")
     *  )
     * )
     *
     */


    public function destroy(Request $request, $formConfigurationKey)
    {
        $userKey = ONE::verifyToken($request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                $formConfiguration = FormConfiguration::whereFormConfigurationKey($formConfigurationKey)->firstOrFail();

                $formConfigurationTranslations = $formConfiguration->formConfigurationTranslations()->get();
                foreach ($formConfigurationTranslations as $formConfigurationTranslation) {
                    $formConfigurationTranslation->delete();
                }

                $formConfiguration->delete();

                return response()->json('OK', 200);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to delete a Form Configuration'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Form Configuration not Found'], 404);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
