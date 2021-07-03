<?php

$nameField = FCHelper::er($table,'name');
$defaultData = 	json_decode($table->default_data, true);
$dataPivots = [];
$realValuePuts = [];
if (is_array($defaultData)) {
	$pivotTable = $defaultData['pivot_table'];
	$originField = $defaultData['origin_field'];
	$targetTable = $defaultData['target_table'];
	$targetField = $defaultData['target_field'];
	$targetSelect = $defaultData['target_select'];
	$columns = [];
	foreach ($targetSelect as $key => $value) {
		$columns[] = $value;
	}

	$dataPivots = FCHelper::getDataPivot($pivotTable, $originField, $targetTable, $targetField, $columns);
	if (count((array)$dataItem) > 0) {
		$realValuePuts = FCHelper::getRealValuePuts($dataItem, $pivotTable, $originField, $targetField);
	}
}
?>
<div class="form-group">
	<p class="form-title" for="">{{FCHelper::er($table,'note')}} <span class="count"></span></p>
    <div class="box-search-pivot">
    	<textarea name="{{$nameField}}" class="hidden">{{implode(',', $realValuePuts)}}</textarea>
    	<input type="text" class="search{{$nameField}}" placeholder="Gõ để tìm kiếm" />
    	<button type="button" class="btnadmin choose{{$nameField}}">Bỏ chọn</button>
    </div>
    <ul class="listitem multiselect padding0 listitem{{$nameField}} show-attribute">
    	
    </ul>
    <script type="text/javascript">
        $(function () {
            function build{{$nameField}}() {
                var choose{{$nameField}} = $('.listitem{{$nameField}} input:checked');
                var str = '';
                for (var i = 0; i < choose{{$nameField}}.length; i++) {
                	if (str == '') {
                		str = $(choose{{$nameField}}[i]).val();
                	}
                	else{
                		str += ','+$(choose{{$nameField}}[i]).val();
                	}
                }
                return str;
            }
            if ($("textarea[name={{$nameField}}]").val().length > 0) {
                $(".choose{{$nameField}}").removeClass("hidden");
            } else {
                $(".choose{{$nameField}}").addClass("hidden");
            }
            function checkedAllParentByChild{{$nameField}}(parent, level) {
                while (level > 0) {
                    level--;
                    var parentLevelClass = ".level-" + level;
                    var parentLevel = parent.prevAll(parentLevelClass).first();
                    var input = parentLevel.find("input").first();
                    if (!input.prop("checked")) {
                        input.prop("checked", true);
                    }
                }
            }
            function unCheckedParentIfNoChildChecked{{$nameField}}(parent, level) {
                while (level > 0) {
                    level--;
                    var parentLevelClass = ".level-" + level;
                    var parentLevel = parent.prevAll(parentLevelClass).first();
                    var parentLevelIndex = parentLevel.index();
                    var parentLevelIndexByClass = parentLevel.index(parentLevelClass);
                    var nextParent = $(parentLevelClass).eq(parentLevelIndexByClass + 1);
                    var lies = parentLevel.parent().find("li");
                    var nextIndex = lies.length;
                    if (nextParent.length > 0) {
                        nextIndex = nextParent.index();
                    }
                    var needUncheckParent = false;
                    for (var i = parentLevelIndex + 1; i < nextIndex; i++) {
                        var input = lies.eq(i).find("input").first();
                        if (input.prop("checked")) {
                            needUncheckParent = true;
                            break;
                        }
                    }
                    if (!needUncheckParent) {
                        var parentInput = parentLevel.find("input").first();
                        parentInput.prop("checked", false);
                    }
                }
            }
            function checkedParent{{$nameField}}(element) {
                var currentValue = $(element).is(":checked");
                var parent = $(element).parents("li");
                var level = parent.data("level");
                level = parseInt(level);
                if (currentValue) {
                    checkedAllParentByChild{{$nameField}}(parent, level);
                } else {
                    unCheckedParentIfNoChildChecked{{$nameField}}(parent, level);
                }
            }
            $("body").on("click", ".listitem{{$nameField}} li input", function (event) {
                checkedParent{{$nameField}}(this);
                var str = build{{$nameField}}();
                $("textarea[name={{$nameField}}]").val(str);
                if (str.length > 0) {
                    $(".choose{{$nameField}}").removeClass("hidden");
                } else {
                    $(".choose{{$nameField}}").addClass("hidden");
                }
            });
            $(".choose{{$nameField}}").click(function (event) {
                event.preventDefault();
                var arr = $(".listitem{{$nameField}} li input").prop("checked", false);
                $(".listitem{{$nameField}} li").removeClass("choose");
                $("textarea[name={{$nameField}}]").val("");
                $(this).addClass("hidden");
            });
            $(".listitem{{$nameField}} li span.expand").click(function (event) {
                event.preventDefault();
                var text = $(this).text();
                var liparent = $(this).parents("li");
                var level = liparent.data("level");
                var idx = liparent.index(".level-" + level);
                var iidx = liparent.index();
                var nitem = $(".level-" + level).eq(idx + 1);
                if (nitem.length > 0) {
                    var nidx = nitem.index();
                    var pitem = liparent.next(".level-" + (level - 1));
                    var pidx = pitem.index();
                    nidx = nidx > pidx && pidx != -1 ? pidx : nidx;
                    for (var i = iidx + 1; i < nidx; i++) {
                        if (text == "+") {
                            $(".listitem{{$nameField}} li").eq(i).show();
                            $(".listitem{{$nameField}} li").eq(i).find("span.expand").text("-");
                        } else {
                            $(".listitem{{$nameField}} li").eq(i).hide();
                            $(".listitem{{$nameField}} li").eq(i).find("span.expand").text("+");
                        }
                    }
                }
                if (text == "+") $(this).text("-");
                else $(this).text("+");
            });
            $("body").on("input", ".search{{$nameField}}", function (event) {
                event.preventDefault();
                var val = $(this).val().toLowerCase();
                if (val == "") {
                    $(this).parent().next().find("li").show();
                } else {
                    var lis = $(this).parent().next().find("li");
                    for (var i = 0; i < lis.length; i++) {
                        var li = $(lis[i]);
                        var text = li.text().toLowerCase();
                        if (text.indexOf(val) != -1) {
                            li.show();
                        } else {
                            li.hide();
                        }
                    }
                }
            });
        });
    </script>
</div>
<style type="text/css">
.listitem.multiselect {
    height: 200px;
    min-width: 200px;
    border: 1px solid #aaa;
    padding: 10px;
    overflow: hidden;
}
.listitem{{$nameField}}{
	overflow: scroll !important;
}
.listitem{{$nameField}} li:nth-child(odd){
			background: #f8f7f7;
		}
	.listitem{{$nameField}} li.choose{
		    background: #d9d9d9ab;
	}
		.listitem{{$nameField}} li{
			position: relative;
		}
		
.listitem{{$nameField}} span.expand{
	    position: absolute;
    right: 0;
    background: #00923f;
    color: #fff;
    font-size: 18px;
    width: 24px;
    text-align: center;
    cursor: pointer;
    user-select: none;
}
</style>