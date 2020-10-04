<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;
use Validator;

class ItemController extends Controller
{
    public function getAllItems()
    {
        try {
            $items = Item::where('status', 'available')->get();
            return response()->json(['items' => $items], 200);
        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }
    }

    public function getAnItem($id)
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
            return response()->json(['item' => $item], 200);

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
                'zip_code' => 'required|integer|min:10000|max:90000',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'address' => 'required|string|max:255',
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
            $location->zip_code = $request->get('zip_code');
            $location->state = $request->get('state');
            $location->city = $request->get('city');
            $location->country = $request->get('country');
            $location->address = $request->get('address');
            $location->save();
            return response()->json(['item' => $item], 200);

        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }
}
