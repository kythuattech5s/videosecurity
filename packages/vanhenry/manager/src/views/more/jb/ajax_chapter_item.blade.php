<div class="col-xs-12 _inject_chapter_item">
    <div class="col-xs-12 " style="padding-right: 0">
      <h4 class="_inject_question_title_chapter">{{$chapter->name}} 
        <span class="fa fa-plus expand"></span>
        <span data-chapterid="{{$chapter->id}}" class="fa fa-trash remove"></span>
      </h4>

    </div>
    <div class="box-content clearfix" style="display: none">
      <h4 class="col-xs-12">CÂU TỰ LUẬN</h4>
      <div class="col-md-6 col-xs-12">
        
        <input type="hidden" class="_inject_questions" name="_inject_questions[{{$chapter->id}}]" value='{{$chapter->questions}}'>
        <div class="_inject_search_chapters_question row">
          
          <div class="col-md-5">
            <input type="text" placeholder="Nhập code câu hỏi tự luận" class="_inject_search_code_question">
          </div>
          <div class="col-md-5">
            <input type="text" placeholder="Nhập tên câu hỏi tự luận" class="_inject_search_name_question">
          </div>
          <div class="col-md-2">
            <button style="width: 100%;" class="filter" type="button">Lọc</button>
          </div>
          <div class="col-md-12">
            <select class="select2 _inject_search_cate_question" style="margin-top: 3px">
              <option value="0">Tất cả danh mục</option>
              @foreach($groupQuestions as $grq)
              <option value="{{$grq->id}}">{{strip_tags($grq->name)}}</option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class=" _inject_list_question">
            <span class="checkall">Chọn tất cả</span>
            <ul>
              
            </ul>
            <p class="hidden next_pagination" ></p>
        </div>
      </div>
      <div class="col-md-6 col-xs-12">
         <?php $questions = \App\ChapterQuestion::select("name","chapter_id","question_id","type",'ord')->where("chapter_id",$chapter->id)->orderBy('ord')->get() ?>
        <p style="    color: #fb0000;text-align: right">Có <strong class="count_questions">{{count($questions)}}</strong> mục lục</p>
        
        <div class="row _inject_list_question_choosen">
          <span class="removeall">Xóa tất cả</span>
            <ul>
             
              @foreach($questions as $question)
              <li data-id="{{$question->question_id}}" data-chapterid="{{$question->chapter_id}}">
                <input onclick="return false;" checked="checked" type="checkbox"> <span class="main">{{$question->name}}</span>

                <span title="Khóa cấp 1 (Hiển thị đáp án, không hiển thị đáp án đúng)" class="fa fa-unlock-alt action lock1 {{$question->type==1?'active':''}} "></span>
                <span title="Khóa cấp 2 (Không hiển thị đáp án, Giải thích, Bình luận)" class="fa fa-lock action lock2 {{$question->type==2?'active':''}}"></span>
                <span title="Free" class="fa fa-globe action free {{$question->type==3?'active':''}}"></span>
                <input value="{{$question->ord}}" type="text" class="ord">
              </li>
              @endforeach
            </ul>
        </div>
      </div>

    </div>
    <div class="box-content-exam clearfix" style="display: none">
      <h4 class="col-xs-12">CÂU TRẮC NGHIỆM</h4>
      <div class="col-md-6 col-xs-12">
        
        <input type="hidden" class="_inject_exams" name="_inject_exams[{{$chapter->id}}]" value='{{$chapter->exams}}'>
        <div class="_inject_search_chapters_exam row">
          
          <div class="col-md-5">
            <input type="text" placeholder="Nhập code câu hỏi tự luận" class="_inject_search_code_exam">
          </div>
          <div class="col-md-5">
            <input type="text" placeholder="Nhập tên câu hỏi tự luận" class="_inject_search_name_exam">
          </div>
          <div class="col-md-2">
            <button style="width: 100%;" class="filter" type="button">Lọc</button>
          </div>
          <div class="col-md-12">
            <select class="select2 _inject_search_cate_exam" style="margin-top: 3px">
              <option value="0">Tất cả danh mục</option>
              @foreach($groupQuestions as $grq)
              <option value="{{$grq->id}}">{{strip_tags($grq->name)}}</option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class=" _inject_list_exam">
            <span class="checkall">Chọn tất cả</span>
            <ul>
              
            </ul>
            <p class="hidden next_pagination" ></p>
        </div>
      </div>
      <div class="col-md-6 col-xs-12">
        <?php $exams = \App\ChapterExam::select("name","chapter_id","exam_id","type",'ord')->where("chapter_id",$chapter->id)->orderBy('ord')->get() ?>
        <p style="    color: #fb0000;text-align: right">Có <strong class="count_exams">{{count($exams)}}</strong> mục lục</p>
        
        <div class="row _inject_list_exam_choosen">
          <span class="removeall">Xóa tất cả</span>
            <ul>
              
              @foreach($exams as $exam)
              <li data-id="{{$exam->exam_id}}" data-chapterid="{{$exam->chapter_id}}">
                <input onclick="return false;" checked="checked" type="checkbox"> <span class="main">{{strip_tags($exam->name)}}</span>
                <span title="Khóa cấp 1" class="fa fa-unlock-alt action lock1 {{$exam->type==1?'active':''}} "></span>
                <span title="Khóa cấp 2" class="fa fa-lock action lock2 {{$exam->type==2?'active':''}}"></span>
                <span title="Free" class="fa fa-globe action free {{$exam->type==3?'active':''}}"></span>
                <input value="{{$exam->ord}}" type="text" class="ord">
              </li>
              @endforeach
            </ul>
        </div>
      </div>
    </div>
  </div>