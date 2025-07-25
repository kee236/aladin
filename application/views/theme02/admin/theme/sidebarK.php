<?php
/**
 * sidebar.php
 *
 * This file is responsible for rendering the main sidebar navigation.
 * It dynamically generates menu items based on user permissions and system configuration.
 */

// Get current user type from session
$userType = $this->session->userdata('user_type');
$isManager = (bool) $this->is_manager; // Convert to boolean for clearer checks
$isDemo = (bool) $this->is_demo; // Convert to boolean for clearer checks
$licenseType = $this->session->userdata('license_type');

/**
 * Helper function to determine if an addon exists.
 * Assumes 'addon_exist' is a global helper function.
 * @param int $moduleId
 * @param string $addonUniqueName
 * @return bool
 */
if (!function_exists('is_addon_active')) {
    function is_addon_active($moduleId, $addonUniqueName) {
        // Placeholder for actual addon_exist logic.
        // In a real scenario, this should call the actual helper.
        return addon_exist($moduleId, $addonUniqueName);
    }
}

/**
 * Helper function to determine if a menu item should be hidden based on module access.
 * @param array $moduleAccess
 * @param string $currentUserType
 * @param array $userModuleAccess
 * @return bool
 */
function shouldHideMenuByModuleAccess(array $moduleAccess, string $currentUserType, array $userModuleAccess): bool {
    if ($currentUserType !== 'Admin' && !empty($moduleAccess)) {
        return count(array_intersect($userModuleAccess, $moduleAccess)) === 0;
    }
    return false;
}

/**
 * Helper function to generate CSS for menu icon color based on URL or provided color.
 * @param string $url
 * @param string|null $color
 * @return string
 */
function getMenuIconColorCss(string $url, ?string $color): string {
    if (empty($color)) {
        if (str_ends_with($url, '=ig') || str_ends_with($url, '/ig')) {
            return 'background: -webkit-linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D, #F56040, #F77737,#FCAF45, #FFDC80);-webkit-background-clip: text;-webkit-text-fill-color: transparent;';
        } elseif (str_ends_with($url, '=fb') || str_ends_with($url, '/fb')) {
            return 'background: -webkit-linear-gradient(45deg, #1877f2, #1877f2, #0a63bf, #2A4480, #1F3D73);-webkit-background-clip: text;-webkit-text-fill-color: transparent;';
        }
    } else {
        // Assuming adjustBrightness is a defined helper function
        if (function_exists('adjustBrightness')) {
            return "background: -webkit-linear-gradient(45deg,".adjustBrightness($color,-0.85).",".adjustBrightness($color,-0.65).",".adjustBrightness($color,-0.45).",".adjustBrightness($color,-0.25).",".$color.");-webkit-background-clip: text;-webkit-text-fill-color: transparent;";
        } else {
            return "color: {$color};"; // Fallback to solid color if adjustBrightness not found
        }
    }
    return ''; // Default empty style
}

// Initialize array to store all valid menu links for JS
$allValidLinks = [];
?>

<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?php echo site_url(); ?>">
        <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt='<?php echo html_escape($this->config->item("product_short_name")); ?>'>
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?php echo site_url(); ?>">
        <img src="<?php echo base_url('assets/img/favicon.png'); ?>" alt='<?php echo html_escape($this->config->item("product_short_name")); ?>'>
      </a>
    </div>
    <ul class="sidebar-menu">
      <?php
        foreach ($menus as $singleMenu) {
            $menuHtml = '';
            $onlyAdmin = (bool) $singleMenu['only_admin'];
            $onlyMember = (bool) $singleMenu['only_member'];
            $moduleAccess = array_filter(explode(',', $singleMenu['module_access']));
            $menuColor = $singleMenu['color'] ?? 'var(--blue)';

            // Display header text if available
            if ($singleMenu['header_text'] !== '') {
                echo '<li class="menu-header">' . html_escape($this->lang->line($singleMenu['header_text'])) . '</li>';
            }

            // Skip conditions (simplified)
            if (($singleMenu['url'] === 'social_apps/index' && $onlyMember && $this->config->item('backup_mode') === '0' && $userType === 'Member')) {
                continue;
            }
            if (($singleMenu['module_access'] === '325' && $isManager) || ($singleMenu['url'] === 'integration' && $isManager)) {
                continue;
            }
            if (($singleMenu['module_access'] === '278,279' || $singleMenu['module_access'] === '296') && ($this->config->item('instagram_reply_enable_disable') === '0' || $this->config->item('instagram_reply_enable_disable') === '')) {
                continue;
            }
            if (!is_addon_active(315, "visual_flow_builder") && $singleMenu['module_access'] === '315') {
                continue;
            }

            $extraText = ($singleMenu['add_ons_id'] !== '0' && $isDemo) ? ' <label class="label label-warning" style="font-size:9px;padding:4px 3px;">Addon</label>' : '';

            $dropdownClass1 = $singleMenu['have_child'] ? "nav-item dropdown" : "";
            $dropdownClass2 = $singleMenu['have_child'] ? "has-dropdown" : "";

            $linkBaseUrl = $singleMenu['is_external'] ? "" : site_url();
            $targetAttr = $singleMenu['is_external'] ? " target='_BLANK'" : "";

            $iconColorCss = getMenuIconColorCss($singleMenu['url'], $menuColor);

            // Check if current menu should be displayed based on user type and module access
            $shouldDisplayParentMenu = false;
            if ($onlyAdmin && $userType === 'Admin' && !$isManager) {
                $shouldDisplayParentMenu = true;
            } elseif ($onlyMember && $userType === 'Member') {
                $shouldDisplayParentMenu = true;
            } elseif (!$onlyAdmin && !$onlyMember && ($userType === 'Admin' || empty($moduleAccess) || count(array_intersect($this->module_access, $moduleAccess)) > 0)) {
                $shouldDisplayParentMenu = true;
            }

            if (!$shouldDisplayParentMenu) {
                continue; // Skip rendering this parent menu if not applicable
            }

            $parentUrl = $linkBaseUrl . $singleMenu['url'];
            $menuHtml .= "<li class='{$dropdownClass1}'>";
            $menuHtml .= "<a {$targetAttr} href='{$parentUrl}' class='nav-link {$dropdownClass2}'>";
            $menuHtml .= "<i class='{$singleMenu['icon']}' style='{$iconColorCss}'></i> ";
            $menuHtml .= "<span>" . html_escape($this->lang->line($singleMenu['name'])) . $extraText . "</span>";
            $menuHtml .= "</a>";

            array_push($allValidLinks, $parentUrl);

            // Handle first level child menus
            if (isset($menu_child_1_map[$singleMenu['id']]) && count($menu_child_1_map[$singleMenu['id']]) > 0) {
                $menuHtml .= '<ul class="dropdown-menu">';
                foreach ($menu_child_1_map[$singleMenu['id']] as $singleChildMenu) {
                    $onlyAdmin2 = (bool) $singleChildMenu['only_admin'];
                    $onlyMember2 = (bool) $singleChildMenu['only_member'];
                    $childMenuColor = $singleChildMenu['color'] ?? $menuColor; // Inherit parent color if not set

                    // Skip conditions for child menus
                    if (($userType === 'Admin' && $licenseType !== 'double' && in_array($singleChildMenu['url'], ['admin/activity_log','payment/accounts','payment/earning_summary','payment/transaction_log','blog/posts']))) { // Using the specific array directly
                        continue;
                    }
                    if (($onlyAdmin2 && $userType === 'Member') || ($onlyMember2 && $userType === 'Admin')) {
                        continue;
                    }
                    if ($onlyAdmin2 && $isManager) {
                        continue;
                    }

                    $childLinkBaseUrl = $singleChildMenu['is_external'] ? "" : site_url();
                    $childTargetAttr = $singleChildMenu['is_external'] ? " target='_BLANK'" : "";
                    $childHref = $singleChildMenu['have_child'] ? '#' : $childLinkBaseUrl . $singleChildMenu['url'];

                    $moduleAccess2 = array_filter(explode(',', $singleChildMenu['module_access']));
                    $hideSecondMenuClass = shouldHideMenuByModuleAccess($moduleAccess2, $userType, $this->module_access) ? 'hidden' : '';

                    $menuHtml .= "<li class='{$hideSecondMenuClass}'>";
                    $menuHtml .= "<a {$childTargetAttr} href='{$childHref}' class='nav-link'>";
                    $menuHtml .= "<i style='color:{$childMenuColor}' class='{$singleChildMenu['icon']}'></i>";
                    $menuHtml .= html_escape($this->lang->line($singleChildMenu['name']));
                    $menuHtml .= "</a>";

                    if (!$singleChildMenu['have_child']) { // Only add to allValidLinks if it's a direct clickable link
                         array_push($allValidLinks, $childLinkBaseUrl . $singleChildMenu['url']);
                    }


                    // Handle second level child menus
                    if (isset($menu_child_2_map[$singleChildMenu['id']]) && count($menu_child_2_map[$singleChildMenu['id']]) > 0) {
                        $menuHtml .= "<ul class='dropdown-menu2'>";
                        foreach ($menu_child_2_map[$singleChildMenu['id']] as $singleChildMenu2) {
                            $onlyAdmin3 = (bool) $singleChildMenu2['only_admin'];
                            $onlyMember3 = (bool) $singleChildMenu2['only_member'];

                            // Skip conditions for second child menus
                            if (($onlyAdmin3 && $userType === 'Member') || ($onlyMember3 && $userType === 'Admin')) {
                                continue;
                            }
                            if ($onlyAdmin3 && $isManager) {
                                continue;
                            }

                            $child2LinkBaseUrl = $singleChildMenu2['is_external'] ? "" : site_url();
                            $child2TargetAttr = $singleChildMenu2['is_external'] ? " target='_BLANK'" : "";

                            $child2Url = $child2LinkBaseUrl . $singleChildMenu2['url'];
                            $menuHtml .= "<li>";
                            $menuHtml .= "<a {$child2TargetAttr} href='{$child2Url}' class='nav-link'>";
                            $menuHtml .= "<i class='{$singleChildMenu2['icon']}'></i> ";
                            $menuHtml .= html_escape($this->lang->line($singleChildMenu2['name']));
                            $menuHtml .= "</a>";
                            $menuHtml .= "</li>";

                            array_push($allValidLinks, $child2Url);
                        }
                        $menuHtml .= "</ul>";
                    }
                    $menuHtml .= "</li>";
                }
                $menuHtml .= "</ul>";
            }

            $menuHtml .= "</li>";
            echo $menuHtml; // Echo the entire menu item if all checks pass
        }

        // Hardcoded Payment menu for Member with 'double' license type
        if ($licenseType === 'double' && $userType === 'Member' && !$isManager) {
            echo '
            <li class="menu-header">' . html_escape($this->lang->line("Payment")) . '</li>
            <li class="nav-item dropdown">
              <a href="#" class="nav-link has-dropdown" style="background: -webkit-linear-gradient(270deg,#ffa801,#5a3b01);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"><i class="fa fa-coins"></i> <span>' . html_escape($this->lang->line("Payment")) . '</span></a>
              <ul class="dropdown-menu">
                <li class=""><a href="' . site_url("payment/buy_package") . '" style="color:#ffa801" class="nav-link"><i class="fa fa-cart-plus"></i>' . html_escape($this->lang->line("Renew Package")) . '</a></li>
                <li class=""><a href="' . site_url("payment/transaction_log") . '" style="color:#ffa801" class="nav-link"><i class="fa fa-history"></i>' . html_escape($this->lang->line("Transaction Log")) . '</a></li>
                <li class=""><a href="' . site_url("payment/usage_history") . '" style="color:#ffa801" class="nav-link"><i class="fa fa-user-clock"></i>' . html_escape($this->lang->line("Usage Log")) . '</a></li>
              </ul>
            </li>';
            array_push($allValidLinks, site_url("payment/buy_package"), site_url("payment/transaction_log"), site_url("payment/usage_history"));
        }
      ?>
    </ul>

    <?php
    if ($licenseType === 'double' && $this->config->item('enable_support') === '1') {
        $supportMenuText = $this->lang->line("Support Desk");
        $supportIcon = "fa fa-headset";
        $supportUrl = site_url('simplesupport/tickets');

        echo '
        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
          <a href="' . html_escape($supportUrl) . '" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="' . html_escape($supportIcon) . '"></i> ' . html_escape($supportMenuText) . '
          </a>
        </div>';
        array_push($allValidLinks, $supportUrl);
    }
    ?>
  </aside>
</div>

<?php
// Remove duplicate links and 'base_url()#'
$allValidLinks = array_unique($allValidLinks);
$hashLinkKey = array_search(site_url().'#', $allValidLinks); // Use site_url() for consistency
if ($hashLinkKey !== FALSE) {
    unset($allValidLinks[$hashLinkKey]);
}

// Convert custom_links array to a more manageable format for JS, avoiding special character encoding if possible
// It's better to manage these custom links in a separate JS file or pass them as a JSON object if structure allows
// For now, I'll keep the PHP logic for building the JS object, but suggest a better approach.
$customLinksAssoc = [];
foreach ($custom_links as $key => $value) {
    // Ensure keys and values are clean URLs
    $customLinksAssoc[rtrim($key, '/')] = rtrim($value, '/');
}
?>


<script type="text/javascript">
  // All valid links generated by PHP, including parent and child links.
  // Using JSON.parse and JSON.stringify to handle PHP array to JS array conversion robustly.
  var allValidLinksJS = <?php echo json_encode(array_values($allValidLinks)); ?>;

  // Custom links associative array (child URL -> parent URL)
  var customLinksAssocJS = <?php echo json_encode($customLinksAssoc); ?>;

  var currentSidebarURL = window.location.href.split('#')[0].trim(); // Remove hash part if any

  // Function to clean and standardize URLs for comparison
  function cleanUrl(url) {
    if (!url) return '';
    // Remove trailing slash for consistent comparison, but keep for root URL
    return url === '<?php echo site_url(); ?>' ? url : url.replace(/\/$/, '');
  }

  // Clean the current URL for comparison
  currentSidebarURL = cleanUrl(currentSidebarURL);

  var activeURL = '';

  // 1. Check if the current URL matches any custom link directly (child URL)
  if (customLinksAssocJS[currentSidebarURL]) {
    activeURL = customLinksAssocJS[currentSidebarURL];
  }
  // 2. Check if the current URL matches any known valid link directly
  else if (allValidLinksJS.includes(currentSidebarURL)) {
    activeURL = currentSidebarURL;
  }
  // 3. If not found, try to find a parent by progressively trimming the URL
  else {
    var tempURL = currentSidebarURL;
    while (tempURL !== '<?php echo site_url(); ?>' && tempURL.length > '<?php echo site_url(); ?>'.length) {
      tempURL = tempURL.substring(0, tempURL.lastIndexOf('/'));
      if (tempURL === '') break; // Prevent infinite loop if URL becomes empty
      tempURL = cleanUrl(tempURL);

      if (customLinksAssocJS[tempURL]) {
        activeURL = customLinksAssocJS[tempURL];
        break;
      } else if (allValidLinksJS.includes(tempURL)) {
        activeURL = tempURL;
        break;
      }
    }
    // Fallback if no specific match, try the root if it's the only option
    if (activeURL === '' && currentSidebarURL === '<?php echo site_url(); ?>') {
        activeURL = currentSidebarURL;
    }
  }

  // Set 'active' class based on the determined activeURL
  if (activeURL) {
    $('ul.sidebar-menu a').filter(function() {
      // Clean href for comparison
      var href = cleanUrl($(this).attr('href'));
      return href === activeURL;
    }).parents('li.nav-item.dropdown').addClass('active'); // Activate parent dropdown
     $('ul.sidebar-menu a').filter(function() {
        var href = cleanUrl($(this).attr('href'));
        return href === activeURL;
    }).parent('li').addClass('active'); // Activate direct li if not a dropdown
  }

  $(document).ready(function() {
      // Refined logic for menu-header cleanup
      // This part ensures that if a menu-header has no visible siblings after it, it gets removed.
      // It also ensures that there are no consecutive menu-headers.
      // It's generally better to prevent these from being generated in PHP if possible.
      $('.sidebar-menu .menu-header').each(function() {
          var $this = $(this);
          var $nextVisibleSibling = $this.nextAll('li:not(.hidden)').first(); // Find first visible li sibling
          if ($nextVisibleSibling.length === 0 || $nextVisibleSibling.hasClass('menu-header')) {
              $this.remove(); // Remove if no visible siblings or next is another header
          }
      });
  });
</script>
