

function popup_show(_title, _height, _width, _html, _callback, _staticblock)
{
	var h = parseInt(_height);
	var w = parseInt(_width);

	// Create popup
	var p = document.createElement('div');
	p.id = 'curacao_popup';
	p.style.height = h.toString() + 'px';
	p.style.width = w.toString() + 'px';
	p.style.left = '50%';
	p.style.top = '50%';
	p.style.marginLeft = '-' + ((w+10) / 2).toString() + 'px';
	p.style.marginTop = '-' + (h / 2).toString() + 'px';
	p.style.display = 'none';
	p.style.zIndex = 9999998;
	p.style.position = 'fixed';
	p.style.border = '5px solid #008ecf';
	p.style.borderRadius = '10px';
	p.style.backgroundColor = '#FFFFFF';

	// Create logo
	var l = document.createElement('img');
	l.id = 'curacao_popup_logo';
	l.style.height = '80px';
	l.style.width = '80px';
	l.src = location.protocol + '/ci_media/images/popup_circleFlower.png';
	l.style.position = 'absolute';
	l.style.left = '25px';
	l.style.top = '-20px';
	l.style.zIndex = 9999999;
	
	// Create title
	var t = document.createElement('div');
	t.id = 'curacao_popup_title';
	t.style.height = '50px';
	t.style.width = 'auto'; //(w - 165).toString() + 'px';
	t.style.lineHeight = '50px';
	t.style.color = '#FFFFFF';
	t.style.fontFamily = 'Arial';
	t.style.fontSize = '18px';
	t.style.fontWeight = 'bold';
	t.style.padding = '0px 45px 0px 120px';
	t.style.backgroundColor = '#008ecf';
	t.style.textAlign = 'left';
	t.innerHTML = _title.toUpperCase();

	// Create close button
	var c = document.createElement('img');
	c.id = 'curacao_popup_close';
	c.style.position = 'absolute';
	c.style.right = '10px';
	c.style.top = '10px';
	c.style.height = '25px';
	c.style.width = '25px';
	c.style.zIndex = 9999999;
	c.style.cursor = 'pointer';
	c.src = location.protocol + '/ci_media/images/popup_closeBtn.png';
	c.onclick = function(){ popup_hide(); };
	
	// Create content
	var cc = document.createElement('div');
	cc.id = 'curacao_popup_content';
	cc.style.height = (h - 100).toString() + 'px';
	// cc.style.width = 'auto';
	//little change for ab-60 display
	cc.style.overflowX = 'hidden';
	cc.style.overflowY = 'auto';
	cc.style.padding = '20px';
	cc.style.color = '#363636';
	cc.style.textAlign = 'left';
	cc.style.backgroundColor	= '#FFFFFF';

	// Create background
	var b = document.createElement('div');
	b.id = 'curacao_popup_background';
	b.style.height = '100%';
	b.style.width = '100%';
	b.style.backgroundColor = 'rgba(0,0,0,0.5)';
	b.style.display = 'none';
	b.style.zIndex = 9999997;
	b.style.top = '0';
	b.style.left = '0';
	b.style.position = 'fixed';
	b.onclick = function(){ popup_hide(); };

	// Append to body
	p.appendChild(l);
	p.appendChild(t);
	p.appendChild(t);
	p.appendChild(c);
	p.appendChild(cc);
	document.body.appendChild(b);
	document.body.appendChild(p);

	if(_staticblock)
	{ 
		$j.post("https://icuracao.com/credit-app",
		{
			link: '://icuracao.com/',
			staticblock: _staticblock
		},
		function(_data, _status)
		{
			document.getElementById("curacao_popup_content").innerHTML = _data;
			document.getElementById("curacao_popup").style.display = 'block';
			document.getElementById("curacao_popup_background").style.display = 'block';
	
			if(typeof _callback === "function"){ _callback(); }
		});		
	}
	else
	{
		document.getElementById("curacao_popup_content").innerHTML = _html;
		document.getElementById("curacao_popup").style.display = 'block';
		document.getElementById("curacao_popup_background").style.display = 'block';
	
		if(typeof _callback === "function"){ _callback(); }
	}
}

function popup_hide()
{
	stopTimer = true;
	//reset timer if user clicks somewhere else to hide the pop up
	resetTimer();
	bindSessionListeners();
	
	// Clear Timeout
	// if((typeof timeleftcheck != 'undefined')){ clearInterval(timeleftcheck); timeout_script(); }
	
	var p = document.getElementById("curacao_popup");
	var b = document.getElementById("curacao_popup_background");
	p.parentNode.removeChild(p);
	b.parentNode.removeChild(b);
}
 
// Runs the timeout script
function timeout_script()
{
	// Check if timeout has occurred
	//setTimeout(function(){clearInterval(interval)}, 18000000);
	setTimeout(function()
	{
		popup_show('{TIMEOUT_TITLE}', 170, 450, '', function()
		{
			var c = document.getElementById('curacao_popup_content');
			
			// Create content
			var h = document.createElement('div');
			h.id = 'timeout_holder';
			h.style.textAlign = 'center';
			
			var h2 = document.createElement('div');
			h2.id = 'timeout_button_holder';
			h2.style.textAlign = 'center';
			h2.style.padding = '20px 20px 0px 20px';
			
			// Create content
			var s1 = document.createElement('span');
			s1.id = 'timeout_holder_span';
			s1.style.fontFamily = 'Arial';
			s1.style.fontSize = '14px';
			s1.innerHTML = '{TIMEOUT_SAYING1}';
			
			var s2 = document.createElement('span');
			s2.id = 'timeout_timeleft';
			s2.innerHTML = '60 {TIMEOUT_SAYING2}!';
			s2.style.fontFamily = 'Arial';
			s2.style.fontSize = '14px';
			s2.style.fontWeight = 'bold';
			
			var s3 = document.createElement('span');
			s3.style.cursor = 'pointer';
			s3.style.margin = '0px 5px';
			s3.width = '120px';
			s3.id = 'timeout_holder_continue';
			s3.innerHTML = '{TIMEOUT_BUTTON1}';
			s3.style.backgroundColor = '#008ecf';
			s3.style.borderRadius = '5px';
			s3.style.padding = '8px 10px';
			s3.style.fontFamily = 'Arial';
			s3.style.fontSize = '12px';
			s3.style.fontWeight = 'bold';
			s3.style.textAlign = 'center';
			s3.style.color = '#FFFFFF';
			s3.onclick = function(){ popup_hide(); };
			
			var s4 = document.createElement('span');
			s4.style.cursor = 'pointer';
			s4.style.margin = '0px 5px';
			s4.width = '120px';
			s4.id = 'timeout_holder_quit';
			s4.innerHTML = '{TIMEOUT_BUTTON2}';
			s4.style.backgroundColor = '#f26722';
			s4.style.borderRadius = '5px';
			s4.style.padding = '8px 10px';
			s4.style.fontFamily = 'Arial';
			s4.style.fontSize = '12px';
			s4.style.fontWeight = 'bold';
			s4.style.textAlign = 'center';
			s4.style.color = '#FFFFFF';
			s4.onclick = function(){ window.location.href = '{TIMEOUT_QUIT_LINK}'; };
			
			var s5 = document.createElement('script');
			s5.innerHTML = 'var timeleft = 60;';
			s5.innerHTML += 'var timeleftcheck = setInterval(function(){'; 
			s5.innerHTML += 'document.getElementById("timeout_timeleft").innerHTML = (timeleft--) + " {TIMEOUT_SAYING2}!";';
			s5.innerHTML += 'if(timeleft<1){ window.onbeforeunload = null; window.location.replace("{LINK_TIMEOUT}"); }';
			s5.innerHTML += '}, 1000);';
			
			h.appendChild(s1);
			h.appendChild(s2);
			h2.appendChild(s3);
			h2.appendChild(s4);
			c.appendChild(h);
			c.appendChild(h2);
			c.appendChild(s5);
		});
		
	}, 1200000);
	 
}
 
// Start timeout
// timeout_script();

function is_int(value) {
  if ((parseFloat(value) == parseInt(value)) && !isNaN(value)) {
    return true;
  } else {
    return false;
  }
}

function isnull(_o) {
  return (typeof _o === 'undefined' || _o === null || (typeof _o === 'string' && _o === '')) ? true : false;
}

//Checks if a license is valid
function validdl(_state, _license)
{
	switch(_state.toUpperCase())
	{
		case 'AZ' : 

			var az1 = /^[A-Za-z]{1}[0-9]{8}$/;
			// var az2 = /^[A-Za-z]{2}[0-9]{2,5}$/;console.log(az2.test(_license));
			var az3 = /^[0-9]{9}$/;
			//if (az1.test(_license) || az2.test(_license) || az3.test(_license)) { return true; }    
			if (az1.test(_license) || az3.test(_license)) { return true; }    
			return false;

		case 'CA' : 

			var ca1 = /^[A-Za-z]{1}[0-9]{7}$/;
			if (ca1.test(_license)) { return true; }
    		return false;

		case 'NV' : 

			var nv1 = /^[0-9]{9}$/;
			var nv2 = /^[0-9]{10}$/;
			var nv3 = /^[0-9]{12}$/;
			var nv4 = /^[xX]{1}[0-9]{8}$/;			
			if (nv1.test(_license) || nv2.test(_license) || nv3.test(_license) || nv4.test(_license)) { return true; }
			return false;

		default : return false;
	}

}

//TODO:needs different rules for verify the id number for docB 
function validIntNumber(idtype,idnumber){	
	
	switch (idtype.toUpperCase()) {
		default:
			console.log('unknown idtype',idtype);
		break;
	}
	return true;
}

function formatname(_ev)
{
	// Check if tab and exit
	if(_ev.keyCode == 9 || _ev.keyCode == 8 || _ev.keyCode == 37 || _ev.keyCode == 39 || _ev.keyCode == 46){ return true; }
	
	var c = String.fromCharCode(_ev.which);
	var regex = /^[A-Za-z ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏàáâãäåæçèéêëìíîïÐÑÒÓÔÕÖØÙÚÛÜÝÞßðñòóôõöøùúûüýþÿ\.\'\,\(\)\-\&]+$/;
	if(!regex.test(c)){ _ev.preventDefault(); return false; }
    return true;
}

function formatstreet(_ev)
{
	// Check if tab and exit
	if(_ev.keyCode == 9 || _ev.keyCode == 8 || _ev.keyCode == 37 || _ev.keyCode == 39 || _ev.keyCode == 46){ return true; }
	
	var c = String.fromCharCode(_ev.which);
	var regex = /^[A-Za-z0-9 \.\'\,\(\)\#\;\:\°]+$/;
	if(!regex.test(c)){ _ev.preventDefault(); return false; }
    return true;
}

function formataptnumber(_ev)
{
	// Check if tab and exit
	if(_ev.keyCode == 9 || _ev.keyCode == 8 || _ev.keyCode == 37 || _ev.keyCode == 39 || _ev.keyCode == 46){ return true; }
	
	var c = String.fromCharCode(_ev.which);
	var regex = /^[0-9A-Za-z \'\-\.\/\#]+$/;
	if(!regex.test(c)){ _ev.preventDefault(); return false; }
    return true;
}

function emailRegex(_email){
	//basic regex email check
	var r =  /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(!r.test(_email)){ return false }
	return true;
}

function validIncome(_income){
	_income = _income.replace(',','');
	//if income contains non numeric digit
	if(_income.match(/\D/)){
		return false;
	}
	return true;
}


function validRefcode(_refcode){
	let pattern = /^[a-z0-9]+$/i;
	return pattern.test(_refcode);
}

function validpass(_password)
{
	var r = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
	if(!r.test(_password)){ return false; }
    return true;
}

function validssn(_ssn)
{
	var r = /^(?!\b(\d)\1+-(\d)\1+-(\d)\1+\b)(?!123-45-6789|219-09-9999|078-05-1120)(?!666|000|9\d{2})\d{3}-(?!00)\d{2}-(?!0{4})\d{4}$/;
	if(!r.test(_ssn)){ return false; }
    return true;
}

function validphone(_phone)
{
	var r = /^\((?!888|877|800|866|855|844|833|822|411|211|311|511|555|611|711|811|911|950)[2-9]{1}([0-9]{2})\)(?!555-01[0-9]{2})(([2-9]{1}(?!11)[0-9]{2})-[0-9]{4})$/;
	if(!r.test(_phone)){ return false; }
    return true;
}

function validName(name){
	let r =/^[a-zA-Z]*$/;
	if(!r.test(name)){
		return false;
	} 
	return true;
}

function esp(){
	if(window.location.href.indexOf("esp") != -1){
		return true;
	}
	return false;
}

function start_application(){
	$('#loader').show();
	window.location.href = `${urlPrepath}step1`;
	
}

//numbers only is used for the phonenumebr, dob and monthly income
function numbersOnly(event){	
	
	var pattForNumber = /[0-9]/;
	let actionKey = ["Backspace","Delete","ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Tab"];
	
	let key = event.key;
	//stop the non-number typing and allow delete 
	if(!pattForNumber.test(event.key) && actionKey.indexOf(event.key) == -1){
		event.preventDefault();
		return false;
	}
}

function charactersOnly(event){
	 let pattForCharacters = /[a-zA-Z]/;
	 let actionKey = ["Backspace","Delete","ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Tab"];
 	
 		let key = event.key;
 		//stop the non-characters typing and allow delete 
 		if(!pattForCharacters.test(event.key) && actionKey.indexOf(event.key) == -1){
 			event.preventDefault();
 			return false;
 		}
}

function charactersNumbersOnly(event){
	let pattern = /[a-zA-Z0-9]/;
	let actionKey = ["Backspace","Delete","ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Tab"];
 
	 let key = event.key;
	 //stop the non-characters typing and allow delete 
	 if(!pattern.test(event.key) && actionKey.indexOf(event.key) == -1){
		 event.preventDefault();
		 return false;
	 }
}



//update the city and state name, hide empty error
function dynamicZipHelper(city,state){
	$('#step2-city').val(city);
	$('#step2-state').val(state);
	
	let efields = ['step2-city','step2-state'];
	
	hide_error(efields);
	
	unknown_zip = 0;
}

var success_dynamic_zip = 0;
//pop city and state according to zip code
function dynamicZip(event){
	numbersOnly(event);
	
	var el = $(this);
	if(el.val().length < 5) return;
	
	if((el.val().length == 5 && is_int(el.val()))){
		$.ajax({
			url:"https://zip.getziptastic.com/v2/US/"+el.val(),
			cache:false,
			dataType:"json",
			type:"GET",
			success:function(result,success){
				if(result.state_short === 'CA' || result.state_short === 'AZ' || result.state_short === 'NV'){
					dynamicZipHelper(result.city,result.state_short);
					success_dynamic_zip = 1;
				}else{
					unknown_zip = 1;
				}
			},
			error:function(result,success){
				unknown_zip = 1;
				
			}
		});
	}else{
		console.log('invalid zip code');
	}
}

//after this input reaches the goal, focus to the next input box
function focusNext(event){
	
	let next = $(this).next().attr('id');
	if($(this).val().length === 2 && event.keyCode != 8 && event.keyCode != 46){
		$(`#${next}`).focus();
	}
}

//stop numbers and format the income as money
function formatIncome(event){
	numbersOnly(event);
	let income = parseInt($('#step2-income').val().replace(',',''),10);
	if(!isNaN(income)){
		$('#step2-income').val(income.toLocaleString());
		if(income < 400 && !$('#step2-income').is(':focus')){
			$('#income-low').show();
		}else{
			$('#income-low').hide();
			invalid_income = 0;
		}
	}else{
		invalid_income = 1;
	}
	if(income >= 8000){
		$('#step2-income-confirm').show();
	}else{
		$('#step2-income-confirm').hide();
	}	
}

//switch the doc view choice between docA and docB
function switchDoc(event) {
	
	let curr = event.data.choice;
	let other = event.data.other;
  let prepath = '/skin/frontend/enterprise/default/images/organicCreditApp2018/';
	
  $(curr).css('border-color', '#008ecf');
  $(curr).children().eq(1).css('color', '#008ecf');
  $(other).css('border-color', 'grey');
	$(other).children().eq(1).css('color', 'grey');
	//change view only
	if(curr === '#us-id'){
		$(`${curr}-image`).attr('src', `${prepath}/docA_active.png`);
	  $(`${other}-image`).attr('src', `${prepath}/docB_inactive.png`);
		setTimeout(function(){
			docForm('docA');
		},500);
	}else{
		$(`${curr}-image`).attr('src', `${prepath}/docB_active.png`);
	  $(`${other}-image`).attr('src', `${prepath}/docA_inactive.png`);
		setTimeout(function(){
			docForm('docB');
		},500);
	}
}

//show form for docA and docB
function docForm(choice){
	$('#step3').hide();
	if(choice === 'docA'){
		go_step('step3-us');
		$('#step3-us').show();
	  $('#step3-int').hide();
	}else{
		go_step('step3-int');
		$('#step3-us').hide();
	  $('#step3-int').show();
	}
}

//close the error message
function closeCurrent(element){
	var parent = element.parentNode;
	parent.style.display = 'none';
}

//remove the highlight on doc choice at step3
function resetDoc(){
	$('#us-id,#int-id').css('border-color','grey');
	$('#us-id .id-image-desc').css('color','grey');
	$('#int-id .id-image-desc').css('color','grey');
	let prepath = '/skin/frontend/enterprise/default/images/organicCreditApp2018/';
	$('#us-id-image').attr('src',`${prepath}docA_inactive.png`);
	$('#int-id-image').attr('src',`${prepath}docB_inactive.png`);
}

//for step3 showing id subtypes for international country
function intCountry(){
	let idtype = $('#step3-int-idtype');
	let idtypes = {
		'ES':{'':'SELECT','BE1':'LICENCIA DE CONDUCIR','BE2':'DOC UNICO DE IDENTIDAD','BE3':'PASAPORTE','BE4':'OTRO EL SALVADOR ID ACEPTADO','id-not-support':'Other'},
		'GU':{'':'SELECT','BG1':'LICENCIA DE MANEJO','BG2':'IDENTIFICACION CONSULAR','BG3':'PASAPORTE','BG4':'OTRO GUATE ID ACEPTADO','BG5':'NEW MATRICULA CONSULAR','id-not-support':'Other'},
		'MX':{'':'SELECT','BM1':'NUEVA MATRIC CONSULAR','BM2':'ANTIGUA MATRIC CONSULAR#6 DIGITS','BM4':'PASAPORTE DE MEX','BM5':'OTRO MEX ID ACEPTADO','BM6':'CRED PARA VOTAR, FOLIO#9 DIGITS','BM7':'ANTIGUA MATRIC CONSULAR#7 DIGITS','BM8':'MATRIC CONSULAR  8 DIGITS','BM9':'MATRIC CONSULAR  9 DIGITS','id-notsupport':'Other'},
		'Other':{'':'SELECT','id-not-support':'Other'}
	};
	let country = $(this).val();
	let valJson = idtypes[country];
	let idtypeComponent = $('#step3-int-idtype');
	for(let key in valJson){
		idtypeComponent += `<option value=${key}>${valJson[key]}</option>`;
	}
	$('#step3-int-idtype').html(idtypeComponent);
}

//hide the passed in elements with id name
function hide_error(input){
	if(input.constructor === Array){
		input.forEach(function(id){
			$(`#${id}-empty-error`).hide();
			$(`#${id}-valid-error`).hide();
			$(`#${id}-error`).hide();
		})
	}else{
		$(`#${input}-empty-error`).hide();
		$(`#${input}-valid-error`).hide();
	}
	
}

//pass an array containing all the ids needed to be checked
function checkNull(input){
	input.forEach(function(id){
		if($(`#${id}`).val() === ''){
			$(`#${id}-empty-error`).show();
		}
	})
}

//check if no error on the front end verification
function noError(){
	if($('.error').is(':visible')){
		return false;
	}
	return true;
}

function homepage(){
	$('#loader').show();
	window.location.href = "/";
}

function show_store(){
	$('#loader').show();
	window.location.href = "/store-locator";
}

function stepR(step){
	popup_hide();
	$('#loader').show();
	window.location.href= `${urlPrepath}step${step}`;
}

// function stepReminder(step){
// 
// 	let html = `Our system indicates your most up to update step is Step${step}, do you want to continue? 
// 	<div id="stepR-wrapper"><button id="stepR-yes" class="button" onclick="stepR(${step})">Continue</button ><button id="stepR-No" class="button" onclick="popup_hide()">Stay</button></div>`;
// 	popup_show('Remind',220,290,html);
// 	$('#curacao_popup_content').css('overflowY','hidden');
// 
// }

//setup 10 minutes for user inactivity
var timeoutInMiliseconds = 600000;
var timeoutId; 
  
function startTimer() { 
    // window.setTimeout returns an Id that can be used to start and stop a timer
    timeoutId = window.setTimeout(doInactive, timeoutInMiliseconds)
}
  
function doInactive() {
	let html = `Your session is about to expire, do you want to continue? 
	<div id="stepR-wrapper"><button id="stepR_yes" class="button" onclick="continue_apply()">Continue</button ><button id="stepR_No" class="button" onclick="leave_apply()">Leave</button></div>`;
	if(screen.width < 781){
		popup_show('Remind',220,290,html);
	}else{
		popup_show('Remind',250,400,html);
	}
	//remove event listener during popup time
	removeSessionListeners();
	time = 30;
	stopTimer = false;
	sessionTimer();
	$('#curacao_popup_content').css('overflowY','hidden');
}

function sessionTimer(){
	if(stopTimer === true) return;
	$("#stepR_yes").html(`Continue(${time}s)`);
	console.log('time',time);
	time--;
	if(time < 0 ){
		leave_apply();
	}else{
		setTimeout(sessionTimer,1000);
	}
}
var stopTimer = false;
function continue_apply(){
	stopTimer = true;
	bindSessionListeners();
	popup_hide();
	resetTimer();
}

function bindSessionListeners(){
	document.addEventListener("mousemove", resetTimer, false);
	document.addEventListener("mousedown", resetTimer, false);
	document.addEventListener("keypress", resetTimer, false);
	document.addEventListener("touchmove", resetTimer, false);
	document.addEventListener("touchstart", resetTimer, false);
	document.addEventListener("touchend", resetTimer, false);
}

function removeSessionListeners(){
	document.removeEventListener("mousemove", resetTimer, false);
	document.removeEventListener("mousedown", resetTimer, false);
	document.removeEventListener("keypress", resetTimer, false);
	document.removeEventListener("touchmove", resetTimer, false);
	document.removeEventListener("touchstart", resetTimer, false);
	document.removeEventListener("touchend", resetTimer, false);
}

function leave_apply(){
	localStorage.setItem('uuid','');
	window.location.href='/credit-app/expire';
}

function resetTimer() { 
    window.clearTimeout(timeoutId)
    startTimer();
}
 
function setupTimers () {
    document.addEventListener("mousemove", resetTimer, false);
    document.addEventListener("mousedown", resetTimer, false);
    document.addEventListener("keypress", resetTimer, false);
    document.addEventListener("touchmove", resetTimer, false);
		document.addEventListener("touchstart", resetTimer, false);
		document.addEventListener("touchend", resetTimer, false);
    startTimer();
}
	

$(document).ready(function(){
	
	//session for inactivity of 10 minutes
	setupTimers();
	
	if(esp()){
		urlPrepath = `/esp${urlPrepath}`;
	}
	
	let current_step = window.location.href.slice(-1);
	
	let uuid = localStorage.getItem('uuid');
	
	let ref_url = window.location.href;
	
	if(ref_url.indexOf('step1') != -1 && ref_url.indexOf('rf=')!= -1){
			let ref_code = ref_url.substring(ref_url.indexOf('rf=')+3);
			$('#step1-refcode').val(ref_code);
	}
	console.log('uuid',uuid);
	
	$('#step2-dob-year').on('focus',function(){
		if(dob_month_check != 1){
			$('#step2-dob-month').focus();
		}else if(dob_date_check != 1){
			$('#step2-dob-date').focus();
		}
	});
	
	$('.step-label').on('click',function(){
		let next_id = $(this).next().attr('id');
		console.log($(this).next().attr('id'));
		$(`#${next_id}`).focus();
	})
	
	// if(!isnull(uuid)){
	// 	//ajax to backend to check if most up to date step
	// 	$.ajax({
	// 		type:'POST',
	// 		data:{
	// 			'uuid':uuid
	// 		},
	// 		url:`${urlPrepath}checkStep`,
	// 		success:function(res){
	// 			res = JSON.parse(res);
	// 			if(!res['error']){
	// 				if((isnull(res['decision']) && !isnull(res['step'])) || (res['decision'].toUpperCase() == 'PENDING' && res['step'] != '5') ){
	// 					let goStep = res['step'];		
	// 					//only redirect when the system step is greater than current step
	// 					if(goStep > current_step){
	// 						stepReminder(goStep);
	// 					}					
	// 				}
	// 			}else{
	// 				return;
	// 			}
	// 		},
	// 		error:function(err){
	// 			console.log('err ',err);
	// 			return;
	// 		}
	// 	})
	// }
	
	$('#step1-fname,#step1-mname,#step1-lname,#step3-us-mname,#step3-int-mname,#step5-fname1,#step5-lname1,#step5-fname2,#step5-lname2').on("propertychange change click keypress keydown keyup input paste",charactersOnly);
	
	$('#step1-refcode').on("propertychange change click keypress keydown keyup input paste",charactersNumbersOnly);
	
	$('#step1-phone,#step2-dob-date,#step2-dob-month,#step2-dob-year,#step3-ssn,#pverify-code,#step5-phone1,#step5-phone2').on("propertychange change click keypress keydown keyup input paste",numbersOnly);
	
	$('#step2-dob-month,#step2-dob-date').on('propertychange change keypress keydown keyup input paste',focusNext);
	
	$('#text-pverify,#call-pverify').on('click',function(){
		$('#step1-pchoice-empty-error').hide();
	});
	
	$('#step3-us-dl,#step3-us-si').on('click',function(){
		$('#step3-us-idtype-empty-error').hide();
	});
	
	$('#step1-email').on('focus',function(){
		$('#step1-email-duplicate-error').hide();
	});
	
	$('#text-pverify').on('click',function(){
		localStorage.removeItem('pverify_choice');
		localStorage.setItem('pverify_choice','text');
	});
	
	$('#call-pverify').on('click',function(){
		localStorage.removeItem('pverify_choice');
		localStorage.setItem('pverify_choice','call');
	});
	
	$('#step1-phone').mask('(000)000-0000');
	$('#step4-phone').mask('(000)000-0000');
	$('#step5-phone1').mask('(000)000-0000');
	$('#step5-phone2').mask('(000)000-0000');
	
	let current_url = window.location.href;
	//show the last four digits for phone verification
	if(current_url.indexOf('phoneverify') != -1){
		let phone = localStorage.getItem('phone');
		if(!isnull(phone)){
			$('#pverify-phone').text(phone.substring(phone.length-4,phone.length));
		}	
	}
	
	if(current_url.indexOf('step3') != -1){
		resetDoc();
	}
	
	if(current_url.indexOf('approve') != -1){
		$('#approve_fname').text(localStorage.getItem('f_name'));
		$('#approve_account_number').text(localStorage.getItem('account_number'));
		$('#approve_credit_limit').text(localStorage.getItem('credit_limit'));
	}
	
	
	$('#step2-income').on("propertychange change click keypress keydown keyup input paste",formatIncome);
	
	//Dropdown changes only apply to mobile devices
	if(screen.width < 781){
		$('#step3-int-country').change(function(){
			$('#step3-int-idtype').css('width','25%');
			$("#country-tmp-option").html($('#step3-int-country option:selected').text());
	    $(this).width($("#country-tmp-select").width());  
		});
		
		$('#step3-int-idtype').change(function(){
	    $("#width_tmp_option").html($('#step3-int-idtype option:selected').text());
	    $(this).width($("#width_tmp_select").width());  
	 	});
	 
	 	$('#step4-llength').change(function(){
		 	$("#width_tmp_option").html($('#step4-llength option:selected').text());
		 	$(this).width($("#width_tmp_select").width());  
		});
		
		$('#step4-emptype').change(function(){
			$("#width_tmp_option_1").html($('#step4-emptype option:selected').text());
			$(this).width($("#width_tmp_select_1").width());  
		});
	 
		$('#step4-wlength').change(function(){
			$("#width_tmp_option_2").html($('#step4-wlength option:selected').text());
			$(this).width($("#width_tmp_select_2").width());  
		});
		
		$('#step5-rel1').change(function(){
			$("#width_tmp_option_1").html($('#step5-rel1  option:selected').text());
			$(this).width($("#width_tmp_select_1").width());  
		});
		
		$('#step5-rel2').change(function(){
			$("#width_tmp_option_2").html($('#step5-rel2 option:selected').text());
			$(this).width($("#width_tmp_select_2").width());  
		});
		
	}
	
	//allow the auto pop for city and state by zip code
	$('#step2-zip').on("propertychange change click keypress keydown keyup input paste",dynamicZip);
	
	let prepath = '/skin/frontend/enterprise/default/images/organicCreditApp2018/';
	$('#us-id').on('click',function(){
		$("#us-id").css('border-color', '#008ecf');
	  $("#us-id").children().eq(1).css('color', '#008ecf');
		$("#us-id-image").attr('src', `${prepath}/docA_active.png`);
		setTimeout(function(){
			$('#loader').show();
			window.location.href = `${urlPrepath}step3us`;
		},100);
		
	});
	
	$('#int-id').on('click',function(){
		$("#int-id").css('border-color', '#008ecf');
	  $("#int-id").children().eq(1).css('color', '#008ecf');
		$("#int-id-image").attr('src', `${prepath}/docB_active.png`);
		setTimeout(function(){
			$('#loader').show();
			window.location.href = `${urlPrepath}step3int`;
		},100);
	});
	
	//dynamically change the dropdown between int countries and sub id types
	$('#step3-int-country').change(intCountry);
	
	$('#step3-ssn').mask('000-00-0000');
	
	$('#step4-emptype').on('change',function(){
		let value = $(this).val();
		if(value === 'U' || value === 'T'){
			$('#step4-rest-wrapper').hide();
		}else{
			$('#step4-rest-wrapper').show();
		}
	});
	
});

var uuid = '';
var f_name = '', m_name = '', l_name = '', phone,email = '', ref_code='',tcpa = 'N', terms = 'N';

function showInfo(id){
	$(`#${id}`).show();
}

//step1 global checker
var fname_check = 0, lname_check = 0, phone_check = 0, email_check = 0, refcode_check = 0, terms_check = 0;
function check_step1_input(id){
	let value = $(`#${id}`).val();
	switch (id) {
		case 'step1-fname':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				fname_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				fname_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				localStorage.setItem('f_name',value);
				fname_check = 1;
				f_name = value;
			}
			break;
		case 'step1-mname':
			if(!isnull(value)){
				if(!validName(value)){
					$(`#${id}-valid-error`).show();
				}else{
					$(`#${id}-valid-error`).hide();
					m_name = value;
				}
			}
			break;
		case 'step1-lname':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				lname_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				lname_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				lname_check = 1;
				l_name = value;
			}
			break;
		case 'step1-phone':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				phone_check = 0;
			}else if(!validphone(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				phone_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				localStorage.setItem('phone',value);
				phone_check = 1;
				phone = value.replace(/[()-]/g,'');
			}
			break;
		case 'step1-email':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				email_check = 0;
			}else{
				if(emailRegex(value)){
					$.ajax({
						url:'/credit-app/step1/checkEmail',
						cache:false,
						data:{
							'email':`${value}`
						},
						dataType:"json",
						type:"POST",
						success:function(res){
							if(res === 1){
								$(`#${id}-empty-error`).hide();
								$(`#${id}-valid-error`).hide();
								 email_check = 1;
								 email = value;
							}else{
								$(`#${id}-empty-error`).hide();
								$(`#${id}-valid-error`).show();
								email_check = 0;
							}
						},
						error:function(res){
							$(`#${id}-valid-error`).html(`email valid error ${res}`);
							console.log('error on valid email',res);
							email_check = 0;
						}
					});
				}else{
					$(`#${id}-empty-error`).hide();
					$(`#${id}-valid-error`).show();
					email_check = 0;
				}
			}
			break;
		case 'step1-refcode':
			if(isnull(value)){
					refcode_check = 1;
			}else if(!validRefcode(value)){
					$(`#${id}-valid-error`).show();
					 refcode_check = 0;
			}else{
					$(`#${id}-valid-error`).hide();
					 refcode_check = 1;
					 ref_code = value;
			}
			break;
		case 'step1-tcpa':
			if($(`#${id}`).is(':checked')){
				tcpa = 'Y';
			}else{
				tcpa = 'N';
			}
			break;
		case 'step1-terms':
			if(!$(`#${id}`).is(':checked')){
				$(`#${id}-empty-error`).show();
				terms = 'N';
			}else{
				 terms_check = 1;
				 terms = 'Y';
			}
			break;
		default:
			console.log('check step1: unknown id',id);
			break;
	}
}
function check_step1(){
	

	let efields = ['step1-fname','step1-lname','step1-phone','step1-email','step1-refcode','step1-terms','step1'];

	hide_error(efields);
	
	let fields = ['step1-fname','step1-mname','step1-lname','step1-phone','step1-email','step1-refcode','step1-tcpa','step1-terms'];
	
	for(let i = 0; i < fields.length; i++){
		check_step1_input(fields[i]);
	}
	
	if(!$('#text-pverify').is(':checked') && !$('#call-pverify').is(':checked')){
		$('#step1-pchoice-empty-error').show();
		return;
	}else{
		$('#step1-pchoice-empty-error').hide();
	}
	
	if(!noError()){return;}
	
	if(fname_check === 1 && lname_check === 1 && phone_check === 1 && email_check === 1 && refcode_check === 1 && terms_check === 1){
		submit_step1();
	}else{
		return;
	}
	
}

function submit_step1(){

	$("#loader").show();
	
	//compose json object
	let step1 = {
		'f_name':f_name,
		'm_name':m_name,
		'l_name':l_name,
		'phone':phone,
		'email':email,
		'ref_code':ref_code,
		'tcpa':tcpa,
		'terms':terms
	};
	
	let url = `${urlPrepath}step1`;
	
	console.log('step1 object',step1);
	//ajax call to backend and wait for response
	$.ajax({
		type:'POST',
		url:url,
		data:{
			'f_name':f_name,
			'm_name':m_name,
			'l_name':l_name,
			'phone':phone,
			'email':email,
			'ref_code':ref_code,
			'tcpa':tcpa,
			'terms':terms
		},
		success:function(res){
			res = JSON.parse(res);
			if(res['error']){
				$("#loader").hide();
				$('#step1-error').text(res['error_message']);
				$('#step1-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#step1-error').show();
			}else{
				uuid = res['uuid'];
				localStorage.setItem('uuid',uuid);
				sendVerifyCode();
			}
		},
		error:function(error){
			console.log('error from step1: ', error);
			$("#loader").hide();
		}
	});
}

//send the verify code to customer's phone number
function sendVerifyCode(){
	let uuid = localStorage.getItem('uuid');
	let pverify_choice = localStorage.getItem('pverify_choice');
	let url = `${urlPrepath}sendcode`;
	let sendCode = {
		'uuid':uuid,
		'type':pverify_choice
	};
	$.ajax({
		type:'POST',
		url:url,
		data:sendCode,
		success:function(res){
			
			res = JSON.parse(res);
			console.log('phone send code',res);
			if(res['error']){
				$("#loader").hide();
				$('#verify-error').text(res['error_message']);
				$('#verify-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#verify-error').show();
			}else{
				window.location.href = `${urlPrepath}phoneverify`;
			}
		},
		error:function(err){
			console.log('error on sending verification code');
		}
	});
}

//resend code if the customer mis type or didn't receive verify code
function resendCode(){
	console.log('resend code');
	sendVerifyCode();
	$('#onem-countdown').show();
	// sendVerifyCode();
	$('#pverify-resendCode').prop('disabled',true)
	$('#pverify-resendCode').css('color','grey');
	time = 60;
	timer();
}

var time;
//show count down for resending code
function timer(){
	$('#onem-countdown').html(`(${time}s)`);
	time--;
	if(time < 0 ){
		$('#pverify-resendCode').prop('disabled',false);
		$('#pverify-resendCode').css('color','rgb(242, 103, 34)');
		$('#onem-countdown').hide();
	}else{
		setTimeout(timer,1000);
	}
}

var sendcode = '';
function check_pverify_input(id){
	let vcode = $(`#${id}`).val();
	if(isnull(vcode)){
		$(`#${id}-empty-error`).show();
		return false;
	}else if(vcode.length < 6){
		$(`#${id}-empty-error`).hide();
		$(`#${id}-valid-error`).show();
		return false;
	}else{
		$(`#${id}-empty-error`).hide();
		$(`#${id}-valid-error`).hide();
	}
	sendcode = vcode;
	return true;
}

function check_pverify(){
	
	$('#verify-error').hide();
	
	if(!check_pverify_input('pverify-code')){
		return;
	}
	
	let phone = localStorage.getItem('phone');
	phone = phone.replace(/[-()]/g,'');
	
	//if backend returns good back go to the next step
	$('#loader').show();
	
	let url = `${urlPrepath}verifycode`;
	uuid = localStorage.getItem('uuid');
	let pverify = {
		'uuid':uuid,
		'sendcode':sendcode
	}
	$.ajax({
		type:'POST',
		url:url,
		data:pverify,
		success:function(res){
			
			res = JSON.parse(res);
			console.log('res from phone code verify :',res);
			if(res['error']){
				$("#loader").hide();
				$('#verify-error').text(res['error_message']);
				$('#verify-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#verify-error').show();
			}else{
				window.location.href = `${urlPrepath}step2`;
			}
		},
		error:function(error){
			console.log('error from phone code verify: ', error);
			$("#loader").hide();
		}
	})
}

var street = '', apt = '',zip = '', city = '', state = '', income = '', dob_month = '', dob_date = '',dob_year = '',dob = '';

//step2 checker
var address_check = 0, zip_check = 0, unknown_zip = 1, city_check = 0, state_check = 0, income_check = 0, dob_month_check = 0, dob_date_check = 0, dob_year_check = 0, dob_check = 0; 

//check the month, date and year relationship
function check_dob(){
	$('#step2-dob-valid-error').hide();
	let date = parseInt($('#step2-dob-date').val());
	let month = parseInt($('#step2-dob-month').val());
	let year = parseInt($('#step2-dob-year').val());
	
	if((month === 4 || month === 6 || month === 9 || month === 11) && date === 31){
		return false;
	}else{
		if(month ===2){
			let isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
			if(date > 29 || (date === 29 && !isleap)){
				return false;
			}
		}
	}
	dob_check = 1;
	return true; 
}

function check_step2_input(id){
	let value = $(`#${id}`).val();
	switch (id) {
		case 'step2-address':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				address_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				address_check = 1;
				street = value;
			}			
			break;
		case 'step2-address-aptnumber':
			apt = value;
			break;
		case 'step2-zip':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				zip_check = 0;
			}else if(unknown_zip === 1 && success_dynamic_zip == 1){
				$(`#${id}-valid-error`).show();
				zip_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				zip_check = 1;
				zip = value;
			}			
			break;
		case 'step2-city':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				city_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				city_check = 1;
				city = value;
			}			
			break;
		case 'step2-state':
			value = $(`#${id} option:selected`).val();
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				state_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				state_check = 1;
				state = value;
			}			
			break;
		case 'step2-income':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				income_check = 0;
			}else if(!validIncome(value)){
				$(`#${id}-valid-error`).show();
				income_check = 0;
			}else if(value < 400){
				$('#income-low').show();
				income_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				$('#income-low').hide();
				income_check = 1;
				income = value.replace(/[,]/g,'');
			}			
			break;
		case 'step2-dob-month':
			if(isnull(value)){
				$('#step2-dob-empty-error').show(); 
				dob_month_check = 0;
			}else{
				$('#step2-dob-empty-error').hide();
				value = parseInt(value);
				if(value < 1 || value > 12){
					$(`#${id}-valid-error`).show();
					dob_month_check = 0;
				}else{
					$(`#${id}-valid-error`).hide();
					dob_month_check = 1;
					dob_month = value;
				}	
			}
					
			break;
		case 'step2-dob-date':
			if(isnull(value)){
				$('#step2-dob-empty-error').show();
				dob_date_check = 0;
			}else{
				$('#step2-dob-empty-error').hide();
				value = parseInt(value);
				if(value < 1 || value > 31){
					$(`#${id}-valid-error`).show();
					dob_date_check = 0;
				}else{
					$(`#${id}-valid-error`).hide();
					dob_date_check = 1;
					dob_date = value;
				}
			}		
			break;
		case 'step2-dob-year':
			if(isnull(value)){
				$('#step2-dob-empty-error').show();
			}else{
				$('#step2-dob-empty-error').hide();
				value = parseInt(value);
				let curYear = (new Date()).getFullYear();
				if(curYear-value>100 || curYear-value<18){
					$(`#${id}-valid-error`).show();
				}else{
					if(curYear-value<21){
						if(!esp()){
							var minimum_age_alert = 'For online credit application, at this moment we only accept age over 21. Please go to our store to complete your application or call 1-800-990-3422. Thanks.';
						}else{
							var minimum_age_alert = 'Para Aplicaciones de Crédito en línea, por el momento solo se pueden procesar con edad de 21 años o más. Por favor visite nuestras tiendas para completar su aplicación o puede llamar al 1-800-990-3422. Gracias';
						}
						if(screen.width < 370){
							popup_show('',220,250,minimum_age_alert);
						}else if(screen.width < 781){
							popup_show('',250,290,minimum_age_alert);
						}else{
							popup_show('',250,400,minimum_age_alert);
						}
						
						return;
					}
					$(`#${id}-valid-error`).hide();
					dob_year_check = 1;
					dob_year = value;
				}	
			}	
			break;
		default:
			console.log('check step2: unknown id', id);
			break;	
	}
}

function check_step2(){
	
	let efields = ['step2-address','step2-zip','step2-city','step2-state','step2-income','step2-dob-month','step2-dob-date','step2-dob-year','step2-dob','step2'];
	hide_error(efields);
	
	
	let fields = ['step2-address','step2-address-aptnumber','step2-zip','step2-city','step2-state','step2-income','step2-dob-month','step2-dob-date','step2-dob-year'];
	
	for(let i = 0; i < fields.length; i++){
		check_step2_input(fields[i]);
	}
	
	if(!check_dob()){
		$('#step2-dob-valid-error').show();
		return;
	}
	
	if(!noError()){return;}
	
	if(address_check === 1 && zip_check === 1 && city_check === 1 && state_check === 1 && income_check === 1 && dob_month_check === 1 && dob_date_check === 1 && dob_year_check === 1 && dob_check === 1){
		submit_step2();
	}else{
		return;
	}
	
}

function submit_step2(){
	$('#loader').show();
	uuid = localStorage.getItem('uuid');

	let verify_address_url = `${urlPrepath}/checkAddress`;

	let verify_address = {
		'uuid':uuid,
		'street':`${street} ${apt}`,
		'city':city,
		'zip':zip,
		'state':state,
	};
	
	$.ajax({
		type:'POST',
		data:verify_address,
		url:verify_address_url,
		success:function(res){
			res = JSON.parse(res);
			console.log('res from address verify',res);
			
			if(res['error']){
				$("#loader").hide();
				$('#step2-error').text(res['error_message']);
				$('#step2-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#step2-error').show();
			}else{
				//submit step2

				if(dob_date < 10){
					dob_date = `0${dob_date}`
				};
				if(dob_month < 10){
					dob_month = `0${dob_month}`
				};
				let url = `${urlPrepath}step2`;

				var step2 = {
					'uuid':uuid,
					'street1':`${street}`,
					'street2': `${apt}`,
					'zip':zip,
					'city':city,
					'state':state,
					'income':income,
					'dob':`${dob_year}-${dob_month}-${dob_date}`
				}
				console.log('step2',step2);
				
				$.ajax({
					type:'POST',
					data:step2,
					url:url,
					success:function(res){
						res = JSON.parse(res);
						console.log('res in step2',res);
						if(res['error']){
							$("#loader").hide();
							$('#step2-error').text(res['error_message']);
							$('#step2-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
							$('#step2-error').show();
						}else{

							window.location.href = `${urlPrepath}step3`;

						}
					},
					error:function(err){
						console.log('step2 error',err);
					}
				});
			}
		},
		error:function(err){
			console.log('err from address err',err);
		}
	});
}

var ssn = '', us_id_type = '', us_state = '', us_id_number = '', us_maiden = '';
//Step3 US checker
var ssn_check = 0,us_state_check = 0, us_idnumber_check = 0, us_mname_check = 0;
function check_step3_us_input(id){
	let value = $(`#${id}`).val();
	let isdl = $('#step3-us-dl').prop('checked');
	let state = $('#step3-us-state').val();
	switch (id) {
		case 'step3-ssn':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				ssn_check = 0;
			}else if(!validssn(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				ssn_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				ssn_check = 1;
				ssn = value.replace(/[-]/g,'');
			}			
			break;
		case 'step3-us-state':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				us_state_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				us_state_check = 1;
				us_state = value;
			}
		case 'step3-us-idnumber':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				us_idnumber_check = 0;
			}else if(isdl && !validdl(state,value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				us_idnumber_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				us_idnumber_check = 1;
				us_id_number = value;
			}
			break;
		case 'step3-us-mname':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				us_mname_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				us_mname_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				us_mname_check = 1;
				us_maiden = value;
			}
			break;
		default:
			console.log('unknown id on step3us: ',id);
			break;
	}
}
function check_step3_us(){
	
	let efields = ['step3-ssn','step3-int-idtype','step3-us-idtype','step3-us-state','step3-us-idnumber','step3-us-mname','step3-us'];
	
	hide_error(efields);
	
	let fields = ['step3-ssn','step3-us-state','step3-us-idnumber','step3-us-mname'];
	
	for(let i = 0; i < fields.length; i++){
		check_step3_us_input(fields[i]);
	}
	
	if(ssn_check === 0){
		check_step3_us_input('step3-ssn');
		return;
	}
	
	if(!$('#step3-us-dl').prop('checked') && !$('#step3-us-si').prop('checked')){
		$('#step3-us-idtype-empty-error').show();
		return;
	} 
	
	if($('#step3-us-dl').prop('checked')){
		us_id_type = 'DL';
	}else{
		us_id_type = 'SI';
	}
	
	if(us_state_check === 0){
		check_step3_us_input('step3-us-state');
		return;
	}
	
	if(us_idnumber_check === 0){
		check_step3_us_input('step3-us-idnumber');
		return;
	}
	
	if(us_mname_check === 0){
		check_step3_us_input('step3-us-mname');
		return;
	}
	
	if(!noError()){return;}
	
	if(ssn_check === 1 && us_state_check === 1 && us_idnumber_check ===1 && us_mname_check){
		if(us_state == 'CA'){
			us_id_type = 'AU1';
		}else{
			us_id_type = 'AU4';
		}
		submit_step3('us');
	}
	
}

var int_country = '',int_id_type = '',int_id_number = '',int_maiden = '';
//step3 International ID checker
var int_idnumber_check = 0, int_mname_check = 0;
function check_step3_int_input(id){
	
	let value = $(`#${id}`).val();
	
	int_id_type = $('#step3-int-idtype').val();
	
	switch (id) {
		case 'step3-int-country':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
			}else{
				$(`#${id}-empty-error`).hide();
				int_country = value;
			}
			break;
		case 'step3-int-idtype':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
			}else{
				$(`#${id}-empty-error`).hide();
				int_id_type = value;
			}
			break;
		case 'step3-int-idnumber':
				if(isnull(value)){
					$(`#${id}-empty-error`).show();
					int_idnumber_check = 0;
				}else if(!validIntNumber(int_id_type,value)){
					$(`#${id}-empty-error`).hide();
					$(`#${id}-valid-error`).show();
					int_idnumber_check = 0;
				}else{
					$(`#${id}-empty-error`).hide();
					$(`#${id}-valid-error`).hide();
					 int_idnumber_check = 1;
					 int_id_number = value;
				}
			break;
		case 'step3-int-mname':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				int_mname_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				int_mname_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				int_mname_check = 1;
				int_maiden = value;
			}
			break;
		default:
			console.log('step3 int unknown id',id);
			break;
	}
}
function check_step3_int(){
	
	let efields = ['step3-int-country','step3-int-idtype','step3-int-idnumber','step3-int-mname','step3-int-cometostore','step3-int'];
	
	hide_error(efields);
	
	let fields = ['step3-int-country','step3-int-idtype','step3-int-idnumber','step3-int-mname'];
	
	for(let i = 0; i < fields.length; i++){
		check_step3_int_input(fields[i]);
	}
	
	if(int_country === ''){
		check_step3_int_input('step3-int-country');
		return;
	}
	
	if(int_id_type === ''){
		check_step3_int_input('step3-int-idtype');
		return;
	}
	
	if(int_country === 'Other' && int_id_type === 'id-not-support'){
		$('#step3-int-cometostore-empty-error').show();
		return;
	}
	
	if(int_idnumber_check === 0){
		check_step3_int_input('step3-int-idnumber');
		return;
	}
	
	if(int_maiden === ''){
		check_step3_int_input('step3-int-mname');
		return;
	}
	
	if(!noError()) return;
	
	if(int_idnumber_check === 1 && int_mname_check === 1){
		submit_step3('int');
	}
	
}

//after validating all the input of step3, submit the form
function submit_step3(choice){
	let step3,url;
	uuid = localStorage.getItem('uuid');
	url = `${urlPrepath}step3`;
	if(choice === 'us'){
		//compose us json	
		step3 = {
			"uuid":uuid,
  		"ssn":ssn,
  		"country":'US',
  		"id_state":us_state,
  		"id_type":us_id_type,
  		"id_number":us_id_number, 
  		"maiden_name":us_maiden
		};
		console.log('step3_us json',step3);
	}else if(choice === 'int'){
		step3 = {
			"uuid":uuid,
  		"ssn":'',
  		"country":int_country,
  		"id_state":'',
  		"id_type":int_id_type,
  		"id_number":int_id_number, 
  		"maiden_name":int_maiden
		};
		console.log('step3_int json',step3);
		
	}else{
		console.log('unknown choice:',choice);
		return;
	}
	
	//send data to backend and call webservice, render different templates based on decision
	
	$('#loader').show();
	let decision = '';
	//send the ajax call
	$.ajax({
		type:'POST',
		url:url,
		data:step3,
		success:function(res){
			res = JSON.parse(res);
			console.log('res from step3',res);
			if(res['error']){
				$("#loader").hide();
				if(step3['country'] === 'US'){
					$('#step3-us-error').text(res['error_message']);
					$('#step3-us-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
					$('#step3-us-error').show();
				}else{
					$('#step3-int-error').text(res['error_message']);
					$('#step3-int-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
					$('#step3-int-error').show();
				}
			}else{
				decision = res['status'].toUpperCase();
				switch (decision) {
					case 'APPROVE':
						localStorage.setItem('credit_limit',res['total_limit']);
						localStorage.setItem('account_number',res['account']);
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}approve`;
						break;
					case 'PENDING':
						window.location.href = `${urlPrepath}step4`;
						break;
					case 'DUPLICATE':
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}duplicate`;
						break;
					case 'DECLINE':
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}decline`;
						break;
					default:
						console.log("Unknown decision");
						break;
				}
			}
		},
		error:function(err){
			console.log('error from step3',err);
		}
	});
	
	
	
}

var bank_type = '', live_length = '',emptype = '',company_name = '',work_length = '',company_phone = '';
var bank_check = 0, llength_check = 0, emptype_check = 0, cname_check = 0, wlength = 0, cphone_check = 0; 
function check_step4_input(id){
	let value = $(`#${id}`).val();
	switch (id) {
		case 'step4-bank-type':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				bank_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				bank_type = value;
				bank_check = 1;
			}
			break;
		case 'step4-llength':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				llength_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				live_length = value;
				llength_check = 1;
			}
			break;
		case 'step4-emptype':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				emptype_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				emptype = value;
				emptype_check = 1;
			}
			break;
		case 'step4-cname' : 
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				cname_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				company_name = value;
				cname_check = 1;
			}
			break;
		case 'step4-wlength':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				wlength = 0; 
			}else{
				$(`#${id}-empty-error`).hide();
				work_length = value;
				wlength = 1; 
			}
			break;
		case 'step4-phone':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				cphone_check = 0; 
			}else if(!validphone(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				cphone_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				company_phone = value.replace(/[()-]/g,'');
				cphone_check = 1; 
			}
			break;
		default:
			console.log('step4 unknown id',id);
			break;
	}
}

//check for the choice of bank type and residency length
function check_step4(){
	
	let efields = ['step4-bank-type','step4-llength','step4-emptype','step4-wlength','step4'];
	
	hide_error(efields);
	
	let fields = ['step4-bank-type','step4-llength','step4-emptype','step4-cname','step4-wlength','step4-phone','step4'];
	
	for(let i = 0; i < fields.length;i++){
		check_step4_input(fields[i]);
	}
	
	//no verification for retired and unemplyed applicants
	if((emptype === 'T' || emptype === 'U') && noError()){
		submit_step4();
	}
	
	if(!noError()) return;
	
	if(bank_check === 1 && llength_check === 1 && emptype_check === 1 && cname_check === 1 && wlength === 1 && cphone_check === 1){
		submit_step4();
	}

}

function submit_step4(){
	uuid = localStorage.getItem('uuid');
	
	let live_year = live_length.substring(0,1);
	let live_month = live_length.substring(2,3);
	live_month = live_month === '5' ? '6' : live_month;
	
	let work_year = work_length.substring(0,1);
	let work_month = work_length.substring(2,3);
	
	let step4 = {
		'uuid':uuid,
		'bank_type':bank_type,
		'live_year':live_year,
		'live_month':live_month,
		'emp_type':emptype,
		'company_name':company_name,
		'work_year':work_year,
		'work_month':work_month,
		'company_phone':company_phone
	};
	let url = `${urlPrepath}step4`;
	
	console.log('step4 json',step4);
		
	$('#loader').show();
	
	$.ajax({
		type:"POST",
		data:step4,
		url:url,
		success:function(res){
			res = JSON.parse(res);
			console.log('step4 res',res);
			if(res['error']){
				$("#loader").hide();
				$('#step4-error').text(res['error_message']);
				$('#step4-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#step4-error').show();
			}else{
				window.location.href = `${urlPrepath}step5`;
			}
		},
		error:function(err){
			console.log('step4 error',err);
		}
	});
	
}


var fname1='',lname1 = '', phone1 = '', rel1 = '',fname2 = '',lname2='', phone2='',rel2='';
var fname1_check = 0,lname1_check = 0, phone1_check = 0, rel1_check = 0, fname2_check = 0,lname2_check = 0, phone2_check = 0, rel2_check = 0; 
function check_step5_input(id){
	let value = $(`#${id}`).val();
	switch (id) {
		case 'step5-fname1':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				fname1_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				fname1_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				fname1 = value;
				fname1_check = 1;
			}
			break;
		case 'step5-lname1':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				lname1_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				lname1_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				lname1 = value;
				lname1_check = 1;
			}
			break;
		case 'step5-fname2':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				fname2_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				fname2_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				fname2 = value;
				fname2_check = 1;
			}
			break;
		case 'step5-lname2':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				lname2_check = 0;
			}else if(!validName(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				lname2_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				lname2 = value;
				lname2_check = 1;
			}
			break;
		case 'step5-phone1':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				phone1_check = 0;
			}else if(!validphone(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				phone1_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				phone1 = value.replace(/[()-]/g,'');
				phone1_check = 1;
			}
		case 'step5-phone2':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				phone2_check = 0;
			}else if(!validphone(value)){
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).show();
				phone2_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				$(`#${id}-valid-error`).hide();
				phone2 = value.replace(/[()-]/g,'');
				phone2_check = 1;
			}
			break;
		case 'step5-rel1':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				rel1_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				rel1 = value;
				rel1_check = 1;
			}
			break;
		case 'step5-rel2':
			if(isnull(value)){
				$(`#${id}-empty-error`).show();
				rel2_check = 0;
			}else{
				$(`#${id}-empty-error`).hide();
				rel2 = value;
				rel2_check = 1;
			}
			break;
		default:
			console.log('step5 unknown id',id);
			break;
	}
}

function check_step5(){
	
	let efields = ['step5-fname1','step5-lname1','step5-phone1','step5-fname2','step5-lname2','step5-phone2','step5-rel1','step5-rel2','step5'];
	
	hide_error(efields);
	
	let fields = ['step5-fname1','step5-lname1','step5-phone1','step5-fname2','step5-lname2','step5-phone2','step5-rel1','step5-rel2'];
	
	fields.forEach(function(id){
		check_step5_input(id);
	});
	
	if(!noError()) return;
	
	if(fname1_check === 1 && lname1_check === 1 && phone1_check === 1 && rel1_check === 1 && fname2_check === 1 && lname2_check === 1 && phone2_check === 1 && rel2_check === 1){
		submit_step5();
	}
}

function submit_step5(){
	
	uuid = localStorage.getItem('uuid');
	//send data to backend and call webservice, render different templates based on decision
	let step5 = {
	  "uuid": uuid,
	  "ref1_fname": fname1,
		"ref1_lname": lname1,
	  "ref1_phone": phone1,
		"ref1_relation":rel1,
	  "ref2_fname": fname2,
		"ref2_lname":lname2,
	  "ref2_phone": phone2,
	  "ref2_relation": rel2
	}
	console.log('step5 json',step5);
	
	let url = `${urlPrepath}step5`;
	
	$('#loader').show();
	
	let decision = '';
	
	$.ajax({
		type:'POST',
		data:step5,
		url:url,
		success:function(res){
			console.log('step5 res',res);
			res = JSON.parse(res);
			console.log('res at step5',res);
			if(res['error']){
				$("#loader").hide();
				$('#step5-error').text(res['error_message']);
				$('#step5-error').append('<span onclick="closeCurrent(this)" class="close">X</span>');
				$('#step5-error').show();
			}else{
				decision = res['status'].toUpperCase();
				switch (decision) {
					case 'APPROVE':
						localStorage.setItem('credit_limit',res['total_limit']);
						localStorage.setItem('account_number',res['account']);
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}approve`;
						break;
					case 'PENDING':
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}pending`;
						break;
					case 'DECLINE':
						localStorage.removeItem('uuid');
						window.location.href = `${urlPrepath}decline`;
						break;
					default:
						console.log("Unknown decision");
						break;
				}
			}
		},
		error:function(err){
			console.log('step5 error',err);
		}
	});
	
}


//disable a button and change its color
function disabledButton(button){
	button.prop('disabled','true');
	button.css('background-color','#E6E6E6');
	button.css('color','#FFFFFF');
}

function backToApplication(){
	$('#loader').show();
	window.location.href = '/credit-app';
}
