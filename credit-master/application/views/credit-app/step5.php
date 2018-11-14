<div id="step5" class="step-container">
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
        <?= lang('oca_step5_title') ?>
      </div>
      <div class="input-wrapper">
        <!-- Content body -->
        <div class="left">
          <span class="ref-title"><?= lang('oca_ref_1') ?></span>
        
            <!-- First Name1-->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_fname') ?></span>
              <input type="text" class="input" id="step5-fname1" onfocus="hide_error('step5-fname1')" onblur="check_step5_input('step5-fname1')" maxlength="15"/>
            </div>
            <span id='step5-fname1-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_fname') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-fname1-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_fname') ?><?= lang('oca_valid_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            
            <!-- Last Name1 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_lname') ?></span>
              <input type="text" class="input" id="step5-lname1" onfocus="hide_error('step5-lname1')" onblur="check_step5_input('step5-lname1')" maxlength="15"/>
            </div>
            <span id='step5-lname1-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_lname') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-lname1-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_lname') ?><?= lang('oca_valid_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
          
          <!-- Phone Number1 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_phone') ?></span>
              <input type="tel" class="input" id="step5-phone1" placeholder="e.g.(123)456-7890" onfocus="hide_error('step5-phone1')" onblur="check_step5_input('step5-phone1')"/>
            </div>
            <span id='step5-phone1-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_phone') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-phone1-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_phone') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>

          <!-- Relationship1 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_relation') ?></span>
              <select class="dropdown tenvh" id="step5-rel1" onclick="hide_error('step5-rel1')">
                <option value=""><?= lang('oca_select') ?></option>
                <option value="1"><?= lang('oca_ref1') ?></option>
                <option value="2"><?= lang('oca_ref2') ?></option>
                <option value="3"><?= lang('oca_ref3') ?></option>
                <option value="4"><?= lang('oca_ref4') ?></option>
                <option value="5"><?= lang('oca_ref5') ?></option>
              </select>
              <select id="width_tmp_select_1" style="display:none">
                <option id="width_tmp_option_1"></option>
              </select>
            </div>
            <span id='step5-rel1-empty-error' class="alert error" style="display:none"><?= lang('oca_choice_rel') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>

          <!-- Reference2 -->
          <div class="right">
            <span class="ref-title"><?= lang('oca_ref_2')  ?></span>
            <!-- First Name2 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_fname') ?></span>
              <input type="text" class="input" id="step5-fname2" onfocus="hide_error('step5-fname2')" onblur="check_step5_input('step5-fname2')" maxlength="15"/>
            </div>
            <span id='step5-fname2-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_fname') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-fname2-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_fname') ?><?= lang('oca_valid_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            
            <!-- Last Name2 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_lname') ?></span>
              <input type="text" class="input" id="step5-lname2" onfocus="hide_error('step5-lname2')" onblur="check_step5_input('step5-lname2')" maxlength="15"/>
            </div>
            <span id='step5-lname2-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_lname') ?><?= lang('oca_empty_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-lname2-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_lname') ?><?= lang('oca_valid_err')?><span onclick="closeCurrent(this)" class="close">X</span></span>

            <!-- Phone Number2 -->
          
            <div class="step-input">
              <span class="step-label"><?= lang('oca_ref_phone') ?></span>
              <input type="tel" class="input" id="step5-phone2" placeholder="e.g.(123)456-7890" onfocus="hide_error('step5-phone2')" onblur="check_step5_input('step5-phone2')"/>
            </div>
            <span id='step5-phone2-empty-error' class="alert error" style="display:none"><?= lang('oca_ref_phone') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id='step5-phone2-valid-error' class="alert error" style="display:none"><?= lang('oca_ref_phone') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>

            <!-- Relationship2 -->
            <div class="step-input">
              <span class="step-label"><?= lang('oca_relation') ?></span>
              <select class="dropdown tenvh" id="step5-rel2" onclick="hide_error('step5-rel2')">
                <option value=""><?= lang('oca_select') ?></option>
                <option value="1"><?= lang('oca_ref1') ?></option>
                <option value="2"><?= lang('oca_ref2') ?></option>
                <option value="3"><?= lang('oca_ref3')?></option>
                <option value="4"><?= lang('oca_ref4') ?></option>
                <option value="5"><?= lang('oca_ref5') ?></option>
              </select>
              <select id="width_tmp_select_2" style="display:none">
                <option id="width_tmp_option_2"></option>
              </select>
            </div>
            <span id='step5-rel2-empty-error' class="alert error" style="display:none"><?= lang('oca_choice_rel') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
      </div>
      <!-- Submit button -->
      <button class="step button" onclick="check_step5()">
         <?= lang('oca_submit') ?>
      </button>	
      <span id="step5-error" style="display:none" class="general-error alert error"></span>
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