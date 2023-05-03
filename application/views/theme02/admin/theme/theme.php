<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="UTF-8">
	  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	  <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
	  <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png">
	  <?php
	  include(FCPATH.'application/views/theme02/include/css_include_back.php');
	  include(FCPATH.'application/views/theme02/include/js_include_back.php');
	  ?>
	</head>

	<?php
    $controller_name = $this->uri->segment(1);
    $function_name =$this->uri->segment(2);
    $is_mobile =  $this->session->userdata("is_mobile");
    $body_class = '';
    if(
        $is_mobile=='0' && (
          ($controller_name=="gmb" && ($function_name=="location_list" || $function_name=="")) ||
          ($controller_name=="ecommerce" && ($function_name=="store_list" || $function_name=="")) ||
          ($controller_name=="appointment_booking" && ($function_name=="dashboard" || $function_name=="")) ||
          ($controller_name=="comment_automation" && ($function_name=="index" || $function_name=="")) ||
          ($controller_name=="messenger_bot" && ($function_name=="bot_list")) ||
          ($controller_name=="subscriber_manager" && ($function_name=="bot_subscribers")) ||
          ($controller_name=="instagram_poster") ||
          ($controller_name=='message_manager') ||
          ($controller_name=='ultrapost' && ($function_name=="text_image_link_video_poster")) ||
          ($controller_name=='instagram_reply' && ($function_name=="get_account_lists")) ||
          ($controller_name=='calendar')
        )
    ) $body_class = 'sidebar-mini';
    	// $main_content_class = $this->uri->segment(1)=='dashboard' ? 'bg-white' : '';
    	$main_content_class = '';
    ?>
	
	<body class="g-sidenav-show  bg-gray-100">

		<?php
		include(FCPATH.'application/views/theme02/admin/theme/header.php');

		include(FCPATH.'application/views/theme02/admin/theme/sidebar.php');
		echo '<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">';
		include(FCPATH.'application/views/theme02/admin/theme/topbar.php');
			$this->load->view($body);
		echo '</main>';
		include(FCPATH.'application/views/theme02/admin/theme/footer.php'); ?>

	</body>
</html>
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">