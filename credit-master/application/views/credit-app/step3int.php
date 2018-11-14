<!-- DocB: Country of issurance, ID type, ID Number and Mother's Maiden Name -->
<div id="step3-int"  class="step-container">
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
        <?= lang('oca_step3_int_title') ?>
      </div>
      <div class="input-wrapper">
        <!-- Country of ID Issurance -->
        <div class="left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_step3_int_country') ?></span>
            <select class="twpc dropdown" id="step3-int-country" onclick="hide_error('step3-int-country')">
              <option value=""><?= lang('oca_select') ?></option>
              <option value="ES">El Salvador</option>
              <option value="GU">Guatemala</option>
              <option value="MX">Mexico</option>
              <option value="Other">Other</option>
            </select>
            <select id="country-tmp-select" style="display:none">
              <option id="country-tmp-option"></option>
            </select>
          </div>
          <span id="step3-int-country-empty-error" style="display:none" class="alert error"><?= lang('oca_step3_int_country') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Subtype of ID choice -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_id_type') ?></span>
            <!-- ES ID type -->
            <select id="step3-int-idtype" class="quarterpc dropdown"></select>
            <select id="width_tmp_select" style="display:none">
              <option id="width_tmp_option"></option>
            </select>
          </div>
          <span id="step3-int-idtype-empty-error" style="display:none" class="alert error"><?= lang('oca_id_type') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
          
        <!-- ID number for DocB -->
        <div class="left" style="clear:left">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_id_number') ?></span>
            <input type="text" class="input" id="step3-int-idnumber" onfocus="hide_error('step3-us')" onblur="check_step3_int_input('step3-int-idnumber')" maxlength="15"/>	
          </div>
          <span id="step3-int-idnumber-empty-error" style="display:none" class="alert error"><?= lang('oca_id_number') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step3-int-idnumber-valid-error" style="display:none" class="alert error"><?= lang('oca_id_number') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
      
        <!-- Mother's Maiden Name -->
        <div class="right">
          <div class="step-input">
            <span class="step-label"><?= lang('oca_maiden_name') ?><span class="question" onclick="showInfo('maiden-info')">?</span></span>
            <input type="text" class="input" id="step3-int-mname" onfocus="hide_error('step3-us-mname')" onblur="check_step3_int_input('step3-int-mname')" maxlength="15"/>	
          </div>
          <span id="step3-int-mname-empty-error" style="display:none" class="alert error"><?= lang('oca_maiden_name') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step3-int-mname-valid-error" style="display:none" class="alert error"><?= lang('oca_maiden_name') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="maiden-info" style="display:none" class="alert confirm"><?= lang('oca_maiden_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <!-- Come to store advise -->
        <div id="step3-int-cometostore-empty-error" class="alert error store" style="display:none">
          <?= lang('oca_step3_not_accept') ?><span onclick="closeCurrent(this)" class="close">X</span>
        </div>
      </div>
      
      <button class="step button" onclick="check_step3_int()" style="clear:both">
        <?= lang('oca_continue1') ?>
      </button>	
      <span id="step3-int-error" style="display:none" class="general-error alert error"></span>
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