<!DOCTYPE html>
<html>
<head>
	<title>Manajemen Meeting</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="asset/plugins/datepick/jquery.datetimepicker.css"/ >
<script src="asset/plugins/datepick//jquery.js"></script>
<script src="asset/plugins/datepick//build/jquery.datetimepicker.full.min.js"></script>
<style type="text/css">

#form-container {
	width: 400px;
	margin: 100px auto;
}

input[type="text"] {
	border: 1px solid rgba(0, 0, 0, 0.15);
	font-family: inherit;
	font-size: inherit;
	padding: 8px;
	border-radius: 0px;
	outline: none;
	display: block;
	margin: 0 0 20px 0;
	width: 100%;
	box-sizing: border-box;
}

select {
	border: 1px solid rgba(0, 0, 0, 0.15);
	font-family: inherit;
	font-size: inherit;
	padding: 8px;
	border-radius: 2px;
	display: block;
	width: 100%;
	box-sizing: border-box;
	outline: none;
	background: none;
	margin: 0 0 20px 0;
}

.input-error {
	border: 1px solid red !important;
}

#event-date {
	display: none;
}

#create-event {
	background: none;
	width: 100%;
    display: block;
    margin: 0 auto;
    border: 2px solid #2980b9;
    padding: 8px;
    background: none;
    color: #2980b9;
    cursor: pointer;
}

</style>
</head>

<body>
<h2>New Meeting</h2>
  <form id="form1" action="tes_createvent.php" method="POST">
	<select id="prodi_name" name= "event-group" onchange="cek(this.value)">
		<option value="" selected>--Select Group--</option>
		<option value="1">Teknik Informatika</option>
		<option value="2">Teknik Kimia</option>
		<option value="3">Teknik Elektro</option>
		<option value="11">Teknologi Pangan</option>
		<option value="12">Teknik Industri</option>
	</select>

	<span></span>
	<div id="txtHint" style="margin-bottom: 20px;"></div>
	<span></span>

	<input type="text" id="event-title" name= "event-title" placeholder="What meeting..." autocomplete="on" require="on"/>
	
	
	<select id="event-place" name= "event-place" onchange="place(this.value)">
		<option value="" selected>--Take Place--</option>
		<option value="Ruang Prodi Teknik Informatika">Ruang Prodi Teknik Informatika</option>
		<option value="Ruang Prodi Teknik kimia">Ruang Prodi Teknik Kimia</option>
		<option value="Ruang Prodi Teknik Elektro">Ruang Prodi Teknik Elektro</option>
		
	</select>
	<input type='text' name='other' id='other' placeholder='alternative place...' autocomplete='on'  />

	<input type="text" id="event-start-time" name= "event-start-time" placeholder="Meeting Start Time" autocomplete="off" />
	<input type="text" id="event-end-time" name= "event-end-time" placeholder="Meeting End Time" autocomplete="off" />
	<textarea name= "event-description" placeholder="Description" rows="4" cols="50" ></textarea><br/>
	<input type="submit" name= "create-event" value="Create Event">
  </form>

<script>
	
	jQuery('#event-start-time, #event-end-time ').datetimepicker({
  formatDate:'Y.m.d',
  minDate:0,
  step: 15,
  disabledDates:['2020.08.20','2020.08.21','2020.10.28','2020.10.29','2020.10.30','2020.11.14','2020.12.24','2020.12.25','2020.12.28','2020.12.29','2020.12.31','2021.01.01', '2021.02.12','2021.03.11','2021.03.14','2021.04.02', '2021.04.04','2021.05.01','2021.05.13', '2021.05.14','2021.05.26','2021.06.01', '2021.07.20','2021.08.10', '2021.08.17','2021.10.19','2021.11.04', '2021.12.24','2021.12.25', '2021.12.31'],
  disabledWeekDays:[0],
  timepicker:true});
  $("#event-type").on('change', function(e) {
	if($(this).val() == 'ALL-DAY') {
		$("#event-date").show();
		$("#event-start-time, #event-end-time").hide();
	}					
	else {
		$("#event-date").hide(); 
		$("#event-start-time, #event-end-time").show();
	}
});
	  
<?php //	<button id="create-event">Create Event</button>?>

// Selected time should not be less than current time
/*function AdjustMinTime(ct) {
	
	var dtob = new Date(),
  		current_date = dtob.getDate(),
  		current_month = dtob.getMonth() + 1,
  		current_year = dtob.getFullYear();
  			
	var full_date = current_year + '-' +
					( current_month < 10 ? '0' + current_month : current_month ) + '-' + 
		  			( current_date < 10 ? '0' + current_date : current_date );


	if(ct.dateFormat('Y-m-d') == full_date)
		this.setOptions({ minTime: 0 });
	else 
		this.setOptions({ minTime: false });
}


// DateTimePicker plugin : http://xdsoft.net/jqplugins/datetimepicker/
$("#event-start-time, #event-end-time").datetimepicker({ format: 'Y-m-d H:i', minDate: 0, minTime: 0, step: 5, onShow: AdjustMinTime, onSelectDate: AdjustMinTime});
$("#event-date").datetimepicker({ format: 'Y-m-d', timepicker: false, minDate: 0 });

$("#event-type").on('change', function(e) {
	if($(this).val() == 'ALL-DAY') {
		$("#event-date").show();
		$("#event-start-time, #event-end-time").hide();
	}					
	else {
		$("#event-date").hide(); 
		$("#event-start-time, #event-end-time").show();
	}
});*/

// Send an ajax request to create event
$("#create-event").on('click', function(e) {
	if($("#create-event").attr('data-in-progress') == 1)
		return;

	var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
		error = 0,
		parameters;

	$(".input-error").removeClass('input-error');

	if(!blank_reg_exp.test($("#event-title").val())) {
		$("#event-title").addClass('input-error');
		error = 1;
	}
	if($("#event-title").val()==""){
		$("#event-title").addClass('input-error');
			error = 1;
	}

	if($("#event-type").val() == 'FIXED-TIME') {
		if(!blank_reg_exp.test($("#event-start-time").val())) {
			$("#event-start-time").addClass('input-error');
			error = 1;
		}		

		if(!blank_reg_exp.test($("#event-end-time").val())) {
			$("#event-end-time").addClass('input-error');
			error = 1;
		}
	}
	else if($("#event-type").val() == 'ALL-DAY') {
		if(!blank_reg_exp.test($("#event-date").val())) {
			$("#event-date").addClass('input-error');
			error = 1;
		}	
	}

	if(error == 1)
		return false;

	if($("#event-type").val() == 'FIXED-TIME') {
		// If end time is earlier than start time, then interchange them
		if($("#event-end-time").datetimepicker('getValue') < $("#event-start-time").datetimepicker('getValue')) {
			var temp = $("#event-end-time").val();
			$("#event-end-time").val($("#event-start-time").val());
			$("#event-start-time").val(temp);
		}
	}

	// Event details
	parameters = { 	title: $("#event-title").val(), 
					event_time: {
						start_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-start-time").val().replace(' ', 'T') + ':00' : null,
						end_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-end-time").val().replace(' ', 'T') + ':00' : null,
						event_date: $("#event-type").val() == 'ALL-DAY' ? $("#event-date").val() : null
					},
					all_day: $("#event-type").val() == 'ALL-DAY' ? 1 : 0,
				};
	
	$("#create-event").attr('disabled', 'disabled');
	$.ajax({
        type: 'POST',
        url: 'ajax.php',
        data: { event_details: parameters },
        dataType: 'json',
        success: function(response) {
        	$("#create-event").removeAttr('disabled');
        	alert('Event created with ID : ' + response.event_id);
        },
        error: function(response) {
            $("#create-event").removeAttr('disabled');
            alert(response.responseJSON.message);
        }
    }); 

});

</script>
<script>
function cek(str) {
  var xhttp;
  if (str == "") {
    document.getElementById("txtHint").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("txtHint").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "getuser.php?val="+str, true);
  xhttp.send();
}
</script>
<script>
	function member_toggle(source) 
			{
			  var checkboxes = document.getElementsByName('member[]');
			  if (source.checked) {
			  for(var i=0, n=checkboxes.length;i<n;i++) 
			  {
				if (checkboxes[i].type == 'checkbox' ) {
                   checkboxes[i].checked = true;
               }
			  }
			}else{
				for(var i=0, n=checkboxes.length;i<n;i++) 
			  {
				if (checkboxes[i].type == 'checkbox' ) {
                   checkboxes[i].checked = false;
               }
			  }
			}
			}

</script>

<script>
function place(str) {
	if (str == "") {
		document.getElementById("other").readOnly = false;
	}else{
		document.getElementById("other").readOnly = true;
		}
}

</script>

</body>
</html>