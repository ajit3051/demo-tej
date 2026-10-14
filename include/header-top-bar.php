<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$currentUsername = $_SESSION['login_user'] ?? 'Admin';
?>
<style>
/* MODERN TOP BAR STYLING */
.main-header.tej-top-navbar {
    position: relative;
    max-height: 56px;
    z-index: 1030;
    margin-bottom:10px;
}

/* Ensure sidebar starts below top bar */
.main-sidebar {
    padding-top: 56px !important;
}

.tej-top-navbar .navbar {
    background-color: #1e293b !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    min-height: 56px !important;
    margin-left: 250px !important;
    transition: margin-left 0.3s ease-in-out;
}

body.sidebar-collapse .tej-top-navbar .navbar {
    margin-left: 50px !important;
}

.tej-top-navbar .logo {
    background-color: #0f172a !important;
    color: #ffffff !important;
    height: 56px !important;
    line-height: 56px !important;
    font-weight: 700 !important;
    font-size: 18px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    width: 250px !important;
    float: left !important;
    transition: width 0.3s ease-in-out;
}

body.sidebar-collapse .tej-top-navbar .logo {
    width: 50px !important;
}

body.sidebar-collapse .tej-top-navbar .logo .logo-lg {
    display: none !important;
}

body.sidebar-collapse .tej-top-navbar .logo .logo-mini {
    display: block !important;
}

.tej-top-navbar .logo .logo-mini {
    display: none;
    font-size: 16px;
    color: #009688;
}

.tej-top-navbar .logo .logo-lg {
    color: #ffffff !important;
    font-size: 18px !important;
    letter-spacing: 0.5px;
}

.tej-top-navbar .logo .logo-lg span {
    color: #009688;
}

.tej-top-navbar .sidebar-toggle {
    color: #cbd5e1 !important;
    padding: 16px 18px !important;
    font-size: 18px !important;
    line-height: 24px !important;
    float: left !important;
    text-decoration: none !important;
    transition: color 0.2s ease;
}

.tej-top-navbar .sidebar-toggle:hover {
    color: #38bdf8 !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
}

.tej-profile-dropdown {
    position: relative;
    list-style: none;
    margin: 0;
    padding: 0;
}

.tej-profile-toggle {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 10px 18px !important;
    color: #f1f5f9 !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    border-radius: 6px;
    height: 56px;
    box-sizing: border-box;
    transition: background-color 0.2s ease;
}

.tej-profile-toggle:hover,
.tej-profile-toggle:focus,
.tej-profile-dropdown.open .tej-profile-toggle {
    background-color: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

.tej-avatar-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #009688, #0284c7);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    border: 2px solid rgba(255,255,255,0.2);
}

.tej-profile-menu {
    border-radius: 8px !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3) !important;
    border: 1px solid #334155 !important;
    background-color: #1e293b !important;
    padding: 6px 0 !important;
    min-width: 200px !important;
    margin-top: 2px !important;
    right: 15px !important;
    left: auto !important;
}

.tej-profile-menu-header {
    padding: 12px 16px;
    border-bottom: 1px solid #334155;
    margin-bottom: 4px;
}

.tej-profile-menu-header .name {
    font-size: 14px;
    font-weight: 700;
    color: #f8fafc;
}

.tej-profile-menu-header .role {
    font-size: 11.5px;
    color: #94a3b8;
    margin-top: 2px;
}

.tej-profile-menu > li > a {
    color: #cbd5e1 !important;
    padding: 9px 16px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
}

.tej-profile-menu > li > a:hover {
    background-color: #334155 !important;
    color: #ffffff !important;
}

.tej-profile-menu > li > a .fa {
    font-size: 15px;
    width: 18px;
    text-align: center;
}

.tej-profile-menu .divider {
    height: 1px;
    margin: 4px 0;
    overflow: hidden;
    background-color: #334155;
}
</style>

<header class="main-header tej-top-navbar">
   <!-- Logo -->
   <a href="fh_dashboard.php" class="logo">
      <span class="logo-mini"><b>T</b>ERP</span>
      <span class="logo-lg"><b>TEJAS</b><span>ERP</span></span>
   </a>
   
   <!-- Header Navbar -->
   <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" title="Toggle Sidebar">
         <span class="sr-only">Toggle navigation</span>
         <i class="fa fa-bars"></i>
      </a>

      <div class="navbar-custom-menu">
         <ul class="nav navbar-nav">
            <li class="dropdown dropdown-user tej-profile-dropdown">
               <a href="#" class="dropdown-toggle tej-profile-toggle" data-toggle="dropdown" aria-expanded="false">
                  <div class="tej-avatar-circle">
                     <i class="fa fa-user"></i>
                  </div>
                  <span><?php echo htmlspecialchars($currentUsername); ?></span>
                  <i class="fa fa-angle-down" style="margin-left: 2px; font-size: 12px; color: #94a3b8;"></i>
               </a>
               <ul class="dropdown-menu tej-profile-menu dropdown-menu-right">
                  <li class="tej-profile-menu-header">
                     <div class="name"><?php echo htmlspecialchars($currentUsername); ?></div>
                     <div class="role">Administrator</div>
                  </li>
                  <li>
                     <a href="profile.php">
                        <i class="fa fa-user-circle" style="color: #38bdf8;"></i>
                        <span>Profile</span>
                     </a>
                  </li>
                  <li class="divider"></li>
                  <li>
                     <a href="logout.php">
                        <i class="fa fa-sign-out" style="color: #f87171;"></i>
                        <span>Logout</span>
                     </a>
                  </li>
               </ul>
            </li>
         </ul>
      </div>
   </nav>
</header>
