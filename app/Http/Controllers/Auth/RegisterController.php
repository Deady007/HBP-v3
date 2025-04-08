<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Mail\RegistrationSuccess;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function create(array $data)
    {
        Log::info('Create user data: ', $data);
        $currentYear = date('Y');
        $lastUser = User::whereYear('created_at', $currentYear)->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastUser ? intval(substr($lastUser->unique_id, -4)) + 1 : 1;
        $uniqueId = sprintf('USR-%s-%04d', $currentYear, $sequenceNumber);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number' => $data['phone_number'],
            'unique_id' => $uniqueId,
        ]);

        $userRole = Role::where('name', 'User')->first();
        $user->assignRole($userRole);

        $mailData = [
            'id' => $user->unique_id,
            'email' => $user->email,
            'password' => $data['password']
        ];

        Mail::to($user->email)->send(new RegistrationSuccess($mailData));

        return $user;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        Log::info('Validator data: ', $data);
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'phone_number' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:5', // Minimum 5 characters
                'regex:/[A-Z]/', // At least one uppercase letter
                'regex:/[a-z]/', // At least one lowercase letter
                'regex:/[@#$%&]/', // At least one special character
            ],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        Auth::login($user);

        return redirect($this->redirectTo);
    }
}
