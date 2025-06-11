<?php
   // @php block ถูกแปลงเป็น <?php ... ?>
   $footer        = get_content("content_footer")->first();
   $footerbg      = $footer->file->where("type",'footer_background')->first();
   $footerbgSize  = get_appearance_img_size('footer','content','footer_background');
   $paymentImg      = $footer->file->where("type",'payment_image')->first();
   $paymentImgSize  = get_appearance_img_size('footer','content','payment_image');
   $icons         = get_content("element_social_icon");
   $buttons       = get_content("element_footer");
   $blogs         = get_feature_blogs()->take(2);
   $services      = get_content("element_service")->take(4);
?>

<footer>
  <div class="container">
    <div class="footer-top pt-110 pb-110">
        <div class="row justify-content-center">
          <div class="col-lg-9">
              <div class="footer-top-content" data-aos="fade-up" data-aos-duration="1000">
                   <img src="<?php echo imageURL($footerbg,'frontend',true,$footerbgSize); ?>" alt="<?php echo @$footerbg->name ?? 'footer-bg.jpg'; ?>" class="footer-top-img">
                    <h2>
                       <?php echo @$footer->value->title; ?>
                    </h2>
                    <p> <?php echo @$footer->value->description; ?> </p>


                    <?php if( $buttons->count() > 0) : // @if ถูกแปลงเป็น <?php if : ?> ?>
                      <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <?php foreach ($buttons as $button) : // @foreach ถูกแปลงเป็น <?php foreach : ?> ?>
                                  <a href="<?php echo @$button->value->button_URL; ?>" class="i-btn btn--lg btn--primary capsuled">
                                    <?php echo @$button->value->button_name; ?>
                                    <span>
                                        <i class=" <?php echo @$button->value->button_icon; ?>"></i>
                                    </span>
                                  </a>
                            <?php endforeach; // @endforeach ถูกแปลงเป็น <?php endforeach; ?> ?>
                      </div>
                    <?php endif; // @endif ถูกแปลงเป็น <?php endif; ?> ?>
              </div>
          </div>
        </div>
    </div>
  </div>

  <div class="container-fluid px-lg-0 px-md-4">
    <div class="news-letter-area">
        <div class="newsletter-wrapper">
          <form  action="<?php echo route('subscribe'); ?>" method="post">
             <?php
                // CSRF token generation for CI3
                // This assumes you have a helper or a custom function to generate it
                // If you use CI3's security class, it might be $this->security->get_csrf_token_name()
                // and $this->security->get_csrf_hash()
                if (function_exists('csrf_field')) {
                    echo csrf_field(); // If you have a custom helper for Laravel-like csrf_field()
                } else {
                    // Fallback for standard CI3 CSRF protection
                    // Make sure CI_URI->config->item('csrf_protection') is true in config.php
                    // And $this->input->post($this->security->get_csrf_token_name()) is checked in controller
                    $ci =& get_instance(); // Get CI instance
                    if ($ci->config->item('csrf_protection') === TRUE) {
                        echo '<input type="hidden" name="' . $ci->security->get_csrf_token_name() . '" value="' . $ci->security->get_csrf_hash() . '">';
                    }
                }
             ?>
              <input name="email" type="email" placeholder="<?php echo translate('Enter your email'); ?>">
              <button class="i-btn btn--lg btn--primary capsuled">
                   <?php echo translate("SUBSCRIBE"); ?>
                  <span><i class="bi bi-arrow-up-right"></i></span>
              </button>
          </form>
        </div>
    </div>
  </div>

  <div class="container">
      <div class="footer-bottom">
        <div class="row gy-5">
          <?php if($menus->count() > 0) : ?>
              <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                  <h4 class="footer-title">
                     <?php echo translate('Quick link'); ?>
                  </h4>
                  <ul class="footer-list">
                     <?php foreach ($menus as $menu) : ?>
                            <li>
                                <a href="<?php echo url($menu->url); ?>">
                                    <?php echo $menu->name; ?>
                                </a>
                            </li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          <?php endif; ?>

          <?php if($pages->count() > 0) : ?>
              <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                  <h4 class="footer-title">
                      <?php echo translate("Information"); ?>
                  </h4>
                  <ul class="footer-list">
                      <?php foreach ($pages as $page) : ?>
                          <li>
                              <a href="<?php echo route('page',$page->slug); ?>">
                                <?php echo $page->title; ?>
                              </a>
                          </li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          <?php endif; ?>

          <?php if($services->count() > 0) : ?>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                  <h4 class="footer-title">Services</h4>
                  <ul class="footer-list">
                      <?php foreach($services as $service) : ?>
                         <li><a href="<?php echo route('service',['slug' => make_slug($service->value->title) ,'uid'=> $service->uid  ]); ?>"> <?php echo limit_words($service->value->title,25); ?></a></li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          <?php endif; ?>

           <?php if($blogs->count() > 0) : ?>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                  <h4 class="footer-title">
                     <?php echo translate("Blogs"); ?>
                  </h4>
                  <ul class="footer-list">
                       <?php foreach ($blogs as $blog) : ?>
                          <li>
                            <a href="<?php echo route('blog.details',$blog->slug); ?>"><?php echo limit_words($blog->title,28); ?></a>
                            <span>
                                <?php echo get_date_time($blog->created_at,"F j, Y"); ?>
                            </span>
                        </li>
                       <?php endforeach; ?>
                  </ul>
              </div>
           <?php endif; ?>

        </div>
      </div>

      <div class="copyright-area d-flex justify-content-lg-between justify-content-center align-items-center flex-wrap gap-4">

           <?php if($icons->count() > 0) : ?>
              <div class="footer-social">
                  <ul>
                       <?php foreach ($icons as $icon) : ?>
                            <li><a target="_blank" href="<?php echo $icon->value->button_url; ?>"><i class="<?php echo $icon->value->icon; ?>"></i></a></li>
                       <?php endforeach; ?>

                  </ul>
              </div>
            <?php endif; ?>

            <div class="payment-image">
                <img src="<?php echo imageURL($paymentImg ,'frontend',true,$paymentImgSize); ?>" alt="<?php echo @$paymentImg->name ?? 'payment.jpg'; ?>">
            </div>


            <div class="copyright">
               <p class="mb-0 text-white opacity-75 fs-14 lh-1"><?php echo site_settings("copy_right_text"); ?></p>
            </div>
      </div>
  </div>
</footer>
