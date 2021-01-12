<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
    	return success($request->user()->collections);
    }

    public function show($collection, Request $request){
    	$collection = $request->user()->collections()->where('id', $collection)->first();
    	if(is_null($collection)){
    		return failure([], 404);
    	}
    	return success($collection);
    }

    public function store(Request $request)
    {
    	$request->validate([
    		'name' => ['required'],
    		'color' => ['sometimes', 'regex:/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$/'] // Force hex storage
    	]);

    	$collection = $request->user()->collections()->create($request->only(['name', 'color']));

    	return success($collection->toArray());
    }

    public function update($collection, Request $request)
    {
    	$request->validate([
    		'name' => ['sometimes'],
    		'color' => ['sometimes', 'regex:/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$/'] // Force hex storage
    	]);

    	$collection = $request->user()->collections()->where('id', $collection)->first();

    	if(is_null($collection)){
    		return failure([], 404);
    	}

    	$collection->update($request->only(['name', 'color']));

    	return success($collection->refresh()->toArray());
    }

    public function destroy($collection, Request $request)
    {
    	$collection = $request->user()->collections()->where('id', $collection)->first();

    	if(is_null($collection)){
    		return failure([], 404);
    	}

    	$collection->delete();

    	return success([]);
    }
}
