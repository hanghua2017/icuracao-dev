<link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step2.css" />

<script  type="text/javascript">

//Send a phone call message at step2
function phonesend()
{
	show_loader();

	var v = document.getElementById('preapp-voice');
	var m = document.getElementById('preapp-msg');

	// Disable Button
	v.disabled = true;
	v.innerHTML = "<?=lang('preapp_voice')?>";

	jQuery.post("/accept/step2/sendcode", {phone: "<?=$phonenumber?>", command: "phonecall"}).done(function(_data)
	{
		if(parseInt(_data)===-1)
		{
			hide_loader();
			alert('Error sending phone call verification.');
		}
		else
		{
			hide_loader();
			m.innerHTML = "<?=lang('preapp_saying2')?> <br><br> <?=lang('preapp_sayingerror1')?>";
			console.log(_data);
			enc = _data;
		}
	});
}

// Send a text message at step2
function textsend()
{
	show_loader();

	var t = document.getElementById('preapp-text');
	var m = document.getElementById('preapp-msg');

	// Disable Button
	t.disabled = true;
	t.innerHTML = "<?=lang('preapp_text')?>";

	jQuery.post("/accept/step2/sendcode", {phone: "<?=$phonenumber?>", command: "sendtext"}).done(function(_data)
	{

		if(parseInt(_data)===-1)
		{
			hide_loader();
			alert('Error sending phone txt message.');
		}
		else
		{
			hide_loader();
			m.innerHTML = "<?=lang('preapp_saying2')?> <br><br> <?=lang('preapp_sayingerror1')?>";
			console.log(_data);
			enc = _data;
		}
	});
}

var code = 0;
var enc = '';

//verify code for step2
function verifycode()
{
	show_loader();

	var v = document.getElementById('preapp-voice');
	var t = document.getElementById('preapp-text');
	var s = document.getElementById('submit1');
	var vid = document.getElementById('vid');
	var form1 = document.getElementById('form1');
	var prid = document.getElementById('prid');

	// Check if vid
	if(vid.value.length===0){ alert("<?=lang('preapp_sayingerror3')?>"); 	hide_loader(); return false; }

	// Disable submit
	s.disabled = true;
	s.innerHTML = "<?=lang('preapp_submit2')?>->";

	// Enable Button
	v.disabled = false;
	v.innerHTML = "<?=lang('preapp_voice')?>";
	t.disabled = false;
	t.innerHTML = "<?=lang('preapp_text')?>";
  
	jQuery.post("/accept/step2/verifycode", {"enc": enc, "vid": vid.value, "prid": prid.value}).done(function(_data){
		if(parseInt(_data) === -1){
			// ReEnable submit
			hide_loader();
			s.disabled = false;
			s.innerHTML = "<?=lang('preapp_submit2')?>";
			alert("<?=lang('preapp_sayingerror4')?>");
		}
		else
		{
			form1.submit();
		}
	});

	return false;
}


hide_loader();

</script>

<div class="main">
  <div class="col-main">
      <div id="preapp-content">
        <div id="preapp-header"><?=lang('preapp_header')?></div>
        <form action="/accept/step3" id="form1" name="form1" method="post">
          <fieldset id="preapp-fieldset">
  	         <div id="preapp-round">
  		           <h3 id="preapp-saying">Dear <?=$fname?>,</h3>
  		             <span id="preapp-msg"><?=lang('preapp_saying2')?></span>
  		               <hr class="preapp-hr">
  		                 <span id="preapp-phone"><?=lang('preapp_phone')?> <?=$phonenumber?></span>
  		                   <button id="preapp-voice" type="button" onclick="phonesend(); return false;"><?=lang('preapp_voice')?></button>
                  <button id="preapp-text" type="button" onclick="textsend(); return false;"><?=lang('preapp_text')?></button>
  	          </div>
            	<div id="preapp-round2">
            		<input name="vid" id="vid" type="text" placeholder="Verification Code" onkeypress="return numberformat(event);" maxlength="6" />
            		<button id="submit1" name="submit1" type="button" onclick="return verifycode();"><?=lang('preapp_submit2')?></button>
            		<input type="hidden" id="prid" name="prid" value="<?=$prid?>" />
            		<input type="hidden" id="ssn" name="ssn" value="<?=$ssn?>" />
            		<input type="hidden" id="arssn" name="arssn" value="<?=$arssn?>" />
            		<input type="hidden" id="referenceFlag" name="referenceFlag" value="<?=$referenceFlag?>" />
            	</div>
      </fieldset>
    </form>
    </div>
  </div>
</div>

<div id="landing-footer" class="mobile-only page-footer">
  <div id="landing-phone" class="footer-phone">
    <!-- <img class="footer-image" src="/ci_media/images/credit_app/phone.png" /> -->
    <a class="phone-number" href="tel:1-877-287-2266">1(877)287 2266</a>
  </div>
</div>