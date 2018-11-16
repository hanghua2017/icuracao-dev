<div id="header" class="page-header landing">
	
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

<!-- landing page -->
<div id="CCA_center">
	<div id="CCA_landing">
		 
		 <div class="title">
			 <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
			 <?= lang('oca_landing_title1') ?>
		 </div>
		 
		<!-- <button class="button" onclick="start_application()">
				<?= lang('oca_landing_button1')  ?>
		</button> -->
		
		<div class="image-container">
			<div class="image-inner-container">
			<!-- First row of images -->
			<div class="image-row"> 
				<div class="image-column-left">
					<?php if($store == 'desktop'){ ?>
						<img class="image" src="/ci_media/images/credit_app/desktop_landing_1.png" />
					<?php }else{ ?>
						<img class="image" src="/ci_media/images/credit_app/landing_1.png" />
					<?php } ?>
				</div>
				<div class="image-column-right">
					<?php if($store == 'desktop'){ ?>
						<img class="image-title" src="/ci_media/images/credit_app/desktop_landing_2.png" />
					<?php }else{ ?>
						<img class="image-title" src="/ci_media/images/credit_app/landing_2.png" />
					<?php } ?>
					<div class="image-text-title"><?= lang('oca_landing_row1_title')  ?></div>
					<div class="image-text-content"><?=  lang('oca_landing_row1_text') ?></div>
				</div>
			</div>
			<!-- Second row of images -->
			<div class="image-row"> 
				<div class="image-column-right">
					<?php if($store == 'desktop'){ ?>
						<img class="image-title" src="/ci_media/images/credit_app/desktop_landing_3.png" />
					<?php }else{ ?>
						<img class="image-title" src="/ci_media/images/credit_app/landing_3.png" />
					<?php } ?>
					<div class="image-text-title"><?= lang('oca_landing_row2_title')  ?></div>
					<div class="image-text-content"><?=  lang('oca_landing_row2_text') ?></div>
				</div>
				<div class="image-column-left">
					<?php if($store == 'desktop'){ ?>
						<img class="image" src="/ci_media/images/credit_app/desktop_landing_4.png" />
					<?php }else{ ?>
						<img class="image" src="/ci_media/images/credit_app/landing_4.png" />
					<?php } ?>	
				</div>
			</div>
			<!-- Third row of images -->
			<div class="image-row"> 
				<div class="image-column-left">
					<?php if($store == 'desktop'){ ?>
						<img class="image" src="/ci_media/images/credit_app/desktop_landing_5.png" />
					<?php }else{ ?>
						<img class="image" src="/ci_media/images/credit_app/landing_5.png" />
					<?php } ?>
				</div>
				<div class="image-column-right">
					<?php if($store == 'desktop'){ ?>
						<img class="image-title" src="/ci_media/images/credit_app/desktop_landing_6.png" />
					<?php }else{ ?>
						<img class="image-title" src="/ci_media/images/credit_app/landing_6.png" />
					<?php } ?>
					<div class="image-text-title"><?=  lang('oca_landing_row3_title') ?></div>
					<div class="image-text-content"><?=  lang('oca_landing_row3_text') ?></div>
				</div>
			</div>
			</div>
		</div>
		
		<div id="terms">
			<div id="terms-center">
				<p id="terms-text"><?= lang('oca_view_terms') ?> <a href="<?= lang('oca_terms_ca') ?>" target="_blank">California</a>, <a href="<?= lang('oca_terms_az') ?>" target="_blank">Arizona</a> or <a href="<?= lang('oca_terms_nv') ?>" target="_blank">Nevada</a></p>
			</div>
		</div> 
		
		<button class="landing-button button" onclick="start_application()">
				<?= lang('oca_landing_button1')  ?>
		</button>
	</div>
</div>
	
	<div id="landing-footer" class="page-footer">
		<div id="landing-phone" class="footer-phone">
			<img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
			<a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
		</div>
	</div>


