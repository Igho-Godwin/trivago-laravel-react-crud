<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

class StoreItem extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('not_contains', function ($attribute, $value, $parameters) {
            // Banned words
            $words = ["Free", "Offer", "Book", "Website"];
            foreach ($words as $word) {
                if (stripos($value, $word) !== false) return false;
            }
            return true;
        });

        return [
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
                'availability' => 'required|integer'
        ];
    }

    public function messages()
    {
        return ['not_contains' => "Name most not contain ['Free', 'Offer', 'Book', 'Website'] "];
    }
}
