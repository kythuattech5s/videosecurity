<?php
namespace vanhenry\manager\controller;
use vanhenry\manager\model\HUser;
use Validator;
use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use vanhenry\manager\helpers\CT;
use vanhenry\manager\model\HGroupModule;
use vanhenry\manager\model\HGroupUser;
use vanhenry\manager\model\HModule;
use vanhenry\manager\model\HRole;
use Session;
use vanhenry\manager\middleware\RedirectIfAuthenticated;
class AuthController extends BaseAdminController
{
/*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $admincp = "";
    protected $redirectTo = 'esystem';
    protected $guard = 'h_users';
    protected $loginPath = 'esystem/login';
    protected $redirectPath = 'esystem';
    protected $redirectAfterLogout = 'esystem/login';
    protected $username = 'username';
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->admincp = \Config::get('manager.admincp');
        $this->redirectTo = $this->admincp;
        $this->redirectPath = $this->admincp;
        $this->loginPath = $this->admincp."/login";
        $this->redirectAfterLogout = $this->admincp."/login";
        $this->middleware('h_guest', ['except' => ['logout']]);
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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
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
        return HUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    public function getLogin()
    {
        return view('vh::login');
    }
    public function postLogin()
    {
    	return $this->authenticate();
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate()
    {
        $credentials = request()->only('username', 'password');
        if (Auth::guard($this->guard)->attempt($credentials)) {
            return $this->authenticated(request(), Auth::guard($this->guard)->user());
        }
        return view('vh::login');
    }
    protected function authenticated($request, $user){
        $_user= array(
            'user'=>$user,
            'menu'=>$this->getMenuUser($user->group),
            'module'=>$this->getModuleUserAccess($user->group)
            );
        Session::put(CT::$KEY_SESSION_USER_LOGIN, $_user);
        return redirect()->intended($this->redirectPath);
    }
    private function getMenuUser($idUser){
        $allGroupModule = HGroupModule::where('parent',0)->orderBy('ord','asc')->orderBy('id','asc')->get();
        $_userModule= collect(HRole::getGroupModuleByUserId($idUser))->sortBy('ord');
        foreach ($allGroupModule as $key => $value) {
            $pid = $value->id;
            $ret = $_userModule->filter(function($item) use($pid){
                return $item->parent == $pid;
            });
            $value->childs = $ret;
        }
        $allGroupModule = $allGroupModule->filter(function($item){
            return $item->childs->count()>0;
        });
        return $allGroupModule;
    }
    private function getModuleUserAccess($idUser){
        $arr =  HRole::getModuleByUserId($idUser);
        $ret = array();
        foreach ($arr as $key => $value) {
            $ret[$value->id] = $value;
        }
        return $ret;
    }
    public function logout()
    {
        Auth::guard($this->guard)->logout();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
}