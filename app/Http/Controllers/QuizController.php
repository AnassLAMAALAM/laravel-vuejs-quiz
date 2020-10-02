<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller 
{
    public function index(Module $module)
    {

        return Quiz::with('author')->get();

        $quizzes = $module->quizzes()->get();
        return QuizResource::collection($quizzes);
    }

    public function show(Module $module,Quiz $quiz)
    {
        return response()->json([
            'id' => $this->id,
            'publish' => $this->publish,
            'published_at' => $this->published_at,
            'time' => $this->time,
            'views_count' => $this->created_at,
            'votes_count' => $this->updated_at], 200);
    }

    public function store(Request $request,Module $module)
    {

        $validator = Validator::make($request->all(), [

            'publish' => 'required|boolean',
            'published_at' => 'nullable|date',
            'time' => 'nullable|date_format:H:i:s', //"time" :  "02:17:00", 
            'views_count' => 'Integer',
            'votes_count' => 'Integer',
            
        ]);
         
        if ($validator->fails()) {
            return response()->json($validator->errors()->get('*'),500);
        }else{
            $quiz = $module->quizzes()->create($request->all() + ['user_id' => Auth::id()]);

            return response()->json(['message' => 'Your quiz has been submitted successfully', 
            'quiz' => new QuizResource($quiz)],201);

        }
    }

    public function update(Request $request,Module $module,Quiz $quiz)
    {
        $validator = Validator::make($request->all(), [

            'publish' => 'required|boolean',
            'published_at' => 'nullable|date',
            'time' => 'nullable|date_format:H:i:s', //"time" : "02:17:00" ,
            'views_count' => 'Integer',
            'votes_count' => 'Integer',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->get('*'),500);
        }else{
             $quiz->update($request->all() + ['user_id' => Auth::id()]);

            return response()->json(['message' => 'Your quiz has been updated successfully', 
            'quiz' => new QuizResource($quiz)],200);
        }
    }

    public function destroy(Module $module,Quiz $quiz)
    {
        $quiz->delete();
        return response()->json(['message' => "Your quiz has been removed", 204]);
    }

}