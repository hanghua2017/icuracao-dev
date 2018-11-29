<link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step3.css" />

<?php

$terms = strip_tags(lang('oca_terms_content'));
$terms = preg_replace("/[\r\n]*/","",$terms);

?>
<script>

// window.onload = function(e)
// {
// 	setSelectedIndex(document.getElementById('income'), '');
// 	setSelectedIndex(document.getElementById('ref1relation'), '');
// 	setSelectedIndex(document.getElementById('ref2relation'), '');
// 	setSelectedIndex(document.getElementById('licensestate'), '');
// };

document.addEventListener("DOMContentLoaded", function() {
  //check full ssn is set or not
  let ssn1 = localStorage.getItem('ssn1');
  let ssn2 = localStorage.getItem('ssn2');
  let ssn3 = localStorage.getItem('ssn3');
  if(!isnull(ssn1) && !isnull(ssn2) && !isnull(ssn3)){
    document.getElementById('ssn1').value = ssn1;
    document.getElementById('ssn2').value = ssn2;
    document.getElementById('ssn3').value = ssn3;
  }
});

</script>

<div class="main">
  <div class="col-main">
    <?php if(isset($error)){ ?>
      <div class="alert error">
        <?= $error;?>
      </div>
    <?php } ?>
    <div id="preapp-content">

      <div id="preapp-header"><?=lang('preapp_header')?></div>

      <form action="/accept/step4" id="formStep3" name="formStep3" method="post">
        <fieldset id="preapp-fieldset">

	         <h3 class="preapp-saying"><?=lang('preapp_provide')?></h3>
	          <hr class="preapp-hr">

	           <label class="preapp-label"><?=lang('preapp_license')?> <span class="preapp-required">*</span></label>
	            <select class="not-custom no-mouseflow" name="licensestate" id="licensestate">
            		<option value="">-- State --</option>
            		<option value="AZ">Arizona</option>
            		<option value="CA">California</option>
            		<option value="NV">Nevada</option>
            	</select>
	           
             <input name="license" id="license" type="text" value="" />

          	<label class="preapp-label"><?=lang('preapp_income')?> <span class="preapp-required">*</span></label>
          	<select class="not-custom" name="income" id="income">
          		<option value="">-- Income --</option>
          		<option value="1250">$1000 - $1500</option>
          		<option value="1750">$1500 - $2000</option>
          		<option value="2250">$2000 - $2500</option>
          		<option value="2750">$2500 - $3000</option>
          		<option value="3250">$3000 - $3500</option>
          		<option value="3750">$3500 - $4000</option>
          		<option value="4250">$4000 - $4500</option>
          		<option value="4750">$4500 - $5000</option>
          		<option value="5250">$5000 - $5500</option>
          		<option value="5750">$5500 - $6000</option>
          		<option value="6250">$6000 - $6500</option>
          		<option value="6750">$6500 - $7000</option>
          	</select>

          	<hr class="preapp-hr">
          	<h3 class="preapp-saying"><?=lang('preapp_saying4')?></h3>
          	<hr class="preapp-hr">

          	<label class="preapp-label"><?=lang('preapp_email')?><span class="preapp-required">*</span></label>
          	<input name="email" id="email" type="text" value="" />

          	<label class="preapp-label"><?=lang('preapp_password')?><span class="preapp-required">*</span></label>
          	<input class="no-mouseflow" type="password" name="password" id="password" value="" />

            <input type="hidden" id="referenceFlag" name="referenceFlag" value="<?=$referenceFlag?>">
            <?php if($referenceFlag == 'Y') { ?>

	           <hr class="preapp-hr">

	           <h3 class="preapp-saying"><?=lang('preapp_saying8')?></h3>

	           <hr class="preapp-hr">

          	 <span class="preapp-required">*</span>
          	 <input type="text" name="ref1fname" id="ref1fname" placeholder="<?=lang('preapp_fname')?>" />
          	 <input type="text" name="ref1lname" id="ref1lname" placeholder="<?=lang('preapp_lname')?>" />

            	<label class="preapp-label" id="ref1phonelabel"><?=lang('preapp_phone')?><span class="preapp-required">*</span></label>
            	<input class="no-mouseflow phone" name="ref1prim1" id="ref1prim1" type="text" maxlength="3" onkeyup="return phonefocus('ref1prim1','ref1prim2');" onkeypress="return numberformat(event);" />
            	<input class="no-mouseflow phone" name="ref1prim2" id="ref1prim2" type="text" maxlength="3" onkeyup="return phonefocus('ref1prim2','ref1prim3');" onkeypress="return numberformat(event);" />
            	<input class="no-mouseflow phone" name="ref1prim3" id="ref1prim3" type="text" maxlength="4" onkeypress="return numberformat(event);" />

            	<select class="not-custom" name="ref1relation" id="ref1relation">
                  <option value="">-- <?=lang('preapp_relationship')?> --</option>
                  <option value="1"><?=lang('preapp_sonparent')?></option>
                  <option value="2"><?=lang('preapp_brothersister')?></option>
                  <option value="3"><?=lang('preapp_uncle')?></option>
                  <option value="4"><?=lang('preapp_coparent')?></option>
                  <option value="5"><?=lang('preapp_friends')?></option>
            	</select>

            	<br>
            	<span class="preapp-required">*</span>
            	<input type="text" name="ref2fname" id="ref2fname" placeholder="<?=lang('preapp_fname')?>" />
            	<input type="text" name="ref2lname" id="ref2lname" placeholder="<?=lang('preapp_lname')?>" />

            	<label class="preapp-label" id="ref2phonelabel"><?=lang('preapp_phone')?><span class="preapp-required">*</span></label>
            	<input class="no-mouseflow phone" name="ref2prim1" id="ref2prim1" type="text" maxlength="3" onkeyup="return phonefocus('ref2prim1','ref2prim2');" onkeypress="return numberformat(event);" />
            	<input class="no-mouseflow phone" name="ref2prim2" id="ref2prim2" type="text" maxlength="3" onkeyup="return phonefocus('ref2prim2','ref2prim3');" onkeypress="return numberformat(event);" />
            	<input class="no-mouseflow phone" name="ref2prim3" id="ref2prim3" type="text" maxlength="4" onkeypress="return numberformat(event);" />

            	<select class="not-custom" name="ref2relation" id="ref2relation">
                  <option value="">-- <?=lang('preapp_relationship')?> --</option>
                  <option value="1"><?=lang('preapp_sonparent')?></option>
                  <option value="2"><?=lang('preapp_brothersister')?></option>
                  <option value="3"><?=lang('preapp_uncle')?></option>
                  <option value="4"><?=lang('preapp_coparent')?></option>
                  <option value="5"><?=lang('preapp_friends')?></option>
            	</select>

      
        <?php } ?>
	       <hr class="preapp-hr">

      	<label class="preapp-label-checkbox preapp-label-terms"><input class="preapp-checkbox" name="terms" id="terms" type="checkbox"  /> <?=lang('preapp_terms')?>
      	<?php $terms_text = preg_replace( "/\r|\n/","",$terms); ?>
      	<a><span id="show_tcpa" class="question" onclick="popup_show('Terms & Conditions', 400, 300, '<?= htmlentities($terms_text)?>',function(){});" title="Show Information">Click here to Review Terms</span></a>
      	</label>

      	<div id="preapp-submit-holder">
      		<button id="btnsubmit" name="btnsubmit" type="button" form="formStep3" onclick="return validateStep3();" ><?=lang('preapp_submit3')?></button>
          <span id="preapp-error"></span>
      	</div>
        
      	<input type="hidden" id="prid" name="prid" value="<?=$prid?>" />
      	<input type="hidden" id="ssn" name="ssn" value="<?=$ssn?>" />
      	<input type="hidden" id="arssn" name="arssn" value="<?=$arssn?>" />
        <input type="hidden" id="ssn1" name="ssn1" value=""/>
        <input type="hidden" id="ssn2" name="ssn2" value=""/>
        <input type="hidden" id="ssn3" name="ssn3" value=""/>
        </fieldset>
      </form>
    </div>
  </div>
</div>

<div class="preapp-page-footer2 mobile-only">
	<div class="preapp-footer-phone">
		<!-- <img class="footer-image" src="/ci_media/images/credit_app/phone.png" /> -->
		<a class="phone-number" href="tel:1-877-287-2266">1(877)287 2266</a>
	</div>
</div>