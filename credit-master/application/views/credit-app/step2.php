<!-- Step2: Address, Monthly Income and Date of Birth -->
<div id="step2" class="step-container">
  <!-- Header -->
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
      <img class="progress-image" src="/ci_media/images/credit_app/step2_progress.png" />
    </div>
    <div class="content-body">
      <div class="step-title">
        <?= lang('oca_step2_title') ?>
      </div>
      
      <div class="input-wrapper">
        <!-- Address -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_address') ?></span>
            <input type="text" class="input" id="step2-address" onfocus="hide_error('step2-address')" onblur="check_step2_input('step2-address')"/>	
          </div>
          <span id="step2-address-empty-error" style="display:none" class="alert error"><?= lang('oca_address') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-address-valid-error" style="display:none" class="alert error"><?= lang('oca_address') ?><?= lang('oca_valid_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Apt Number -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_apt') ?><span class="optional"> (<?= lang('oca_optional') ?>)</span></span>
            <input type="text" class="input" id="step2-address-aptnumber"/>
          </div>
        </div>
              
        <!-- Zip Code -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_zip') ?></span>
            <input type="tel" class="input" id="step2-zip" maxlength="5" onfocus="hide_error('step2-zip')" onblur="check_step2_input('step2-zip')"/>
          </div>
          <span id="step2-zip-empty-error" style="display:none" class="alert error"><?= lang('oca_zip') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-zip-valid-error" style="display:none" class="alert error"><?= lang('oca_zip_valid') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- City Name -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_city') ?></span>
            <input type="text" class="input" id="step2-city" onfocus="hide_error('step2-city')" onblur="check_step2_input('step2-city')" maxlength="15"/>
          </div>
          <span id="step2-city-empty-error" style="display:none" class="alert error"><?= lang('oca_city') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
                
        <!-- State Name -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_state') ?></span>
            <select class="dropdown" id="step2-state" onclick="hide_error('step2-state')">
              <option value=""><?= lang('oca_select') ?></option>
              <option value="CA">California</option>
              <option value="NV">Nevada</option>
              <option value="AZ">Arizona</option>
            </select>
          </div>
          <span id="step2-state-empty-error" style="display:none" class="alert error"><?= lang('oca_state') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Monthly Income -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_income') ?> <span class="read" onclick="showInfo('income-info')">?</span></span>
            <input type="text" class="input" id="step2-income" maxlength="6" onfocus="hide_error('step2-income')" onblur="check_step2_input('step2-income')"/>
          </div>
          <span id="step2-income-empty-error" style="display:none" class="alert error"><?= lang('oca_income') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-income-valid-error" style="display:none" class="alert error"><?= lang('oca_income') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-income-confirm" style="display:none" class="alert confirm"><?= lang('oca_income_confirm') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="income-info" style="display:none" class="alert confirm"><?= lang('oca_income_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="income-low" style="display:none" class="alert error">Sorry the minimum montly income we accpet is $400<span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
                
        <!-- Date of Birth -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_dob') ?></span>
            <div class="dob-container">
              <input type="tel" class="dob-date" id="step2-dob-month" placeholder="mm" maxlength="2" onfocus="hide_error('step2-dob-month')" onblur="check_step2_input('step2-dob-month')"/> /
              <input type="tel" class="dob-month" id="step2-dob-date" placeholder="dd" maxlength="2" onfocus="hide_error('step2-dob-date')" onblur="check_step2_input('step2-dob-date')"/> /
              <input type="tel" class="dob-year" id="step2-dob-year" placeholder="yyyy" maxlength="4" onfocus="hide_error('step2-dob-year')" onblur="check_step2_input('step2-dob-year')"/>	
            </div>
          </div>
          <span id="step2-dob-month-valid-error" style="display:none" class="alert error"><?= lang('oca_month') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-dob-date-valid-error" style="display:none" class="alert error"><?= lang('oca_date') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-dob-year-valid-error" style="display:none" class="alert error"><?= lang('oca_year') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-dob-empty-error" style="display:none" class="alert error"><?= lang('oca_dob') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step2-dob-valid-error" style="display:none" class="alert error"><?= lang('oca_dob') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>  
        
      </div>
                
        <button class="step button" onclick="check_step2()">
           <?= lang('oca_next') ?>
        </button>	  
         <span id="step2-error" style="display:none" class="general-error alert error"></span>
         <img class="center-image" src="/ci_media/images/credit_app/trust_badge.png"/>
    </div>
  </div>	
  
  <div class="page-footer">
    
    <div class="footer-phone">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
</div>