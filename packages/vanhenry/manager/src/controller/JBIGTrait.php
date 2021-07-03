<?php 
namespace vanhenry\manager\controller;
use \App\Question;
use Illuminate\Http\Request;
trait JBIGTrait{
	// const KEY_STATUS_LOCK1 = 1;
	// const KEY_STATUS_LOCK2 = 2;
	// const KEY_STATUS_FREE = 3;
	public function jbsearch(\Request $request,$table){
		if(request()->isMethod("post")){
			$inputs= request()->input();
			$cate = isset($inputs["cate"])?$inputs["cate"]:0;
			$name = isset($inputs["name"])?$inputs["name"]:"";
			$q = Question::select("id","code","name")->where("act",1)->whereRaw("(trash <> 1 or trash is null)");
			if($cate!=0){
				$q = $q->where("id_cate",$cate);
			}
			if($name!=""){
				$q= $q->where("name","LIKE","%".$name."%");
			}
			$arr = $q->get();
			return $arr->toJson();
		}
	}
	public function getChapterById(\Request $request,$bookId){
		$chapters = \App\Chapter::where("book_id",$bookId)->where("act",1)->get();
		return response()->json($chapters);
	}
	public function getQuestionByChapter(Request $request){
		$post = $request ->input();
		$code = isset($post["code"])?$post["code"]:"";
		$name = isset($post["name"])?$post["name"]:"";
		$cate = (int)(isset($post["cate"])?$post["cate"]:"");
		$q = \App\Question::select("chapter_id","id","name")->where("act",1);
		if($code!=""){
			$q = $q->where("code", "like","%".$code."%");
		}
		if($name!=""){
			$q = $q->where("name", "like","%".$name."%");
		}
		if($cate>0){
			$q = $q->where("cate_id",$cate);
		}
		$questions = $q->orderBy("name")->paginate(50);
		
		return response()->json($questions);
	}
	public function getExamByChapter(Request $request){
		
		$post = $request ->input();
		$code = isset($post["code"])?$post["code"]:"";
		$name = isset($post["name"])?$post["name"]:"";
		$cate = (int)(isset($post["cate"])?$post["cate"]:"");
		$q = \App\Exam::select("chapter_id","id","name")->where("act",1);
		if($code!=""){
			$q = $q->where("code", "like","%".$code."%");
		}
		if($name!=""){
			$q = $q->where("name", "like","%".$name."%");
		}
		if($cate>0){
			$q = $q->where("cate_id",$cate);
		}
		$exams = $q->orderBy("name")->paginate(50);
		
		
		return response()->json($exams);
	}
	public function getBoxAjaxQuestion(Request $request,$bookId,$chapterId){
		return view("vh::more.jb.ajax_choose_tuluan");
	}
	public function getDetailChapter(Request $request,$bookId,$chapterId){
		if($request->isMethod("post")){
			$post = $request->input();
			$name = isset($post["name"])?$post["name"]:"";
			$ord = isset($post["ord"])?$post["ord"]:"";
			if($chapterId==0){
				$chapter = new \App\Chapter;
				$chapter->name = $name;
				$chapter->ord = $ord;
				$chapter->act = 1;
				$chapter->book_id = $bookId;
				$chapter->save();
			}
			else{
				$chapter = \App\Chapter::find($chapterId);
				$chapter->book_id = $bookId;
				$chapter->save();
			}
			$groupQuestions = \App\QuestCategory::where("act",1)->get();
			return view("vh::more.jb.ajax_chapter_item",compact("chapter","groupQuestions"));
		}
		if($chapterId==0){
			return view("vh::more.jb.ajax_chapter_item");
		}
	}
	public function deleteChapter(Request $request,$chapterId){
		if($chapterId>0){
			\App\Chapter::where("id",$chapterId)->delete();
			\App\ChapterQuestion::where("chapter_id",$chapterId)->delete();
			\App\ChapterExam::where("chapter_id",$chapterId)->delete();
			return response()->json(["code"=>200]);
		}
	}
}
?>