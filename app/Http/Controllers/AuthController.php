<?php

namespace App\Http\Controllers;

use App\Entities\Friend;
use App\Entities\InviteFriend;
use App\Mail\ConfirmationEmail;
use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Psy\Util\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'dob' => 'required',
            'photo' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        // Upload Image
        $image = $request->photo;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time().'.'.'png';
        File::put(public_path().'/img/'. $imageName, base64_decode($image));

        $request->merge(["photo" => url('/img/'.$imageName)]);
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input["device_UUID"]   = $request->device_UUID;
        $input["device_type"]   = strtolower($request->device_type);

        $user = User::create($input);

        // Check invitation
        $invitation = InviteFriend::where('email', $request->email)->first();
        if($invitation) {
            InviteFriend::where("id", $invitation->id)->update(["status" => "joined"]);
            Friend::create([
                "user_id" => $user->id,
                "status" => "accepted"
            ]);
        }

        // Send Confirmation Email
        User::where("id",$user->id)->update(["status" => "pending", "email_verified_at" => date("Y-m-d H:i:s")]);
        $emailBody = [
            "name"      => $user->name,
            "url"       => url('/confirmation_email/'.base64_encode($user->id))
        ];
        Mail::to($user->email)->send(new ConfirmationEmail($emailBody));

        return $this->sendResponse([], 'Your account successfully created! Please check your email for confirmation');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            if($user->status != "active"){
                return $this->sendError([], 'Your account is not verified, please check your email for confirmation');
            }

            $user->_token =  $user->createToken('MyApp')-> accessToken;
            User::where("id",$user->id)->update([
                "device_UUID"   => $request->device_UUID,
                "device_type"   => strtolower($request->device_type),
            ]);
            return $this->sendResponse($user, 'User login successfully.');
        }else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmation_email(Request $request){
        $segments = $request->segments();
        if(isset($segments[1])){
            $user_id = base64_decode($segments[1]);
            User::where("id",$user_id)->update(["status" => "active", "email_verified_at" => date("Y-m-d H:i:s")]);

            return response()->json(["message" => "Your email is verified, Now you can login"], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        $token = \Illuminate\Support\Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user = User::where("email", $request->email)->first();

        // Send Confirmation Email
        $emailBody = [
            "name"      => $user->name,
            "url"       => url('/reset-password/'.$user->email.'/'.$token)
        ];
        Mail::to($user->email)->send(new ForgotPasswordEmail($emailBody));

        return $this->sendResponse([], 'We have e-mailed your password reset link!');
    }

    public function submit_forgot_password(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email'=> $request->email])->delete();

        return back()->with('message', 'Your password has been changed!');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function showResetPasswordForm(Request $request) {
        $segments = $request->segments();
        return view('forgot_password_form', [
            'token'     => $segments[2],
            "email"     => $segments[1]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'dob' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        $data = [
            "name"      => $request->input("name"),
            "email"      => $request->input("email"),
            "dob"      => $request->input("dob")
        ];

        if($request->has("photo")) {
            // Upload Image
            $image = $request->photo;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '.' . 'png';
            File::put(public_path() . '/img/' . $imageName, base64_decode($image));

            $data["photo"] = url('/img/' . $imageName);
        }

        $user = User::where("id", Auth::user()->id)->update($data);

        return $this->sendResponse(Auth::user(), 'Profile Updated');
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match!');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->sendResponse($user, 'Password successfully changed!');
    }
}
