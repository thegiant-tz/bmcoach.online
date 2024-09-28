<?php 
namespace App\Trait\APi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait GeneralTrait {
    public function customValidator(Request $request, array $rules, $message = 'Validation error.')
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'status' => $message,
                'status_code' => env('STATUS_CODE_PREFIX') . 'VAD400',
                'errors' => $validator->errors()
            ];
        }
        return $validator->valid();
    }
}