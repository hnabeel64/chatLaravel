<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $model;
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    public function index()
    {
        if (!auth()->check()) {
            return view('login');
        }
        $user = $this->model->getChatUsers();
        return view('home')->with(['user' => $user]);
    }
    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (auth()->attempt($validate)) {
            $response = response(['success' => true, 'data' => $this->model::where('id', auth()->user()->id)->get()]);
            return redirect()->route('home');
        }
        return redirect()->back()->withErrors('something went wrong');
    }
    public function dashboard()
    {
        if (auth()->check()) {
            $user = $this->model->getChatUsers();
            return view('home')->with(['user' => $user]);
        }
        return view('login');
    }

    public function logout()
    {
        if (auth()->check()) {
            $auth = auth()->logout();
            return redirect()->route('login');
        }
    }

    public function sendmessage(Request $request)
    {
        $sender = auth()->user()->id;
        $receiver = $request->receiver_id;
        $message = $request->message;
        return $this->model->sendMessages($sender, $receiver, $message);
    }
    public function getMessage($id)
    {
        $messages = $this->model->getMessages($id);
        return $messages;
    }

    public function getRefreshMessage(Request $request)
    {
        $messages = $this->model->getRefreshMessage($request);
        return $messages;
    }

}
