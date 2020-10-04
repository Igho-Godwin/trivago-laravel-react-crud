<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;
use Validator;

class ItemController extends Controller
{
    private const SUCCESS_MSG = 'Successful' ;
    public function getAll()
    {
        try {
            $items = Item::where('status', 'available')->with('location')->get();
            return response()->json(['items' => $items,'message'=>self::SUCCESS_MSG], 200);
        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }
    }

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
            return response()->json(['item' => $item,'message'=>self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

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

            return response()->json(['item' => $item,'message'=>self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

    public function update(Request $request,$id)
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
            return response()->json(['item' => $item,'message'=>self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

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
            return response()->json(['item' => $item,'message'=>self::SUCCESS_MSG], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }

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

            if($item->availability > 0){
                $item->availability-=$item->availability;
                $item->save();
                $item->location = $item->location;
                return response()->json(['item' => $item,'message'=>self::SUCCESS_MSG], 200);
            }

            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => "Booking not allowed",
                'message'=>"Item availability is at 0 so booking is not allowed."], 403);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }
}
