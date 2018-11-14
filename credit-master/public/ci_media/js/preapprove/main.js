console.log('preapprove main js');
//Show loader
function show_loader()
{
	$('#loader').show();
}

// Hides the loader
function hide_loader()
{
	$('#loader').hide();
}

function isnull(_o) {
  return (typeof _o === 'undefined' || _o === null || (typeof _o === 'string' && _o === '')) ? true : false;
}

function nextbox(fldobj, nbox) {
		if (fldobj.value.length == fldobj.maxLength) {
				fldobj.form.elements[nbox].focus();
		}
}

// Selects the index in the dropdown, used at step1 and step3
function setSelectedIndex(_s, _valsearch)
{
	//for application which doesn't have reference
	if(_s != null){
		for (i = 0; i< _s.options.length; i++)
		{
			if (_s.options[i].value == _valsearch)
			{
				_s.options[i].selected = true;
				break;
			}
		}
	}
	return;
}

// When edit is clicked
function edit()
{
	var address = document.getElementById('address');
	var city = document.getElementById('city');
	var state = document.getElementById('state');
	var zip = document.getElementById('zip');

	// Make not readonly
	address.readOnly = false;
	address.style.color='black';
	city.readOnly = false;
	city.style.color='black';
	state.disabled = false;
	state.style.color='black';
	zip.readOnly = false;
	zip.style.color='black';

}

// build popup
function popup_show(_title, _height, _width, _html, _callback, _staticblock) {
  var h = parseInt(_height);
  var w = parseInt(_width);

  // Create popup
  var p = document.createElement('div');
  p.id = 'curacao_popup';
  p.style.height = h.toString() + 'px';
  p.style.width = w.toString() + 'px';
  p.style.left = '50%';
  p.style.top = '50%';
  p.style.marginLeft = '-' + (w / 2).toString() + 'px';
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
  l.src = location.protocol +
    '/ci_media/images/popup_circleFlower.png';
  l.style.position = 'absolute';
  l.style.left = '25px';
  l.style.top = '-20px';
  l.style.zIndex = 9999999;

  // Create title
  var t = document.createElement('div');
  t.id = 'curacao_popup_title';
  t.style.height = '50px';
  t.style.width = 'auto'; // (w - 165).toString() + 'px';
  t.style.lineHeight = '50px';
  t.style.color = '#FFFFFF';
  t.style.fontFamily = 'Arial';
  t.style.fontSize = '11px';
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
  c.src = location.protocol +
    '/ci_media/images/popup_closeBtn.png';
  c.onclick = function() {
    popup_hide();
  };

	// Create content
  var cc = document.createElement('div');
  cc.id = 'curacao_popup_content';
  cc.style.height = (h - 100).toString() + 'px';
  cc.style.width = 'auto';
  cc.style.overflow = 'auto';
  cc.style.padding = '20px';
  cc.style.color = '#363636';
  cc.style.textAlign = 'left';
  cc.style.backgroundColor = '#FFFFFF';

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
  b.onclick = function() {
    popup_hide();
  };

  // Append to body
  p.appendChild(l);
  p.appendChild(t);
  p.appendChild(t);
  p.appendChild(c);
  p.appendChild(cc);
  document.body.appendChild(b);
  document.body.appendChild(p);

  if (_staticblock) {
    $j
      .post(
        "", {
          link: '://icuracao.com/',
          staticblock: _staticblock
        },
        function(_data, _status) {
          document.getElementById("curacao_popup_content").innerHTML = _data;
          document.getElementById("curacao_popup").style.display = 'block';
          document.getElementById("curacao_popup_background").style.display = 'block';

          if (typeof _callback === "function") {
            _callback();
          }
        });
  } else {
    document.getElementById("curacao_popup_content").innerHTML = _html;
    document.getElementById("curacao_popup").style.display = 'block';
    document.getElementById("curacao_popup_background").style.display = 'block';

    if (typeof _callback === "function") {
      _callback();
    }
  }
}

	function popup_hide() {
  // Clear Timeout
  if ((typeof timeleftcheck != 'undefined')) {
    clearInterval(timeleftcheck);
    timeout_script();
  }

  var p = document.getElementById("curacao_popup");
  var b = document.getElementById("curacao_popup_background");
  p.parentNode.removeChild(p);
  b.parentNode.removeChild(b);
}

// var check_full_ssn = 0, pass_full_ssn = 0;
var check_full_ssn = 0;
//Vaildate form at step1
function validateStep1()
{
	

	var e = document.getElementById('preapp-error');
	var s = document.getElementById('btnsubmit');

	// Disable submit
	e.innerHTML = '';
	s.enabled = false;
	
	var address = document.getElementById('address').value;
	var city = document.getElementById('city').value;
	var stateElement = document.getElementById('state');
	var state = stateElement.options[stateElement.selectedIndex].value;
	var zip = document.getElementById('zip').value;
	var ssn = document.getElementById('ssn').value;
	var arssn = localStorage.getItem('arssn');
	let ssn1 = document.getElementById('ssn1').value;
	let ssn2 = document.getElementById('ssn2').value;
	let ssn3 = document.getElementById('ssn3').value;
	var mmElement = document.getElementById('mm');
	var mm = mmElement.options[mmElement.selectedIndex].value;
	var ddElement = document.getElementById('dd');
	var dd = ddElement.options[mmElement.selectedIndex].value;
	var yyyyElement = document.getElementById('yyyy');
	var yyyy = yyyyElement.options[yyyyElement.selectedIndex].value;
	var prim1 = document.getElementById('prim1').value;
	var prim2 = document.getElementById('prim2').value;
	var prim3 = document.getElementById('prim3').value;
	// var tcpa = document.getElementById('tcpa').checked;
	
	//check customer typed ssn and arssn for the second and third digital
	if(localStorage.getItem('bypassSSNcheck') === 'no'){
		//ssn doesn't match, ask for full ssn
		if(ssn.substr(1,2) != arssn.substr(0,2)){
			// if(pass_full_ssn === 0){
				if(check_full_ssn === 1){
					e.innerHTML === '';
				}else{
					e.innerHTML = 'Your input of SSN doesn\'t match with our system record, please type the full SSN';
				}
				jQuery('#partial-ssn-wrapper').hide();
				jQuery('#full-ssn-wrapper').show();
				if(check_full_ssn === 0){
					check_full_ssn = 1;
					return;
				}
			// }
		}
		
		if(ssn ==='' || ssn.length < 3){ e.innerHTML = 'SSN is a required field!'; s.enabled = true; hide_loader(); return false; }
	}

	if(address === ''){ e.innerHTML = 'Address is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(city === ''){ e.innerHTML = 'City is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(state === ''){ e.innerHTML = 'State is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(zip ===''){ e.innerHTML = 'Zip code is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(check_full_ssn === 1 && ((ssn1 ==='' || ssn1.length < 3) || (ssn2 ==='' || ssn2.length < 2) || (ssn3 ==='' || ssn3.length < 4))){
		e.innerHTML = 'SSN is a required field!'; 
		s.enabled = true; 
		return false;
	}else{
		// pass_full_ssn = 1;
		localStorage.setItem('ssn1',ssn1);
		localStorage.setItem('ssn2',ssn2);
		localStorage.setItem('ssn3',ssn3);
	}
	if(mm ===''){ e.innerHTML = 'DOB month is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(dd ===''){ e.innerHTML = 'DOB day is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(yyyy ===''){ e.innerHTML = 'DOB Year is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(prim1 === '' || prim1.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(prim2 === '' || prim2.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(prim3 === '' || prim3.length < 4){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
	// if(!tcpa){ e.innerHTML = 'Please agree to TCPA to submit form'; s.enabled = true; hide_loader(); return false;}

	// var url = 'https://icuracao.com/accept/step1/checkaddress';

	
	show_loader();
	
	$("#form1").submit();

	//verify address
	// jQuery.ajax({
  //     url: url,
  //     type: "POST",
  //     data: {
  //       city: city,
  //       zip: zip,
  //       state: state,
  //       street: address
  //     }
  //   })
  //   .done(function(result) {
	// 
	// 		var response = JSON.parse(result)
	// 		console.log('response',response);
	// 
  //     if (response.error === 0) {
  //       console.log('address verification passed');
  //       jQuery("#form1").submit();
	// 			console.log('submit form');
  //     } else {
  //       console.log('incorrect address ', response);
  //       e.innerHTML = response.error_message;
  //       hide_loader();
	// 			console.log('does not submit form');
  //     }
  //   })
  //   .fail(function(error) {
  //     console.log('error: ', error);
  //   })

}

//number validate for phone at step1
function numberformat(_e)
{
	var charCode = (_e.which) ? _e.which : _e.keyCode;
	//stop enter to submit form
	if(charCode == 13){
		event.preventDefault();
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){ return false; }
}

//phone focus at step1
function phonefocus(_id1, _id2)
{
	var id1 = document.getElementById(_id1);
	var id2 = document.getElementById(_id2);

	// Forward
	if(id1.value.length===3){ id2.focus(); }
}

//full ssn focus at step1
function ssnfocus(_id1, _id2)
{
	var id1 = document.getElementById(_id1);
	var id2 = document.getElementById(_id2);

	// Forward
	if(_id1 === 'ssn1'){
		if(id1.value.length===3){ id2.focus(); }
	}
	
	if(_id1 === 'ssn2'){
		if(id1.value.length===2){ id2.focus(); }
	}
	
}

// Vaildate form at step3
function validateStep3()
{
	show_loader();

	var e = document.getElementById('preapp-error');
	var s = document.getElementById('btnsubmit');

	// Disable submit
	e.innerHTML = '';
	s.enabled = false;

	var license = document.getElementById('license');
	var income = document.getElementById('income');
	var email = document.getElementById('email');
	var password = document.getElementById('password');
	
	
	
	//remove confirm password
	// var confirm = document.getElementById('confirm');
	      
	var licensestate = document.getElementById('licensestate');

	var terms = document.getElementById('terms').checked;

	if(license.value===''){ e.innerHTML = 'License is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(licensestate.options[licensestate.selectedIndex].value===''){ e.innerHTML = 'License state is a required field!'; s.enabled = true; hide_loader(); return false; }
	// Validate License
	if(!isvalidlicense(licensestate.options[licensestate.selectedIndex].value, license.value)){ e.innerHTML = 'Drivers License or ID must be valid!'; s.enabled = true; hide_loader(); return false; }
	
	if(income.options[income.selectedIndex].value===''){ e.innerHTML = 'Income is a required field!'; s.enabled = true; hide_loader(); return false; }
	
	if(email.value===''){ e.innerHTML = 'Email is a required field!'; s.enabled = true; hide_loader(); return false; }
	//improved the email validate
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(!re.test(email.value)){ e.innerHTML = 'Email must be valid address!'; s.enabled = true; hide_loader(); return false; }
	
	if(password.value===''){ e.innerHTML = 'Password is a required field!'; s.enabled = true; hide_loader(); return false; }
	if(password.value.length < 6){ e.innerHTML = 'Password must be at least 6 characters long!'; s.enabled = true; hide_loader(); return false; }
	// if(confirm.value===''){ e.innerHTML = 'Confirm password is a required field!'; s.enabled = true; hide_loader(); return false; }
	// if(password.value !== confirm.value){ e.innerHTML = 'Password and confirm must match!'; s.enabled = true; hide_loader(); return false; }

	if(!terms){ e.innerHTML = 'Please agree to Terms & Conditions to submit form'; s.enabled = true; hide_loader(); return false;}

	var referenceFlag = document.getElementById('referenceFlag').value;
	
	if(referenceFlag === 'Y'){
		
		var ref1fname = document.getElementById('ref1fname');
		var ref1lname = document.getElementById('ref1lname');
		
		//var ref1phone = document.getElementById('ref1phone');
		var ref1prim1 = document.getElementById('ref1prim1').value;
		var ref1prim2 = document.getElementById('ref1prim2').value;
		var ref1prim3 = document.getElementById('ref1prim3').value;
		var ref1relation = document.getElementById('ref1relation');

		var ref2fname = document.getElementById('ref2fname');
		var ref2lname = document.getElementById('ref2lname');
		//var ref2phone = document.getElementById('ref2phone');
		var ref2prim1 = document.getElementById('ref2prim1').value;
		var ref2prim2 = document.getElementById('ref2prim2').value;
		var ref2prim3 = document.getElementById('ref2prim3').value;
		var ref2relation = document.getElementById('ref2relation');
		
		if(ref1fname.value===''){ e.innerHTML = 'Reference 1 First Name is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref1lname.value===''){ e.innerHTML = 'Reference 1 Last Name is a required field!'; s.enabled = true; hide_loader(); return false; }
		// if(ref1phone.value.length!=10){ e.innerHTML = 'Reference 1 Phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref1prim1 === '' || ref1prim1.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref1prim2 === '' || ref1prim2.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref1prim3 === '' || ref1prim3.length < 4){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref1relation.options[ref1relation.selectedIndex].value===''){ e.innerHTML = 'Reference 1 Relation is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2fname.value===''){ e.innerHTML = 'Reference 2 First Name is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2lname.value===''){ e.innerHTML = 'Reference 2 Last Name is a required field!'; s.enabled = true; hide_loader(); return false; }
		// if(ref2phone.value.length!=10){ e.innerHTML = 'Reference 2 Phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2prim1 === '' || ref2prim1.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2prim2 === '' || ref2prim2.length < 3){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2prim3 === '' || ref2prim3.length < 4){ e.innerHTML = 'Primary phone is a required field!'; s.enabled = true; hide_loader(); return false; }
		if(ref2relation.options[ref2relation.selectedIndex].value===''){ e.innerHTML = 'Reference 2 Relation is a required field!'; s.enabled = true; hide_loader(); return false; }

		// Check if phone is the same
		if((ref1prim1 + ref1prim2 + ref1prim3)===(ref2prim1 + ref2prim2 + ref2prim3)){ e.innerHTML = 'Reference phones can not be the same!'; s.enabled = true; hide_loader(); return false; }
		
		$.ajax({
			url:'/accept/step3/checkEmail',
			cache:false,
			data:{
				'email':email.value
			},
			dataType:"json",
			type:"POST",
			success:function(res){
				if(res['error']){
					if(res['error_code'] == '-2'){
						e.innerHTML = 'Email address must be valid';
					}else{
						e.innerHTML = 'Email address already exists with another user!';
					}
					s.enabled = true; hide_loader();return false;
				}else{
					$("#formStep3").submit();
				}
			},
			error:function(res){
				console.log('error from verifying email',res);
			}
		});
	}	
}

//auto-format phone number at step3
function setcorrectphone(_ev, _id)
{
    var phonelength = document.getElementById(_id).value.length;
    var patt1 = /[0-9]/g;
    var c = String.fromCharCode(_ev.which);
    if (!isNaN(c) && phonelength < 13) //enter here only if a digit is entered
    {
        if ((phonelength == 0)) //before the first digit, add a (
        {
        	document.getElementById(_id).value = '(';
        }
        else if ((phonelength == 4)) //add a ) after the third digit (fourth char because of the '(')
        {
        	document.getElementById(_id).value = document.getElementById(_id).value + ')';
        }
        else if ((phonelength == 8)) //after the eigth digit add a '-'
        {
        	document.getElementById(_id).value = document.getElementById(_id).value + '-';
        }
        return true;
    }
    else //enter here if not a digit
    {
        return false;
    }

};

//validateDL at step3
function isvalidlicense(_state, _license)
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

//play video for step4
function playvideo(fname)
{
	var f = document.getElementById('preapp-iframe');

	setTimeout(function(){ f.style.visibility = 'visible'; }, 3000);
	var link = 'http://welcome.viddyo.com/welcome.aspx?wid=13&tid=239&vfn=' + fname +'&wel=Congratulations&b1t=Shop%20Now&b1u=http://icuracao.com/';
	f.src = link;
	setTimeout(function(){ f.src = ''; f.style.visibility = 'hidden'; }, 35000);
}
