<div id="pverify" class="step-container">
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
      <img class="progress-image" src="/ci_media/images/credit_app/step1_progress.png" />
    </div>
    <div class="content-body">
      <div class="step-title pverify">
        <?= lang('oca_pverify_title') ?>
      </div>
      <div class="container">
        <div class="text">
          <?= lang('oca_pverify_text2') ?> <span id="pverify-phone"></span> <?= lang('oca_pverify_text3') ?>
        </div>
        <div class="step-input">
          <input type="text" class="input" id="pverify-code" onfocus="hide_error('pverify-code')" onblur="check_pverify_input('pverify-code')" maxlength="6" placeholder="<?= lang('oca_enter_code')?>"/>	
        </div>
        <span id="pverify-code-empty-error" style="display:none" class="alert error"><?= lang('oca_verify_code') ?><?= lang('oca_empty_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        <span id="pverify-code-valid-error" style="display:none" class="alert error"><?= lang('oca_verify_code') ?><?= lang('oca_valid_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        <span id="verify-error" style="display:none" class="general-error alert error"></span>
        <button id="pverify-resendCode" class="resend-code" onclick="resendCode()">Send Code Again  <span id="onem-countdown"></span></button>
        <button class="step button" onclick="check_pverify()">
          <?= lang('oca_continue1') ?>
        </button>	
          <img class="center-image" src="/ci_media/images/credit_app/trust_badge.png"/>
      </div>
    
    
    </div>
      
  </div>
  
  <div class="page-footer">
    
    <div class="footer-phone">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
  
</div>