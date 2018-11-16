<!-- Step3: DocA or DocB form -->
<div id="step3" class="step-container">
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
        <?= lang('oca_step3_title') ?>
      </div>
      
      <!-- US ID -->
      <div class="left">
        <div class="id-container" id="us-id">
          <div class="id-image-container">
            <img id="us-id-image" class="id-image" src="/ci_media/images/credit_app/docA_inactive.png" />
          </div>
          <div class="id-image-desc">
          <?= lang('oca_step3_text1') ?>
          </div>
        </div>
        <div class="id-helper" onclick="showInfo('docA-info')">
          <?= lang('oca_step3_text2') ?>
          <span id="docA-info" style="display:none" class="alert confirm"><?= lang('oca_doca_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
      
      </div>
      
      <div class="desktop-only or-choice">
        or
      </div>
      
      <!-- Internation ID -->
      <div class="right">
        <div class="id-container" id='int-id'>
          <div class="id-image-container">
            <img id="int-id-image" class="id-image" src="/ci_media/images/credit_app/docB_inactive.png" />
          </div>
          <div class="id-image-desc">
            <?= lang('oca_step3_text3') ?>
          </div>
        </div>
        <div class="id-helper" onclick="showInfo('docB-info')">
          <?= lang('oca_step3_text2') ?>
          <span id="docB-info" style="display:none" class="alert confirm"><?= lang('oca_docb_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
      </div>
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