<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    //

<<<<<<< HEAD
//    public function guard()
//    {
//        return Auth::guard('api');
//    }

    public function addStory(Request $request){

        $check_user = $this->guard()->user();
        return $check_user;

        $inappropriateWords = ['word1', 'word2', 'word3'];
        Validator::extend('no_inappropriate_words', function ($attribute, $value) use ($inappropriateWords) {
            foreach ($inappropriateWords as $word) {
                if (stripos($value, $word) !== false) {
                    return false; // If any inappropriate word is found, return false
                }
            }
            return true; // If no inappropriate words are found, return true
        });

        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'category_id' => 'required',
            'subscription_id' => 'required',
            'story_title' => 'required',
=======
    public function addStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => '',
            'category_id' => '',
            'subscription_id' => '',
            'story_title' => '',
>>>>>>> 16bb6a890d5928aa98318b5fdabe808ff2308f6e
            'story_image.*' => 'required|mimes:jpeg,png,jpg,gif,svg',
            'music' => '',
            'music_type' => '',
            'description' => 'no_inappropriate_words',
            'story_status' => '',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $subscription_id = $request->subscription_id;
        $subscription_details = Subscription::with('package')->where('id', $subscription_id)->first();
        $package_id = $subscription_details['package']['id'];
        $amount = $subscription_details['package']['amount'];
        $word_limit = $subscription_details['package']['word_limit'];
        $image_limit = $subscription_details['package']['image_limit'];

        if ($package_id == 1) {
            $wordLimit = $word_limit;
            $imageLimit = $image_limit;
        } elseif ($package_id == 2) {
            $wordLimit = $word_limit;
            $imageLimit = $image_limit;
        } elseif ($package_id == 3) {
            $wordLimit = $word_limit;
            $imageLimit = $image_limit;
        }
        // Validate description length based on word limit
        $descriptionLength = str_word_count($request->description);
        if ($descriptionLength > $wordLimit) {
            return response()->json(['message' => 'Description exceeds word limit for this subscription.'], 400);
        }

        // Validate image count based on image limit
        if (count($request->file('story_image')) > $imageLimit) {
            return response()->json(['message' => 'Number of images exceeds limit for this subscription.'], 400);
        }

        $story = new Story();
        $story->user_id = $request->user_id;
        $story->category_id = $request->category_id;
        $story->subscription_id = $request->subscription_id;
        $story->story_title = $request->story_title;
        $story->music_type = $request->music_type;
        $story->description = $request->description;
        $story_music = array();
        if ($request->hasFile('music')) {
            foreach ($request->file('music') as $music) {
                $musicName = time() . '.' . $music->getClientOriginalExtension();
                $music->move(public_path('music'), $musicName);
                $path = '/music/' . $musicName;
                $story_music[] = $path;
            }
        }
        $story_image = array();
        if ($request->hasFile('story_image')) {
            foreach ($request->file('story_image') as $image) {
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('story-image'), $imageName);
                $path = '/story-image/' . $imageName;
                $story_image[] = $path;
            }
        }
        $story->music = json_encode($story_music);
        $story->story_image = json_encode($story_image, true);
        $story->save();
        return response()->json([
            'message' => 'Story add successfully',
            'data' => $story,
            'music' => json_decode($story['music']),
            'image' => json_decode($story['story_image'])
        ], 200);
    }

    public function filterStoryByCategory(Request $request)
    {
<<<<<<< HEAD
        $category_id = $request->category_id;
        $category_name = $request->category_name;
        $story_title = $request->story_title;
        $username = $request->username;

        $query = Story::query()->with('category');

        if($category_id !== null) {
            $query->where('category_id', $category_id);
        }
        if($category_name !== null) {
            $query->whereHas('category', function($q) use ($category_name) {
                $q->where('category_name', $category_name);
            });
        }
        if($story_title !== null) {
            $query->where('story_title', 'like', '%' . $story_title . '%');
        }
        if($username !== null) {
            $query->whereHas('user', function($q) use ($username) {
                $q->where('fullName', $username);
            });
        }

        $story_list = $query->get();

        $formatted_stories = $story_list->map(function($story) {
            $story->story_image = json_decode($story->story_image);
            return $story;
        });

        return response()->json([
            'message' => 'success',
            'data' => $formatted_stories
        ]);
    }

    public function storyDetails(Request $request)
    {
        $story_id = $request->story_id;
        $story_details = Story::with('category','user')->where('id',$story_id)->get();

        $formatted_stories = $story_details->map(function($story){
            $story->story_image = json_decode($story->story_image);
            return $story;
        });

        return response()->json([
            'message' => 'success',
            'data' => $formatted_stories
        ]);
    }

    public function myStory()
    {
        $auth_user_id = auth()->user()->id;
        $story_details = Story::with('category','user')->where('id',$auth_user_id)->get();

        $formatted_stories = $story_details->map(function($story){
            $story->story_image = json_decode($story->story_image);
            return $story;
        });

        return response()->json([
            'message' => 'success',
            'data' => $formatted_stories
        ]);
    }

    public function pendingStory()
    {
        $auth_user_id = auth()->user()->id;
        $story_details = Story::where('user_id',$auth_user_id)->where('story_status',0)->get();

        $formatted_stories = $story_details->map(function($story){
            $story->story_image = json_decode($story->story_image);
            return $story;
        });

        return response()->json([
            'message' => 'success',
            'data' => $formatted_stories
        ]);
    }

    public function deleteStory(Request $request){

        $story_id = $request->story_id;

        $story = Story::find($story_id);
        if ($story) {
            $story_music = json_decode($story->music);
            $story_images = json_decode($story->story_image);


            foreach ($story_music as $musicPath) {
                $absoluteMusicPath = public_path($musicPath);

                if (file_exists($absoluteMusicPath)) {
                    unlink($absoluteMusicPath);
                }
            }

            // Delete each image file associated with the story
            foreach ($story_images as $imagePath) {
                $absoluteImagePath = public_path($imagePath);

                if (file_exists($absoluteImagePath)) {
                    unlink($absoluteImagePath);
                }
            }
            $story->delete();

            return response()->json([
                'message' => 'Story and associated files deleted successfully!'
            ],200);
        } else {
            return response()->json([
                'message' => 'Story Not Found'
            ],404);
        }
    }

    public function editStory(Request $request){

=======
        //        $stories = Story::all();
        //        return response()->json($stories);
        //        $story_list = [];
        //        foreach($stories as $story){
        //            $story_list = [
        //                ''
        //            ]
        //        }
>>>>>>> 16bb6a890d5928aa98318b5fdabe808ff2308f6e
    }

}
