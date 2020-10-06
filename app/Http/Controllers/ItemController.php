<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;
use Validator;

class ItemController extends Controller
{
    private const SUCCESS_MSG = 'Successful';

    /** @OA\Get(
     * path="/api/items/all",
     * summary="Retrieve All items",
     * description="Retrieve All items",
     * operationId="getAll",
     * tags={"items"},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="items", type="object"),
     *       @OA\Property(property="message", type="string")
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="error", type="string"),
     *    )
     * )
     * )
     */

    public function getAll()
    {
        try {
            $items = Item::where('status', 'available')->with('location')->paginate(50);
            return response()->json(['items' => $items, 'message' => self::SUCCESS_MSG], 200);
        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }
    }

    /** @OA\Get(
     * path="/api/item/{id}",
     * summary="Retrieve All items",
     * description="Retrieve All items",
     * operationId="getAll",
     * tags={"items"},
     * @OA\Parameter(
     *    description="ID of item",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="items", type="object"),
     *       @OA\Property(property="message", type="string")
     *        )
     *     ),
     *     @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="invalid-params", type="object")
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="error", type="string"),
     *    )
     * )
     * )
     */

    public function get($id)
    {

        try {

            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:items'
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => "Your request parameters didn't validate.",
                    'invalid-params' => $validator->errors()], 400);
            }

            $item = Item::find($id);
            $item->location = $item->location;
            return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

    /**
     * @OA\Post(
     ** path="/api/item/create",
     *   tags={"items"},
     *   summary="createItem",
     *   operationId="createItem",
     *
     *   @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *      @OA\JsonContent(
     *           @OA\Property(
     *               property="name",
     *               type="string"
     *           ),
     *           @OA\Property(
     *               property="rating",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="category",
     *               type="string",
     *               example="either hotel, alternative, hostel, lodge, resort, guesthouse"
     *           ),
     *           @OA\Property(
     *               property="image",
     *               type="string",
     *           ),
     *           @OA\Property(
     *               property="reputation",
     *               type="number"
     *           ),
     *          @OA\Property(
     *               property="price",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="availability",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="location",
     *               type="object",
     *               @OA\Property(
     *                  property="city",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="state",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="country",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="zip_code",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="address",
     *                  type="string"
     *               ),
     *           ),
     *
     *      )
     *   ),
     * @OA\Response(
     *    response=201,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="items", type="object"),
     *       @OA\Property(property="message", type="string")
     *        )
     *     ),
     *     @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="invalid-params", type="object")
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="error", type="string"),
     *    )
     * )
     *)
     **/

    public function create(Request $request)
    {

        try {

            Validator::extend('not_contains', function ($attribute, $value, $parameters) {
                // Banned words
                $words = ["Free", "Offer", "Book", "Website"];
                foreach ($words as $word) {
                    if (stripos($value, $word) !== false) return false;
                }
                return true;
            });

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:10|string|not_contains',
                'rating' => 'required|integer|min:0|max:5',
                'category' => 'required|in:hotel,alternative,hostel,lodge,resort,guesthouse|string',
                'location.zip_code' => 'required|integer|min:10000|max:90000',
                'location.state' => 'required|string|max:255',
                'location.city' => 'required|string|max:255',
                'location.country' => 'required|string|max:255',
                'location.address' => 'required|string|max:255',
                'image' => 'required|max:255|url',
                'reputation' => 'required|integer|min:0|max:1000',
                'price' => 'required|integer',
                'availability' => 'required|integer',
            ], ['not_contains' => "Name most not contain ['Free', 'Offer', 'Book', 'Website'] "]);

            if ($validator->fails()) {
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => "Your request parameters didn't validate.",
                    'invalid-params' => $validator->errors()], 400);
            }

            if ($request->get('reputation') <= 500) {
                $reputation_badge = 'red';
            } elseif ($request->get('reputation') > 500 && $request->get('reputation') <= 799) {
                $reputation_badge = 'yellow';
            } else {
                $reputation_badge = 'green';
            }

            $item = new Item();
            $item->name = $request->get('name');
            $item->rating = $request->get('rating');
            $item->category = $request->get('category');
            $item->image = $request->get('image');
            $item->reputation = $request->get('reputation');
            $item->price = $request->get('price');
            $item->availability = $request->get('availability');
            $item->reputation_badge = $reputation_badge;
            $item->save();

            $location = new Location();
            $location->item_id = $item->id;
            $location->zip_code = $request->get('location')['zip_code'];
            $location->state = $request->get('location')['state'];
            $location->city = $request->get('location')['city'];
            $location->country = $request->get('location')['country'];
            $location->address = $request->get('location')['address'];
            $location->save();
            $item->location = $location;

            return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 201);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

    /**
     * @OA\Put(
     ** path="/api/item/update/{id}",
     *   tags={"items"},
     *   summary="createItem",
     *   operationId="createItem",
     * @OA\Parameter(
     *    description="ID of item",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *    )
     * ),
     *   @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *      @OA\JsonContent(
     *           @OA\Property(
     *               property="name",
     *               type="string"
     *           ),
     *           @OA\Property(
     *               property="rating",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="category",
     *               type="string",
     *               example="either hotel, alternative, hostel, lodge, resort, guesthouse"
     *           ),
     *           @OA\Property(
     *               property="image",
     *               type="string",
     *           ),
     *           @OA\Property(
     *               property="reputation",
     *               type="number"
     *           ),
     *          @OA\Property(
     *               property="price",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="availability",
     *               type="number"
     *           ),
     *           @OA\Property(
     *               property="location",
     *               type="object",
     *               @OA\Property(
     *                  property="city",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="state",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="country",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="zip_code",
     *                  type="string"
     *               ),
     *               @OA\Property(
     *                  property="address",
     *                  type="string"
     *               ),
     *           ),
     *
     *      )
     *   ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="items", type="object"),
     *       @OA\Property(property="message", type="string")
     *        )
     *     ),
     *     @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="invalid-params", type="object")
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="error", type="string"),
     *    )
     * )
     *)
     **/

    public function update(Request $request, $id)
    {

        try {

            Validator::extend('not_contains', function ($attribute, $value, $parameters) {
                // Banned words
                $words = ["Free", "Offer", "Book", "Website"];
                foreach ($words as $word) {
                    if (stripos($value, $word) !== false) return false;
                }
                return true;
            });

            $request['id'] = $id;

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:items',
                'name' => 'required|max:10|string|not_contains',
                'rating' => 'required|integer|min:0|max:5',
                'category' => 'required|in:hotel,alternative,hostel,lodge,resort,guesthouse|string',
                'location.zip_code' => 'required|integer|min:10000|max:90000',
                'location.state' => 'required|string|max:255',
                'location.city' => 'required|string|max:255',
                'location.country' => 'required|string|max:255',
                'location.address' => 'required|string|max:255',
                'image' => 'required|max:255|url',
                'reputation' => 'required|integer|min:0|max:1000',
                'price' => 'required|integer',
                'availability' => 'required|integer',
            ], ['not_contains' => "Name most not contain ['Free', 'Offer', 'Book', 'Website'] "]);

            if ($validator->fails()) {
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => "Your request parameters didn't validate.",
                    'invalid-params' => $validator->errors()], 400);
            }

            if ($request->get('reputation') <= 500) {
                $reputation_badge = 'red';
            } elseif ($request->get('reputation') > 500 && $request->get('reputation') <= 799) {
                $reputation_badge = 'yellow';
            } else {
                $reputation_badge = 'green';
            }

            $item = Item::find($id);
            $item->name = $request->get('name');
            $item->rating = $request->get('rating');
            $item->category = $request->get('category');
            $item->image = $request->get('image');
            $item->reputation = $request->get('reputation');
            $item->price = $request->get('price');
            $item->availability = $request->get('availability');
            $item->reputation_badge = $reputation_badge;
            $item->save();

            $location = Location::find($item->location->id);
            $location->item_id = $item->id;
            $location->zip_code = $request->get('location')['zip_code'];
            $location->state = $request->get('location')['state'];
            $location->city = $request->get('location')['city'];
            $location->country = $request->get('location')['country'];
            $location->address = $request->get('location')['address'];
            $location->save();
            return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

    /**
     * @OA\Delete(
     ** path="/api/item/delete/{id}",
     *   tags={"items"},
     *   summary="deleteItem",
     *   operationId="deleteItem",
     * @OA\Parameter(
     *    description="ID of item",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *    )
     * ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=500,
     *      description="Internal Server Error"
     *   ),
     *)
     **/

    public function delete($id)
    {

        try {

            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:items'
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => "Your request parameters didn't validate.",
                    'invalid-params' => $validator->errors()], 400);
            }

            $item = Item::find($id);
            $item->status = 'deleted';
            $item->save();
            $item->location = $item->location;
            return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

    /**
     * @OA\Patch(
     ** path="/api/item/book/{id}",
     *   tags={"items"},
     *   summary="bookItem",
     *   operationId="bookItem",
     * @OA\Parameter(
     *    description="ID of item",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="items", type="object"),
     *       @OA\Property(property="message", type="string")
     *        )
     *     ),
     *     @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="invalid-params", type="object")
     *        )
     *     ),
     *        @OA\Response(
     *    response=403,
     *    description="Booking not allowed",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="message", type="object")
     *        )
     *     ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="error", type="string"),
     *    )
     * )
     *)
     **/

    public function book($id)
    {

        try {

            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:items'
            ]);

            if ($validator->fails()) {
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => "Your request parameters didn't validate.",
                    'invalid-params' => $validator->errors()], 400);
            }

            $item = Item::find($id);

            if ($item->availability > 0) {
                $item->availability -= 1;
                $item->save();
                $item->location = $item->location;
                return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);
            }

            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => "Booking not allowed",
                'message' => "Item availability is at 0 so booking is not allowed."], 403);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }
}
