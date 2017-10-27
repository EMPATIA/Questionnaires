<?php

namespace App\Http\Controllers;

use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Icon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IconsController extends Controller
{
    protected $keysRequired = [
        'name',
        'file_id',
        'file_code'
    ];

    /**
     * @SWG\Tag(
     *   name="Icon Method",
     *   description="Everything about Icons Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="iconsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="iconsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="icon_key", format="string", type="string"),
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="file_id", format="string", type="string"),
     *           @SWG\Property(property="file_code", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */

    /**
     * Request list of all Icons
     * Returns the list of all Icons
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $icons = Icon::all();
            return response()->json(['data' => $icons], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Icons list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/icon/{icon_key}",
     *  summary="Shows a Icon Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Icon Method"},
     *
     * @SWG\Parameter(
     *      name="icon_key",
     *      in="path",
     *      description="Icon Method Key",
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
     *      description="Shows the Icon data",
     *      @SWG\Schema(ref="#/definitions/iconsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Icon not Found",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Icon",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  )
     *)
     */


    /**
     * Request of one Icon
     * Returns the attributes of the Icon
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show(Request $request, $iconKey)
    {
        try{
            $icon = Icon::whereIconKey($iconKey)->firstOrFail();
            return response()->json($icon, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Icon not Found'], 404);
        }catch(Exception $e){
            dd($e);
            return response()->json(['error' => 'Failed to retrieve the Icon'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *   definition="iconsCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"name","file_id","file_code" },
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(property="file_id", format="string", type="string"),
     *           @SWG\Property(property="file_code", format="string", type="string")
     *       )
     *   }
     * )
     *
     * @SWG\Post(
     *  path="/icon",
     *  summary="Create a Icon Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Icon Method"},
     *
     *  @SWG\Parameter(
     *      name="Icon",
     *      in="body",
     *      description="Icon Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/iconsCreate")
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
     *      description="The newly created Icon",
     *      @SWG\Schema(ref="#/definitions/iconsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Icon",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
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

        do {
            $rand = str_random(32);

            if (!($exists = Icon::whereIconKey($rand)->exists())) {
                $key = $rand;
                
            }
        } while ($exists);

        try{
            $icon = Icon::create(
                [
                    'icon_key'  => $key,
                    'name'      => $request->json('name'),
                    'file_id'   => $request->json('file_id'),
                    'file_code' => $request->json('file_code'),
                ]
            );
            return response()->json($icon, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Icon'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/icon/{icon_key}",
     *  summary="Update a Icon Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Icon Method"},
     *
     *  @SWG\Parameter(
     *      name="Icon",
     *      in="body",
     *      description="Icon Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/iconsCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="icon_key",
     *      in="path",
     *      description="Icon Key",
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
     *      description="The Updated Icon",
     *      @SWG\Schema(ref="#/definitions/iconsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Icon not Found",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to Update Icon",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update a existing Form
     * Return the Attributes of the Form Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $key)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        //VERIFY PERMISSIONS

        try{
            $icon = Icon::whereIconKey($key)->firstOrFail();

            $icon->name        = $request->json('name');
            $icon->file_id     = $request->json('file_id');
            $icon->file_code   = $request->json('file_code');

            $icon->save();

            return response()->json($icon, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Icon not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Icon'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="iconDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/icon/{icon_key}",
     *  summary="Delete Icon Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Icon Method"},
     *
     * @SWG\Parameter(
     *      name="icon_key",
     *      in="path",
     *      description="Icon Key",
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
     *      @SWG\Schema(ref="#/definitions/iconDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Icon not Found",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete a Icon",
     *      @SWG\Schema(ref="#/definitions/iconsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete existing Form
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $icon = Icon::whereIconKey($key)->firstOrFail();
            $icon->delete();
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Icon not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Icon'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
