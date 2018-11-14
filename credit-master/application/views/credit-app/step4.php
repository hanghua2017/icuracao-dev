<!-- Step4:bank account type, live time -->
<div id="step4" class="step-container">
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
    <div class="desktop-only">
      <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
      <span class="desktop-title">Credit Application</span>
    </div>
    <div class="content-body no-progress">
      <div class="step-title pending">
        <?= lang('oca_step4_title') ?>
      </div>
      <div class="intro-words">
        <?= lang('oca_step4_text1') ?>
      </div>
      
      <div class="input-wrapper">
        <!--Bank Account type -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_bank_type') ?></span> 
            <div class="bank-wrapper">
              <select class="dropdown tenvh" id="step4-bank-type" onclick="hide_error('step4-bank-type')">
                <option value=""><?= lang('oca_select')?></option>
                <option value="CHECKING"><?= lang('oca_bank_1') ?></option>
                <option value="SAVINGS"><?= lang('oca_bank_2') ?></option>
                <option value="BOTH"><?= lang('oca_bank_3') ?></option>
                <option value="NONE"><?= lang('oca_bank_none') ?></option>
              </select>
            </div>
          </div>
          <span id="step4-bank-type-empty-error" class="error alert" style="display:none"><?= lang('oca_select_bank') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Living length -->
        <div class="right">
          <div class="relative step-input">
            <span class="living step-label"><?= lang('oca_live_length')?></span>
            <div class="living-select">
              <select class="dropdown tenvh" id="step4-llength" onclick="hide_error('step4-llength')">
                <option value=""><?= lang('oca_select') ?></option>
                <option value="1.0"><?= lang('oca_live_1') ?></option>
                <option value="4.0"><?= lang('oca_live_2') ?></option>
                <option value="6.5"><?= lang('oca_live_3') ?></option>
                <option value="8.0"><?= lang('oca_live_4') ?></option>
              </select>
              <select id="width_tmp_select" style="display:none">
                <option id="width_tmp_option"></option>
              </select>
            </div>
          </div>
          <span id="step4-llength-empty-error" class="error alert" style="display:none"><?= lang('oca_choice_empty') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Employ type -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_employment_type') ?></span>
            <select class="dropdown tenvh" id="step4-emptype" onclick="hide_error('step4-emptype')">
                  <option value=""><?= lang('oca_select') ?></option>
                  <option value="C"><?= lang('oca_emp_1') ?></option>
                  <option value="D"><?= lang('oca_emp_2') ?></option>
                  <option value="E"><?= lang('oca_emp_3') ?></option>
                  <option value="M"><?= lang('oca_emp_4') ?></option>
                  <option value="O"><?= lang('oca_emp_5') ?></option>
                  <option value="P"><?= lang('oca_emp_6') ?></option>
                  <option value="R"><?= lang('oca_emp_7') ?></option>
                  <option value="S"><?= lang('oca_emp_8') ?></option>
                  <option value="T"><?= lang('oca_emp_9') ?></option>
                  <option value="U"><?= lang('oca_emp_10') ?></option>	
              </select>
              <select id="width_tmp_select_1" style="display:none">
                <option id="width_tmp_option_1"></option>
              </select>
          </div>
          <span id="step4-emptype-empty-error" class="error alert" style="display:none"><?= lang('oca_select_emp')?> <span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <div id="step4-rest-wrapper">
          <!-- Company Name -->
          <div class="right">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_company_name') ?></span>
              <input type="text" class="input" id="step4-cname" onfocus="hide_error('step4-cname')" onblur="check_step4_input('step4-cname')" maxlength="15"/>	
            </div>
            <span id="step4-cname-empty-error" class="error alert" style="display:none"><?= lang('oca_company_name') ?><?= lang('oca_empty_err') ?> <span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>

          <!-- Work length at this company -->
          <div class="left">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_work_length') ?></span>
              <select class="dropdown tenvh" id="step4-wlength" onclick="hide_error('step4-wlength')">
                    <option value=""><?= lang('oca_select') ?></option>
                    <option value="0"><?= lang('oca_work_1') ?></option>
                    <option value="1.0"><?= lang('oca_work_2') ?></option>
                    <option value="2.0"><?= lang('oca_work_3') ?></option>
                    <option value="3.0"><?= lang('oca_work_4') ?></option>
                    <option value="4.5"><?= lang('oca_work_5') ?></option>
                    <option value="8.0"><?= lang('oca_work_6') ?></option>
                    <option value="10.0"><?= lang('oca_work_7') ?></option>
                </select>
                <select id="width_tmp_select_2" style="display:none">
                  <option id="width_tmp_option_2"></option>
                </select>
            </div>

            <span id="step4-wlength-empty-error" class="error alert" style="display:none"><?= lang('oca_select_work') ?> <span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>

          <!-- Company's Phone Number -->
          <div class="right">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_company_phone') ?></span>
              <input type="tel" class="input" id="step4-phone" onfocus="hide_error('step4-phone')" onblur="check_step4_input('step4-phone')" placeholder="e.g.(123)456-7890"/>
            </div>
            <span id="step4-phone-empty-error" class="error alert" style="display:none"><?= lang('oca_company_phone') ?><?= lang('oca_empty_err') ?> <span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step4-phone-valid-error" class="error alert" style="display:none"><?=  lang('oca_company_phone') ?><?= lang('oca_valid_err') ?> <span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
        </div>
      </div>
          
      <button class="step button" onclick="check_step4()">
         <?= lang('oca_continue1') ?>
      </button>	
      <span id="step4-error" style="display:none" class="general-error alert error"></span>
      <img class="center-image" src="/ci_media/images/credit_app/trust_badge.png"/>
    </div>
    
  </div>
  <div class="push"></div>
  <div class="page-footer">
    <div class="footer-phone">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
</div>