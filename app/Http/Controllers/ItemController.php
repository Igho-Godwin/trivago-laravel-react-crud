<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function getAllItems(){
        $items = Item::where('status','available')->get()->location;
        return $items;
    }
}
