<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;

class WorkerController extends Controller{
	public function index(Request $request)
    {
    	return success($request->user()->workers);
    }

    public function show($worker, Request $request){
    	$worker = $request->user()->workers()->where('id', $worker)->first();
    	if(is_null($worker)){
    		return failure([], 404);
    	}
    	return success($worker);
    }

    public function store(Request $request)
    {
    	$request->validate([
    		'name' => ['required'],
      	]);

    	$worker = $request->user()->workers()->create($request->only(['name']));

    	return success($worker->toArray());
    }

    public function update($worker, Request $request)
    {
    	$request->validate([
    		'name' => ['sometimes']
    	]);

    	$worker = $request->user()->workers()->where('id', $worker)->first();

    	if(is_null($worker)){
    		return failure([], 404);
    	}

    	$worker->update($request->only(['name', 'color']));

    	return success($worker->refresh()->toArray());
    }

    public function destroy($worker, Request $request)
    {
    	$worker = $request->user()->workers()->where('id', $worker)->first();

    	if(is_null($worker)){
    		return failure([], 404);
    	}

    	$worker->delete();

    	return success([]);
    }
}
