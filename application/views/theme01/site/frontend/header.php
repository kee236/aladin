<?php
    // ปิดการใช้งานสกุลเงิน - ไม่จำเป็นต้อง query หรือประมวลผล currencies
    // $currencies = site_currencies()->where("code",'!=',session()->get('currency')->code);

    $lastSegment = collect(request()->segments())->last();

    // กำหนดรายการภาษาที่อนุญาตให้แสดง (ใช้รหัส ISO 639-1)
    $allowed_language_codes = ['th', 'en', 'ms', 'lo', 'km', 'id', 'ph'];

    // ดึง locale ปัจจุบันจาก session
    // สมมติว่า session()->get('locale') สามารถดึงค่าจาก CI3 Session ได้
    // หากไม่ใช่ อาจจะต้องเปลี่ยนเป็น $this->session->userdata('locale')
    $current_locale = session()->get('locale') ?? 'en';

    // ค้นหาข้อมูลของภาษาปัจจุบันจาก $languages Collection
    $current_lang_data = null;
    foreach ($languages as $lang_item) {
        if ($lang_item->code == $current_locale) {
            $current_lang_data = $lang_item;
            break;
        }
    }

    // กำหนดรหัสภาษาและชื่อภาษาปัจจุบันเพื่อแสดงผล
    $current_lang_code = $current_lang_data ? $current_lang_data->code : "en";
    // สมมติว่า object $lang_item มี property 'name' สำหรับชื่อเต็มของภาษา
    $current_lang_name = $current_lang_data ? (isset($current_lang_data->name) ? $current_lang_data->name : strtoupper($current_lang_code)) : "English";

    // กรองภาษาที่จะแสดงใน dropdown
    $display_languages = [];
    foreach ($languages as $language_item) {
        // ตรวจสอบสถานะ: สมมติว่า App\Enums\StatusEnum::true->status() ใช้ได้ใน CI3
        $is_active = isset($language_item->status) && $language_item->status == App\Enums\StatusEnum::true->status();
        // ตรวจสอบว่าอยู่ในรายการที่อนุญาต
        $is_allowed = in_array($language_item->code, $allowed_language_codes);
        // ตรวจสอบว่าไม่ใช่ภาษาปัจจุบัน (เพื่อไม่ให้แสดงซ้ำใน dropdown)
        $is_not_current = $language_item->code != $current_lang_code;

        if ($is_active && $is_allowed && $is_not_current) {
            $display_languages[] = $language_item;
        }
    }

    // แปลง $display_languages ให้เป็น Collection เพื่อให้ใช้ count() ได้เหมือนเดิม (ถ้าจำเป็น)
    $display_languages = collect($display_languages);

?>
<header class="header">

    <div class="header-container">
        <div class="d-flex align-items-center gap-3">
            <div class="header-logo d-md-block d-none">
                <a href="<?php echo route('home'); ?>">
                    <img src="<?php echo imageUrl(@site_logo('user_site_logo')->file,'user_site_logo',true); ?>"
                        alt="<?php echo @site_logo('user_site_logo')->file->name ?? 'site-logo.jpg'; ?>">
                </a>
            </div>
        </div>

        <div class="sidebar">
            <div class="sidebar-body">
                <div class="mobile-logo-area d-lg-none mb-4">
                    <div class="mobile-logo-wrap">
                        <a href="<?php echo route('home'); ?>">

                            <img src="<?php echo imageUrl(@site_logo('user_site_logo')->file,'user_site_logo',true); ?>"
                                alt="<?php echo @site_logo('user_site_logo')->file->name; ?>">

                        </a>
                    </div>

                    <div class="closer-sidebar">
                        <i class="bi bi-x-lg "></i>
                    </div>
                </div>

                <div class="sidebar-wrapper">
                    <nav>
                        <ul class="menu-list">
                            <?php foreach ($menus as $menu) : ?>
                                <li class="menu-item">
                                    <a href="<?php echo url($menu->url); ?>"
                                        class="menu-link <?php echo (!request()->routeIs('home') && URL::current() == url($menu->url)) ? 'active' : ''; ?> ">
                                        <?php echo $menu->name; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                            <?php
                                    $megaMenu              = get_content("content_mega_menu")->first();
                                    $intregrationsContent  = get_content("content_integration")->first();
                                    $intregrationsElements = get_content("element_integration");
                                    $hoverImageSize        = get_appearance_img_size('integration','element','hover_image');
                                    $featureImageSize      = get_appearance_img_size('integration','element','feature_image');


                            ?>

                            <?php if($megaMenu->value->select_input->status == App\Enums\StatusEnum::true->status() ) : ?>
                                <li class="menu-item">
                                    <a href="javascript:void(0)" class="menu-link mega-menu-click">
                                        <?php echo @$megaMenu->value->title; ?>
                                        <div class="menu-link-icon">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </a>

                                    <div class="mega-menu container-lg px-0">
                                        <div class="mega-menu-wrapper">
                                            <div class="row g-4 h-100">
                                                <div class="col-lg-12">
                                                    <div class="mega-menu-right">
                                                        <div class="row g-0 h-100 align-items-center">
                                                            <div class="col-lg-8">
                                                                <div class="social-integra">
                                                                    <h5>
                                                                        <?php echo @$intregrationsContent->value->title; ?>
                                                                    </h5>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <?php if($intregrationsElements->count() > 0) : ?>
                                                                                <div class="mega-menu-integra">
                                                                                    <ul class="nav nav-tabs gap-xxl-3 gap-2 border-0" id="customTab" role="tablist">

                                                                                        <?php foreach ($intregrationsElements as $index => $element) : ?>

                                                                                            <?php $file = $element->file->where('type',"feature_image")->first(); ?>

                                                                                            <li class="nav-item" role="presentation">
                                                                                                <a href="<?php echo route('integration',['slug' =>  make_slug($element->value->title) , 'uid' => $element->uid]); ?>" class="nav-link mega-menu-tab <?php echo ($index == 0) ? 'active' : ''; ?> menu-social-item"
                                                                                                    id="tab-<?php echo $index; ?>-tab"
                                                                                                    data-bs-toggle="tab"
                                                                                                    data-bs-target="#tab-<?php echo $index; ?>"
                                                                                                    role="tab"
                                                                                                    aria-controls="tab-<?php echo $index; ?>"
                                                                                                    aria-selected="true">
                                                                                                    <div class="social-item-img">
                                                                                                        <img src="<?php echo imageURL($file,'frontend',true,$featureImageSize); ?>"
                                                                                                            alt="<?php echo @$file->name ?? @$element->value->title.'jpg'; ?>"
                                                                                                            loading="lazy">
                                                                                                    </div>

                                                                                                    <div class="content">
                                                                                                        <h6 class="mb-1">
                                                                                                            <?php echo $element->value->title; ?>
                                                                                                        </h6>
                                                                                                        <p>
                                                                                                            <?php echo $element->value->short_description; ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </a>
                                                                                            </li>

                                                                                        <?php endforeach; ?>
                                                                                        <?php if(count($intregrationsElements) == 0) : ?>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <?php $this->load->view("frontend/partials/not_found"); ?>
                                                                                            </li>
                                                                                        <?php endif; ?>
                                                                                    </ul>
                                                                                </div>
                                                                            <?php else : ?>
                                                                               <?php $this->load->view("frontend/partials/not_found"); ?>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 p-3">
                                                                <?php if($intregrationsElements->count() > 0) : ?>
                                                                    <div class="tab-content" id="customTabContent">
                                                                        <?php foreach ($intregrationsElements as $index => $element) : ?>
                                                                            <?php
                                                                                $file = $element->file->where('type',"hover_image")->first();
                                                                            ?>
                                                                            <div class="tab-pane fade <?php echo ($index == 0) ? 'show active' : ''; ?> " id="tab-<?php echo $index; ?>"
                                                                                role="tabpanel" aria-labelledby="tab-<?php echo $index; ?>-tab">
                                                                                <img src="<?php echo imageURL($file,'frontend',true,$hoverImageSize); ?>"
                                                                                alt="<?php echo @$file->name?? 'preview.jpg';?>" class="rounded-3">
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                <?php else : ?>
                                                                    <?php $this->load->view("frontend/partials/not_found"); ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php foreach ($pages as $page) : ?>
                                <li class="menu-item">
                                    <a href="<?php echo route('page',$page->slug); ?>"
                                        class="menu-link <?php echo ($lastSegment == $page->slug) ? 'active' : ''; ?> ">
                                        <?php echo $page->title; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>

                    <div class="sidebar-action d-lg-none">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <a href='<?php echo route("plan"); ?>' class="i-btn btn--primary-outline btn--lg capsuled">
                                <?php echo translate("Get Started"); ?>
                            </a>

                            <?php if(!auth_user('web')) : ?>
                                <a href='<?php echo route("auth.login"); ?>' class="i-btn btn--secondary btn--lg capsuled">
                                    <?php echo translate('Login'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar-overlay"></div>
        </div>

        <div class="nav-right d-flex jsutify-content-end align-items-center gap-3">
            <div class="d-lg-none">
                <div class="mobile-menu-btn sidebar-trigger">
                    <i class="bi bi-list"></i>
                </div>
            </div>

            <div class="language">
                <button class="dropdown-toggle lang--toggle" type="button"  <?php echo ($display_languages->count() > 0) ? 'data-bs-toggle="dropdown" aria-expanded="false"' : ''; ?>>
                    {{-- แสดงธงและชื่อเต็มภาษาปัจจุบัน --}}
                    <img src="<?php echo asset('assets/images/global/flags/'.strtoupper($current_lang_code ).'.png'); ?>" alt="<?php echo $current_lang_code.'.jpg'; ?>">
                    <span class="lang-name d-none d-md-inline-block"><?php echo $current_lang_name; ?></span>
                </button>

                <?php if($display_languages->count() > 0) : ?>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach($display_languages as $language) : ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo route('language.change',$language->code); ?>">
                                    <img src="<?php echo asset('assets/images/global/flags/'.strtoupper($language->code ).'.png'); ?>" alt="<?php echo $language->code.'jpg'; ?>">
                                    <?php
                                        // แสดงชื่อเต็มภาษา ถ้ามี property 'name'
                                        echo isset($language->name) ? $language->name : strtoupper($language->code);
                                    ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php /* ส่วนของ Currency ถูกซ่อนไว้
            <div class="currency">
                <button class=" <?php echo (isset($currencies) && $currencies->count() > 0) ? 'dropdown-toggle' : ''; ?>  custom--toggle" type="button" <?php echo (isset($currencies) && $currencies->count() > 0) ? 'data-bs-toggle="dropdown" aria-expanded="false"' : ''; ?>>
                    <?php echo session()->get('currency')?->code; ?>
                </button>

                <?php if(isset($currencies) && $currencies->count() > 0) : ?>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach($currencies as $currency) : ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo route('currency.change',$currency->code); ?>">
                                        <?php echo $currency->code; ?></a>
                                </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            */ ?>

            <?php if(auth_user('web')) : ?>
                <div class="dropdown profile-dropdown">
                    <div class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true" role="button">
                        <i class="bi bi-three-dots-vertical"></i>
                    </div>

                    <div class="dropdown-menu dropdown-menu-end">
                        <ul>

                            <li class="dropdown-menu-title">
                                <h6>
                                    <?php echo translate('Welcome'); ?>,
                                    <span class="user-name">
                                        <?php echo auth_user('web')->name; ?>
                                    </span>
                                </h6>
                            </li>

                            <li>
                                <a href="<?php echo route('user.home'); ?>" class="dropdown-item">
                                    <i class="bi bi-house"></i> <?php echo translate('Dashboard'); ?>
                                </a>
                            </li>

                            <li class="dropdown-menu-footer p-0">
                                <a href="<?php echo route('user.logout'); ?>">
                                    <i class="bi bi-box-arrow-left"></i> <?php echo translate('Logout'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
      