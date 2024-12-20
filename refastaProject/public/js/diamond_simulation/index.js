var ct_id = "";
var ct_range = {//カラッと数のレンジ
	"1" : Array(0.200, 0.299)
,	"2" : Array(0.300, 0.399)
,	"3" : Array(0.400, 0.499)
,	"4" : Array(0.500, 0.599)
,	"5" : Array(0.600, 0.699)
,	"6" : Array(0.700, 0.799)
,	"7" : Array(0.800, 0.899)
,	"8" : Array(0.900, 0.999)
,	"9" : Array(1.000, 1.499)
,	"10" : Array(1.500, 1.999)
,	"11" : Array(2.000, 2.999)
,	"12" : Array(3.000, 3.999)
,	"13" : Array(4.000, 4.999)
,	"14" : Array(5.000, 5.999)
,	"15" : Array(6.000, 6.999)
,	"16" : Array(7.000, 7.999)
,	"17" : Array(8.000, 8.999)
,	"18" : Array(9.000, 9.999)
,	"19" : Array(10.000, 10.999)
};

function set_ct() {
	decimalKetaLimit = 3;//少数以下３桁
	var check = isDecimal(document.getElementById("input_ct").value,2,decimalKetaLimit);
	var ct = document.getElementById("input_ct").value;

	if ((ct != "") && (sel_col_id == "" || sel_cla_id == "" || sel_cut_id == "" )) {
		return false;
	}

	ct_id="";
	for (var key in ct_range) {
		if (ct >= ct_range[key][0] && ct <= ct_range[key][1]) {
			ct_id = key;
			break;
		}
	}

	if (check) {
		if (ct_id == "") {
			return false;
		} else {
			return true;
		}
	}
}

function detail_menu() {
    var detail = document.getElementById("detail_calculate");
    var simuSelectChecked = document.querySelector('input[type="radio"].simu_select:checked');
    if (simuSelectChecked) {
        detail.style.display = "block";
    } else {
        if (detail.style.display === "none") {
            detail.style.display = "block";
        } else {
            detail.style.display = "none";
        }
    }
}
function showMoreShapes() {
    var more_shapes = document.getElementById("more_shapes");
    if (more_shapes.style.display === "none") {
        more_shapes.style.display = "block"; // 要素を表示
    } else {
        more_shapes.style.display = "none"; // 要素を非表示
    }
}
 // ラジオボタンが変更されたときに'simu_select'クラスを追加または削除するための関数を追加します
 function addSimuSelectClass() {
	document.querySelectorAll('input[type="radio"]').forEach(function(radioButton) {
		if (radioButton.checked) {
			radioButton.parentNode.classList.add('simu_select');
		} else {
			radioButton.parentNode.classList.remove('simu_select');
		}
	});
}
document.querySelectorAll('input[type="radio"], #checkbox_display_option').forEach(function(radioButton) {
	radioButton.addEventListener('change', addSimuSelectClass);
});
addSimuSelectClass();


window.onload = function(){


	//checkbox_display_color要素を取得して非表示にする
	const checkbox_display_color = document.getElementById("checkbox_display_color");
	checkbox_display_color.style.display = 'none'; 
	var sel_color_radios = document.querySelectorAll('#radio_btn_color input[type="radio"]');
	document.getElementById("checkbox_btn_color").addEventListener("click", function() {
		checkbox_display_color.style.display = ''; 
    });
	sel_color_radios.forEach(function(radio) {
		radio.addEventListener("click", function() {
			checkbox_display_color.style.display = 'none'; 
		});
	});

	// checkbox_display_cut要素を取得して非表示にする
	const checkbox_display_cut = document.getElementById("checkbox_display_cut");
	checkbox_display_cut.style.display = 'none'; 
	const radio_btn_cut = document.querySelectorAll('#radio_btn_cut input[type="radio"]');
	document.getElementById("checkbox_btn_cut").addEventListener("click", function() {
		checkbox_display_cut.style.display = ''; 
	});
	radio_btn_cut.forEach(function(radio) {
		radio.addEventListener("click", function() {
			checkbox_display_cut.style.display = 'none'; 
		});
	});
	document.getElementById("radio_btn_goodcut").addEventListener("click", function() {
		checkbox_display_cut.style.display = 'none'; 
	});

	// checkbox_display_clarity要素を取得して非表示にする
	const checkbox_display_clarity = document.getElementById("checkbox_display_clarity");
	checkbox_display_clarity.style.display = 'none'; 
	const radio_btn_clarity = document.querySelectorAll('#radio_btn_clarity input[type="radio"]');
	document.getElementById("checkbox_btn_clarity").addEventListener("click", function() {
		checkbox_display_clarity.style.display = ''; 
	});
	radio_btn_clarity.forEach(function(radio) {
		radio.addEventListener("click", function() {
			checkbox_display_clarity.style.display = 'none'; 
		});
	});
	
	//checkbox_display_labo要素を取得して非表示にする
	const checkbox_display_labo = document.getElementById("checkbox_display_labo");
	checkbox_display_labo.style.display = 'none'; 
	var radio_btn_labo = document.querySelectorAll('#radio_btn_labo input[type="radio"]');
	document.getElementById("checkbox_btn_labo").addEventListener("click", function() {
		checkbox_display_labo.style.display = ''; 

    });
	radio_btn_labo.forEach(function(radio) {
		radio.addEventListener("click", function() {
			checkbox_display_labo.style.display = 'none'; 
		});
	});


	const jigane_input_box = document.getElementById("jigane_input_box");
	jigane_input_box.style.display = 'none'; 
	document.getElementById("jigane_question_no").addEventListener("click", function() {
        jigane_input_box.style.display = 'none'; 
		const jigane_gram = document.getElementById("jigane_gram");
		jigane_gram.value = '';
		const jigane_name = document.getElementById("jigane_name");
		jigane_name.selectedIndex = '';
    });
	document.getElementById("jigane_question_yes").addEventListener("click", function() {
        jigane_input_box.style.display = ''; 
    });

	const calculate_4c_metal_price_avg = document.getElementById("calculate_4c_metal_price_avg");
	const calculate_4c_metal_price = document.getElementById("calculate_4c_metal_price");
	const explain_text = document.getElementById("explain_text");
	calculate_4c_metal_price_avg.style.display = 'none';
	calculate_4c_metal_price.style.display = 'none';
	explain_text.style.display = 'none';

}
