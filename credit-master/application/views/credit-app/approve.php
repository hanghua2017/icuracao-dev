<div id="approve" class="step-container">
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
  <div class="decision-body">
    <div class="desktop-only">
      <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
      <span class="desktop-title">Credit Application</span>
    </div>
    <div class="content-body decision-page">
      <div class="approve-title"><?= lang('oca_approve_title') ?><div id='approve_fname'></div></div>
      <div class="decision">
        <?= lang('oca_approve_text4') ?>
      </div>
      <div class="welcome">
         <br/>
        <?= lang('oca_pending_3') ?><?= lang('oca_pending_4')?>
      </div>
      
      <div class="account-container">
        <span class="row-left"><span class="account-label"><?= lang('oca_account')  ?></span></span>
        <span class="row-right"><span id='approve_account_number'></span></span>
      </div>
      <div class="account-container">
        <span class="row-left"><span class="account-label"><?= lang('oca_credit_limit')  ?></span></span>
        <span class="row-right">$<span id='approve_credit_limit'></span></span>
      </div>
      
      <button class="decision-button button" onclick="homepage()">
        <?= lang('oca_start_shopping') ?>
      </button>	
    </div>
    
  </div>
  
  <div class="page-footer">
    <div class="footer-phone decision-last">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
  
  
</div>	