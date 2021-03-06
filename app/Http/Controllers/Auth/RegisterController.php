<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => 'required|max:150|regex:/^([a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'apellido'=> 'required|max:150|regex:/^([a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'email' => 'required|email|max:255|unique:users',
            'direccion'=> 'required|max:255',
            'dui'=> 'required|digitS:9',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'direccion' => $data['direccion'],
            'dui' => $data['dui'],
            'fecha_nacimiento' => $data['fecha'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function register(Request $request){
    $this->validator($request->all())->validate();
     event(new Registered($user = $this->create($request->all())));
     Mail::to($user->email)->send(new ConfirmationEmail($user));
     return back()->with('status', 'Por favor confirme su dirección de Email.');
    }

    public function confirmEmail($token)
    {
        User::whereToken($token)->firstOrFail()->Verified();

        return redirect('login')->with('status', '¡Ahora estas confirmado! por favor inicie sesion.');
    }

}
