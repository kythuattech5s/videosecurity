<?php
namespace vanhenry\manager\middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use vanhenry\manager\controller\Admin;
use Session;
use vanhenry\manager\helpers\CT;
use vanhenry\helpers\helpers\JsonHelper as JsonHelper;
class HUserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'h_users')
    {
        $admincp = \Config::get('manager.admincp');
        $gu = Auth::guard($guard);
        if (!$gu->check()) {
            return redirect($admincp.'/login');
        }
        if(isset($request->table)){
            $table = $request->table;
            $action = $request->segment(2);
            if(!Admin::checkExistTable($table)){
                return redirect()->route('404');
            }
            $ret =  $this->checkPermission($table,$action);
            if($ret ==1){
                if($request->ajax()){
                    return JsonHelper::echoJson(400,trans('db::NO_PERMISSION'));
                }
                else{
                    return redirect()->route('no_permission');    
                }
            }
            elseif($ret ==2){
                return redirect()->route('404');
            }
            $transTable = \FCHelper::getTranslationTable($table);
            view()->share('transTable', $transTable);
        }
        else{
            $isMedia = $request->segment(2,"");
            if($isMedia=="media"){
                $table = "media";
                $action =$request->segment(3,"");
                $ret = $this->checkPermissionMedia($table,$action);
                if($ret ==1){
                    if($request->ajax()){
                        return JsonHelper::echoJson(400,trans('db::NO_PERMISSION'));
                    }
                    else{
                        return redirect()->route('no_permission');    
                    }
                }
                elseif($ret ==2){
                    return redirect()->route('404');
                }
            }
        }
        return $next($request);
    }
    private function checkPermissionMedia($table,$action){
        switch ($action) {
            case "createDir":
            case "uploadFile":
            case "uploadFile":
            case "duplicateFile":
                $_action = "insert";
                break;
            case "getInfoLasted":
            case "getInfoFileLasted":
            case "listFolder":
            case "listFolderMove":
            case "getDetailFile":
            case "manager":
            case "view":
                $_action="view";
            break;
            case "saveDetailFile":
            case "moveFile":
            case "rename":
                $_action="update";
            break;
            case "deleteFolder":
            case "deleteFile":
            case "deleteAll":
                $_action="delete";
            break;
            case "copyFile":
                $_action="copy";
            break;
            default:
                $_action="view";
            break;
        }
        $ret =  $this->checkPermission($table,$_action);
        return $ret;
    }
    private function checkPermission($table,$action){
        $ar = Session::get(CT::$KEY_SESSION_USER_LOGIN);
        if(!isset($ar) || !array_key_exists('module',$ar))
        {
            return 2;
        }
        $_action = "";
        switch ($action) {
            case 'view':
            case 'search':
            case 'getData':
            case 'getDataPivot':
            case 'getDataMenu':
            case 'import':
            case 'export':
            case 'jbsearch':
            case 'trashview':
            case 'getRecursive':
            case 'table-lang':
                $_action = 'view';
                break;
            case 'deleteAll':
            case 'delete':
                $_action = 'delete';
                break;
            case 'edit':
            case 'update':
            case 'save':
            case 'addAllToParent':
            case 'removeFromParent':
            case 'editableajax':
            case 'updateRefer':
            case 'trash':
            case 'backtrash':
            case 'do_assign':
                $_action ='update';
                break;
            case 'insert':
            case 'store':
            case 'storeAjax':
            case 'do_import':
                $_action='insert';
                break;
            case 'copy':
                $_action='copy';
                break;
        }
        $fil = collect($ar['module']);
        $fil= $fil->filter(function($item) use($table,$_action){
            return $item->table_map== $table && $item->name == $_action;
        });
        if($fil->count()<=0){ //No permmission
            return 1;
        }
        return 200;
    }
}