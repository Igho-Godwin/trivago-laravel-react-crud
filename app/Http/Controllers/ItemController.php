<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

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

    public function  getAnItem($id){

        try {
            $item = Item::find($id);
            if(!$item){
                return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                    'title' => 'Not found',
                    'error' => 'Item not found'], 404);
            }
            return response()->json(['item' => $item], 200);
        } catch (\Exception $e) {
            return response()->json(['type' => 'https://www.restapitutorial.com/httpstatuscodes.html',
                'title' => 'Internal Server Error',
                'error' => $e->getMessage()], 500);
        }

    }
}
