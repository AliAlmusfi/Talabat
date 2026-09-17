<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Tools\CRUDTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller {
    use CRUDTrait;
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'market_id' => ['required', 'exists:markets,id'],
            'info' => ['required', 'max:255'],
            'type' => ['required', 'in:Fake Store,Wrong Address,Fake Products,Invalid Products,Inapropriate name or product,Something else']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $report = Report::create($validator->validated());
        return $this->response(new ReportResource($report) , 201 , "Report Created Successfully");
    }
    public function show(Request $request){
        return $this->show_method($request, ReportResource::class, Report::class);
    }
    public function show_one(Request $request){
        return $this->showOne_method($request, ReportResource::class, Report::class,'Report');
    }
}
