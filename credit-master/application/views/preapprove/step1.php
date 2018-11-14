<link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step1.css" />
<script>

window.onload = function(e)
{
	setSelectedIndex(document.getElementById('state'), '<?= $state ?>');
	setSelectedIndex(document.getElementById('mm'), '<?= $mm ?>');
	setSelectedIndex(document.getElementById('dd'), '<?= $dd ?>');
	setSelectedIndex(document.getElementById('yyyy'), '<?= $yyyy ?>');
};

//store arssn for verify purpose
document.addEventListener("DOMContentLoaded", function() {
	let arssn ='<?= $arssn ?>';
	localStorage.setItem('arssn',arssn);
	
	localStorage.setItem('bypassSSNcheck','no');
	let promo = '<?= $promo ?>';
	let campaign_code = promo.substr(0,2);
  
	if(campaign_code.toUpperCase() == 'BF'){
		document.getElementById('partial-ssn-wrapper').style.display = 'none';
		document.getElementById('full-ssn-wrapper').style.display = 'block';
		localStorage.setItem('bypassSSNcheck','yes');
	}
});


hide_loader();

<?php

$tcpa = strip_tags(lang('oca_tcpa_content'));
$tcpa = preg_replace("/[\r\n]*/","",$tcpa);

?>

</script>


<div class="main">
  <div class="col-main">
    
  
<div id="preapp-content">
  
<div id="preapp-header"><?=lang('preapp_header')?></div>

<form onsubmit="document.getElementById('state').disabled = false;" action="/accept/step2" id="form1" name="form1" method="post">
<fieldset id="preapp-fieldset">
	<div id="preapp-top">
		<h3 id="preapp-saying"><?= lang('preapp_saying1') ?></h3>
		<span id="preapp-edit" onclick="edit();"><?=lang('preapp_edit');?></span>
	</div>
	<hr class="preapp-hr">
        <input type="hidden" name="referenceFlag" value="<?=$referenceFlag?>">
	<label class="preapp-label"><?=lang('preapp_fname')?></label>
	<input class="no-mouseflow"  name="fname" id="fname" type="text" value="<?=$fname?>" readonly="true" />

	<label class="preapp-label"><?=lang('preapp_lname')?></label>
	<input class="no-mouseflow" name="lname" id="lname" type="text" value="<?=$lname?>" readonly="true" />

	<label class="preapp-label"><?=lang('preapp_address')?><span class="preapp-required">*</span></label>
	<input class="no-mouseflow" name="address" id="address" type="text" value="<?=$address?>" readonly="true" />

	<label class="preapp-label"><?=lang('preapp_city')?><span class="preapp-required">*</span></label>
	<input class="no-mouseflow" name="city" id="city" type="text" value="<?=$city?>" readonly="true" />

  <!-- add not-custom to get rid of the wrong select box -->
	<label class="preapp-label"><?=lang('preapp_state')?><span class="preapp-required">*</span></label>
	<select name="state" id="state" disabled="true" class="not-custom no-mouseflow">
		<option value="">-- State --</option>
		<option value="AL">Alabama</option>
		<option value="AK">Alaska</option>
		<option value="AZ">Arizona</option>
		<option value="AR">Arkansas</option>
		<option value="CA">California</option>
		<option value="CO">Colorado</option>
		<option value="CT">Connecticut</option>
		<option value="DE">Delaware</option>
		<option value="DC">District of Columbia</option>
		<option value="FL">Florida</option>
		<option value="GA">Georgia</option>
		<option value="HI">Hawaii</option>
		<option value="ID">Idaho</option>
		<option value="IL">Illinois</option>
		<option value="IN">Indiana</option>
		<option value="IA">Iowa</option>
		<option value="KS">Kansas</option>
		<option value="KY">Kentucky</option>
		<option value="LA">Louisiana</option>
		<option value="ME">Maine</option>
		<option value="MD">Maryland</option>
		<option value="MA">Massachusetts</option>
		<option value="MI">Michigan</option>
		<option value="MN">Minnesota</option>
		<option value="MS">Mississippi</option>
		<option value="MO">Missouri</option>
		<option value="MT">Montana</option>
		<option value="NE">Nebraska</option>
		<option value="NV">Nevada</option>
		<option value="NH">New Hampshire</option>
		<option value="NJ">New Jersey</option>
		<option value="NM">New Mexico</option>
		<option value="NY">New York</option>
		<option value="NC">North Carolina</option>
		<option value="ND">North Dakota</option>
		<option value="OH">Ohio</option>
		<option value="OK">Oklahoma</option>
		<option value="OR">Oregon</option>
		<option value="PA">Pennsylvania</option>
		<option value="RI">Rhode Island</option>
		<option value="SC">South Carolina</option>
		<option value="SD">South Dakota</option>
		<option value="TN">Tennessee</option>
		<option value="TX">Texas</option>
		<option value="UT">Utah</option>
		<option value="VT">Vermont</option>
		<option value="VA">Virginia</option>
		<option value="WA">Washington</option>
		<option value="WV">West Virginia</option>
		<option value="WI">Wisconsin</option>
		<option value="WY">Wyoming</option>
	</select>

	<label class="preapp-label"><?=lang('preapp_zip')?><span class="preapp-required">*</span></label>
	<input class="no-mouseflow" name="zip" id="zip" type="text" value="<?=$zip?>" readonly="true" maxlength="5" />

	<hr class="preapp-hr">
	
	<div id="partial-ssn-wrapper">
		<label class="preapp-label"><?=lang('preapp_ssn')?><span class="preapp-required">*</span></label>
		<input class="no-mouseflow ssn" name="ssn" id="ssn" type="text" value="<?=$ssn?>" maxlength="3" onkeypress="return numberformat(event);" />
		<span id="ssnformat"> - XX - XXXX</span>
	</div>
	
	<div id="full-ssn-wrapper" style="display:none">
		<label class="preapp-label"><?=lang('preapp_fullssn')?><span class="preapp-required">*</span></label>
		<input class="no-mouseflow ssn" name="ssn1" id="ssn1" type="text" maxlength="3" onkeyup="return ssnfocus('ssn1','ssn2')" onkeypress="return numberformat(event);" />
		<input class="no-mouseflow ssn" name="ssn2" id="ssn2" type="text" maxlength="2" onkeyup="return ssnfocus('ssn2','ssn3')" onkeypress="return numberformat(event);" />
		<input class="no-mouseflow ssn" name="ssn3" id="ssn3" type="text" maxlength="4" onkeypress="return numberformat(event);" />
	</div>
	
  <!-- 	deal with the view problem in FireFox -->
	<div style="clear:both"></div>

	<label class="preapp-label"><?=lang('preapp_dob')?> <span class="preapp-required">*</span></label>
	<select name="mm" id="mm" class="not-custom no-mouseflow">
		<option value="">mm</option>
		<option value="01">01</option>
		<option value="02">02</option>
		<option value="03">03</option>
		<option value="04">04</option>
		<option value="05">05</option>
		<option value="06">06</option>
		<option value="07">07</option>
		<option value="08">08</option>
		<option value="09">09</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
	</select>

	<select name="dd" id="dd" class="not-custom no-mouseflow">
		<option value="">dd</option>
		<option value="01">01</option>
		<option value="02">02</option>
		<option value="03">03</option>
		<option value="04">04</option>
		<option value="05">05</option>
		<option value="06">06</option>
		<option value="07">07</option>
		<option value="08">08</option>
		<option value="09">09</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
		<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
		<option value="19">19</option>
		<option value="20">20</option>
		<option value="21">21</option>
		<option value="22">22</option>
		<option value="23">23</option>
		<option value="24">24</option>
		<option value="25">25</option>
		<option value="26">26</option>
		<option value="27">27</option>
		<option value="28">28</option>
		<option value="29">29</option>
		<option value="30">30</option>
		<option value="31">31</option>
	</select>

	<select name="yyyy" id="yyyy" class="not-custom no-mouseflow">
		<option value="">yyyy</option>
                <?php
                    // Eligible age / year for credit
                    $start = date('Y') - 18;
                    $end = $start - 99;
                    $j = $start;

                     while($j > $end ) {
                    ?>
                <option value="<?=$j?>"><?=$j?></option>
                <?php $j--; } ?>
	</select>

	<label class="preapp-label"><?=lang('preapp_prim')?><span class="preapp-required">*</span></label>
	<input class="no-mouseflow" name="prim1" id="prim1" type="text" value="<?=$prim1?>" maxlength="3" onkeyup="return phonefocus('prim1','prim2');" onkeypress="return numberformat(event);" />
	<input class="no-mouseflow" name="prim2" id="prim2" type="text" value="<?=$prim2?>" maxlength="3" onkeyup="return phonefocus('prim2','prim3');" onkeypress="return numberformat(event);" />
	<input class="no-mouseflow" name="prim3" id="prim3" type="text" value="<?=$prim3?>" maxlength="4" onkeypress="return numberformat(event);" />

	<label class="preapp-label"><?=lang('preapp_sec')?></label>
	<input class="no-mouseflow" name="sec1" id="sec1" type="text" value="<?=$sec1?>" maxlength="3" onkeyup="return phonefocus('sec1','sec2');" onkeypress="return numberformat(event);" />
	<input class="no-mouseflow" name="sec2" id="sec2" type="text" value="<?=$sec2?>" maxlength="3" onkeyup="return phonefocus('sec2','sec3');" onkeypress="return numberformat(event);" />
	<input class="no-mouseflow" name="sec3" id="sec3" type="text" value="<?=$sec3?>" maxlength="4" onkeypress="return numberformat(event);" />

  <label class="preapp-label-checkbox preapp-label-terms"><input class="preapp-checkbox" name="tcpa" id="tcpa" type="checkbox" value="1" /> <?=lang('preapp_tcpa')?>
  <?php $tcpa_text = preg_replace( "/\r|\n/","",$tcpa); ?>
  <a><span id="show_tcpa" class="question" onclick="popup_show('TCPA', 400, 300, '<?= htmlentities($tcpa_text)?>',function(){});" title="Show Information">TCPA</span></a>
  </label>


	<div id="preapp-submit-holder">
		<button id="btnsubmit" name="btnSubmit" type="button" form="form1" onclick="return validateStep1();" value=""><?=lang('preapp_submit1')?></button>
		<span id="preapp-error"></span>
	</div>

  <p class="pdf-downloads"><?=lang('preapp_landing_pricing_terms')?> 
    <a class="termlinks" href="<?= lang("preapp_terms_ca"); ?>">California</a>, 
    <a class="termlinks" href="<?= lang("preapp_terms_az"); ?>">Arizona</a>
    <?= lang("preapp_terms_or");; ?>
    <a class="termlinks" href="<?= lang("preapp_terms_nv");; ?>">Nevada</a>
  </p>

	<input type="hidden" id="prid" name="prid" value="<?=$prid?>" />
	<input type="hidden" id="arssn" name="arssn" value="<?=$arssn?>" />

</form>

</fieldset>

    
</div>
</div>
</div>

<div class="preapp-page-footer2 mobile-only">
	<div class="preapp-footer-phone">
		<!-- <img class="footer-image" src="/ci_media/images/credit_app/phone.png" /> -->
		<a class="phone-number" href="tel:1-877-287-2266">1(877)287 2266</a>
	</div>
</div>


