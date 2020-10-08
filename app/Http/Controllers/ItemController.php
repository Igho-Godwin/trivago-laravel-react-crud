<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;
use App\Http\Requests\StoreItem;
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
     *     )
     * )
     */

    public function getAll()
    {
        $items = Item::where('status', 'available')->with('location')->paginate(50);
        return response()->json(['items' => $items, 'message' => self::SUCCESS_MSG], 200);
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
     *    @OA\Response(
     *    response=422,
     *    description="Your request parameters didn't validate.",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="errors", type="object")
     *        )
     *     )
     * )
     */

    public function get($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:items'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Your request parameters didn't validate.",
                'errors' => $validator->errors()], 422);
        }

        $item = Item::find($id);
        $item->location = $item->location;
        return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);
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
     *    response=422,
     *    description="Your request parameters didn't validate.",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="errors", type="object")
     *        )
     *     )
     *)
     **/

    public function create(StoreItem $request)
    {

        $validated = $request->validated();

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
     *    response=422,
     *    description="Your request parameters didn't validate.",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="errors", type="object")
     *        )
     *     )
     *)
     **/

    public function update(StoreItem $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:items'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Your request parameters didn't validate.",
                'errors' => $validator->errors()], 422);
        }

        $validated = $request->validated();

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
     *    response=422,
     *    description="Your request parameters didn't validate.",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="errors", type="object")
     *        )
     *     )
     *)
     **/

    public function delete($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:items'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Your request parameters didn't validate.",
                'errors' => $validator->errors()], 422);
        }

        $item = Item::find($id);
        $item->status = 'deleted';
        $item->save();
        $item->location = $item->location;
        return response()->json(['item' => $item, 'message' => self::SUCCESS_MSG], 200);

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
     *    response=422,
     *    description="Your request parameters didn't validate.",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string"),
     *       @OA\Property(property="errors", type="object")
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
     *)
     **/

    public function book($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:items'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Your request parameters didn't validate.",
                'errors' => $validator->errors()], 422);
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


    }
}
