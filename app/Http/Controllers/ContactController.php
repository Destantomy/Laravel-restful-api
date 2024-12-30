<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        // 1st thing 1st check the current logged in user
        $data = $request->validated();
        $user = Auth::user();
        // make new contact request
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();
        
        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        // 1st thing 1st check the current logged in user
        $user = Auth::user();
        // find to database current user logged in is true
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
                    ])->setStatusCode(404));
        }
        // else if user valid/exist
        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        // 1st thing 1st check the current logged in user
        $user = Auth::user();
        // find to database current user logged in is true
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
                    ])->setStatusCode(404));
        }
        // else if user valid/exist
        $data = $request->validated();
        $contact->fill($data);
        $contact->save();
        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        // 1st thing 1st check the current logged in user
        $user = Auth::user();
        // find to database current user logged in is true
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
                    ])->setStatusCode(404));
        }
        $contact->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection
    {
        // retrieve current user
        $user = Auth::user();
        $page = $request->input('page', 1); // input : page with default value 1st page
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);
        
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            $email = $request->input('email');
            if($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }

            $phone = $request->input('phone');
            if($phone) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);
    
        return new ContactCollection($contacts);
    }
}
