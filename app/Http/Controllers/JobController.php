<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller{

	public function index(Request $request)
    {
    	return success($request->user()->jobs);
    }

    public function show($job, Request $request){
    	$job = $request->user()->jobs()->where('id', $job)->first();
    	if(is_null($job)){
    		return failure([], 404);
    	}
    	return success($job);
    }

    public function store(Request $request)
    {
    	$request->validate([
    		'name' => ['required'],
      	]);

    	$job = $request->user()->jobs()->create($request->only(['name']));

    	return success($job->toArray());
    }

    public function update($job, Request $request)
    {
    	$request->validate([
    		'name' => ['sometimes']
    	]);

    	$job = $request->user()->jobs()->where('id', $job)->first();

    	if(is_null($job)){
    		return failure([], 404);
    	}

    	$job->update($request->only(['name', 'color']));

    	return success($job->refresh()->toArray());
    }

    public function destroy($job, Request $request)
    {
    	$job = $request->user()->jobs()->where('id', $job)->first();

    	if(is_null($job)){
    		return failure([], 404);
    	}

    	$job->delete();

    	return success([]);
    }
}
