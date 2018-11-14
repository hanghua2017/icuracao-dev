<!-- DocA:SSN,ID type, State, ID Number and Mothers Maiden Name -->
<div id="step3-us" class="step-container"> 
  <div class="page-header">
    <div class="header-center">
  		<img class="header-image" src="/ci_media/images/logo.png" alt="Curacao"/>		
  		<div class="desktop-only">
  			<div class="header-right">
  				<span class="header-text">Apply by Phone</span><span class="header-phone"> 1 <?= lang('oca_contact') ?></span><br/>
  				<?php $lang = $this->session->userdata('site_lang');?>
  				<?php if($lang != 'spanish'){ ?>
  					<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
  				<?php }else{ ?>
  					<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
  				<?php } ?>
  			</div>
  		</div>
  	</div>
  	<div class="mobile-only">
  	<?php $lang = $this->session->userdata('site_lang');?>
  	<?php if($lang != 'spanish'){ ?>
  		<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
  	<?php }else{ ?>
  		<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
  	<?php } ?>
  	</div>
  </div>
  <div class="page-body">
    <div class="progress">
      <div class="desktop-only">
        <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
        <span class="desktop-title">Credit Application</span>
      </div>
      <img class="progress-image" src="/ci_media/images/credit_app/step3_progress.png" />
    </div>
    <div class="content-body">
      <div class="step-title">
        <?= lang('oca_step3_us_title') ?>
      </div>
      <div class="input-wrapper">
        <!-- SSN -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_ssn') ?></span>
            <input type="text" class="input" id="step3-ssn" onfocus="hide_error('step3-ssn')" onblur="check_step3_us_input('step3-ssn')" placeholder="XXX-XX-XXXX" maxlength="11"/>	
          </div>
          <span id="step3-ssn-empty-error" style="display:none" class="alert error"><?= lang('oca_ssn') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step3-ssn-valid-error" style="display:none" class="alert error"><?= lang('oca_ssn') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        <!-- ID Type -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_id_type') ?></span>
            <div class="step-radio-container">
              <input type="radio" id="step3-us-dl" value="yes" name="step3_idtype"/>
              <label class="step3-usid-label" for="step3-us-dl"><?= lang('oca_dl') ?></label>
              <input type="radio" id="step3-us-si" name="step3_idtype"/>
              <label class="step3-usid-label" for="step3-us-si"><?= lang('oca_si') ?></label>
            </div>
          </div>
          <span id="step3-us-idtype-empty-error" style="display:none" class="alert error"><?= lang('oca_id_type') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- State for ID -->
        <div id="step3-id-selector" class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_state') ?></span>
            <select class="dropdown" id="step3-us-state" onclick="hide_error('step3-us-state')">
              <option value=""><?= lang('oca_select') ?></option>
              <option value="CA">California</option>
              <option value="NV">Nevada</option>
              <option value="AZ">Arizona</option>
            </select>
          </div>
          <span id="step3-us-state-empty-error" style="display:none" class="alert error"><?= lang('oca_state') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- ID number for DocA -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_id_number') ?></span>
            <input type="text" class="input" id="step3-us-idnumber" onfocus="hide_error('step3-us-idnumber')" onblur="check_step3_us_input('step3-us-idnumber')" maxlength="15"/>	
          </div>
          <span id="step3-us-idnumber-empty-error" style="display:none" class="alert error"><?= lang('oca_id_number') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step3-us-idnumber-valid-error" style="display:none" class="alert error"><?= lang('oca_id_number') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Mother's Maiden Name -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_maiden_name') ?><span class="question" onclick="showInfo('maiden-info')">?</span></span>
            <input type="text" class="input" id="step3-us-mname" onfocus="hide_error('step3-us-mname')" onblur="check_step3_us_input('step3-us-mname')" maxlength="15"/>	
          </div>
          <span id="step3-us-mname-empty-error" style="display:none" class="alert error"><?= lang('oca_maiden_name') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step3-us-mname-valid-error" style="display:none" class="alert error"><?= lang('oca_maiden_name') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="maiden-info" style="display:none" class="alert confirm"><?= lang('oca_maiden_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
      </div>
      
      
      <button class="step button" onclick="check_step3_us()">
         <?= lang('oca_applynow') ?>
      </button>	
      <span id="step3-us-error" style="display:none" class="general-error alert error"></span>
      <img class="center-image" src="/ci_media/images/credit_app/trust_badge.png"/>
    </div>

    <!-- <div class="demo-only">
      Demo Only: <br/>
      <button onclick="window.location.href='/credit-app2018/approve'" class="button demo">approve page</button>
      <button onclick="window.location.href='/credit-app2018/duplicate'" class="button demo">duplicate page</button>
      <button onclick="window.location.href='/credit-app2018/decline'" class="button demo">decline page</button>
      <button onclick="window.location.href='/credit-app2018/step4'" class="button demo">go to full app</button>
    </div> -->
  </div>
  
  <div class="page-footer">  
    <div class="footer-phone">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
</div>