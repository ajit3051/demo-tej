<?php include('top.php');?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$msg = "";
$is_edit = false;
$edit_id = "";
$edit_row = array(
    'item_code' => '',
    'barcode_no' => '',
    'sku_no' => '',
    'item_name' => '',
    'description' => '',
    'group_name' => '',
    'subgroup_name' => '',
    'brand_name' => '',
    'color_name' => '',
    'style_name' => '',
    'size_name' => '',
    'mou_name' => '',
    'purchase_price' => '',
    'dis_per' => '',
    'dis_amt' => '',
    'mrp' => '',
    'sp' => '',
    'min_qty' => '',
    'status' => 'true',
    'created_date' => '',
    'update_date' => '',
    'about_1' => '',
    'about_2' => '',
    'about_3' => '',
    'about_4' => ''
);

// FILE UPLOAD FUNCTION
function uploadMasterFile($file, $path) {
    if (isset($file) && !empty($file['name']) && $file['error'] == 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed)) {
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
            $filename = time() . "_" . rand(100,999) . "_" . $safeName;
            move_uploaded_file($file['tmp_name'], $path . $filename);
            return $filename;
        }
    }
    return "";
}

function uploadMasterFileArray($fileArray, $index, $path) {
    if (isset($fileArray['name'][$index]) && !empty($fileArray['name'][$index]) && $fileArray['error'][$index] == 0) {
        $ext = strtolower(pathinfo($fileArray['name'][$index], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed)) {
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($fileArray['name'][$index]));
            $filename = time() . "_" . rand(100,999) . "_" . $safeName;
            move_uploaded_file($fileArray['tmp_name'][$index], $path . $filename);
            return $filename;
        }
    }
    return "";
}

function saveItemImages($conn, $itemId, $itemCode, $files, $existingPics, $path, $timestamp) {
    $itemIdSafe = mysqli_real_escape_string($conn, $itemId);
    mysqli_query($conn, "DELETE FROM tbl_item_images WHERE item_id='$itemIdSafe'");

    $numUploaded = isset($files['item_pictures']['name']) && is_array($files['item_pictures']['name']) ? count($files['item_pictures']['name']) : 0;
    $numExisting = is_array($existingPics) ? count($existingPics) : 0;
    $totalCount = max($numUploaded, $numExisting);
    $totalCount = min(10, max(1, $totalCount));

    for ($i = 0; $i < $totalCount; $i++) {
        $existing = $existingPics[$i] ?? '';
        $newFile = uploadMasterFileArray($files['item_pictures'] ?? array(), $i, $path);

        $imgName = "";
        if (!empty($newFile)) {
            $imgName = $newFile;
        } elseif (!empty($existing)) {
            $imgName = $existing;
        }

        if (!empty($imgName)) {
            $imgNameSafe = str_replace("'", "\'", $imgName);
            $itemCodeSafe = str_replace("'", "\'", $itemCode);
            $defaultTitle = ($i == 0) ? 'Front Picture' : 'Picture ' . ($i + 1);
            $sql = "INSERT INTO tbl_item_images (item_id, item_code, image_title, image_name, sort_order, created_date) 
                    VALUES ('$itemIdSafe', '$itemCodeSafe', '$defaultTitle', '$imgNameSafe', '$i', '$timestamp')";
            mysqli_query($conn, $sql);
        }
    }
}

$path = "uploads/item-master/";
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

// Fetch Master options for Variations
$colors_list = array();
$res_colors = mysqli_query($conn, "SELECT color_name FROM tbl_color_master ORDER BY color_name ASC");
if ($res_colors) {
    while ($row_c = mysqli_fetch_assoc($res_colors)) {
        $colors_list[] = $row_c['color_name'];
    }
}

$sizes_list = array();
$res_sizes = mysqli_query($conn, "SELECT size_name FROM tbl_size_master ORDER BY size_name ASC");
if ($res_sizes) {
    while ($row_s = mysqli_fetch_assoc($res_sizes)) {
        $sizes_list[] = $row_s['size_name'];
    }
}

$styles_list = array();
$res_styles = mysqli_query($conn, "SELECT style_name FROM tbl_styledesign_master ORDER BY style_name ASC");
if ($res_styles) {
    while ($row_st = mysqli_fetch_assoc($res_styles)) {
        $styles_list[] = $row_st['style_name'];
    }
}

$edit_variations = array();
$edit_images = array();

// Fetch record for Edit mode
if (isset($_GET['update_id']) && !empty($_GET['update_id'])) {
    $is_edit = true;
    $edit_id = $_GET['update_id'];
    $run = mysqli_query($conn, "SELECT * FROM tbl_item_master WHERE id='$edit_id'");
    if ($run && mysqli_num_rows($run) > 0) {
        $edit_row = mysqli_fetch_assoc($run);
    }

    $img_query = mysqli_query($conn, "SELECT * FROM tbl_item_images WHERE item_id='$edit_id' ORDER BY sort_order ASC, id ASC");
    if ($img_query) {
        while ($i_row = mysqli_fetch_assoc($img_query)) {
            $edit_images[] = $i_row;
        }
    }

    $var_query = mysqli_query($conn, "SELECT * FROM tbl_item_variations WHERE item_id='$edit_id' ORDER BY id ASC");
    if ($var_query) {
        while ($v_row = mysqli_fetch_assoc($var_query)) {
            $edit_variations[] = $v_row;
        }
    }
}

// UPDATE RECORD
if (isset($_POST['update']) && !empty($edit_id)) {
    $item_code      = $_POST['item_code'];
    $barcode_no     = $_POST['barcode_no'];
    $sku_no         = $_POST['sku_no'];
    $item_name      = $_POST['item_name'];
    $description    = $_POST['description'];
    $group_name     = $_POST['group_name'];
    $subgroup_name  = $_POST['subgroup_name'];
    $brand_name     = $_POST['brand_name'];
    $color_name     = $_POST['color_name'] ?? '';
    $style_name     = $_POST['style_name'] ?? '';
    $size_name      = $_POST['size_name'] ?? '';
    $mou_name       = $_POST['mou_name'];
    $purchase_price = $_POST['purchase_price'] ?? '0';
    $dis_per        = $_POST['dis_per'] ?? '0';
    $dis_amt        = $_POST['dis_amt'] ?? '0';
    $mrp            = $_POST['mrp'] ?? '0';
    $sp             = $_POST['sp'] ?? '0';
    $profit_amt     = $_POST['profit_amt'] ?? '0';
    $min_qty        = $_POST['min_qty'] ?? '0';
    $reorder_qty    = $_POST['reorder_qty'] ?? '0';
    $status         = $_POST['status'];
    $update_date    = date('Y-m-d H:i:s');
    $about_1        = $_POST['about_1'];
    $about_2        = $_POST['about_2'];
    $about_3        = $_POST['about_3'];
    $about_4        = $_POST['about_4'];

    $item_name = str_replace("'", "\'", $item_name);
    $item_name = str_replace('"', '\"', $item_name);

    $sql = "UPDATE tbl_item_master SET 
                item_code='$item_code', barcode_no='$barcode_no', sku_no='$sku_no', item_name='$item_name', description='$description',
                group_name='$group_name', subgroup_name='$subgroup_name', brand_name='$brand_name', color_name='$color_name',
                style_name='$style_name', size_name='$size_name', mou_name='$mou_name',
                purchase_price='$purchase_price', dis_per='$dis_per', dis_amt='$dis_amt', mrp='$mrp', sp='$sp', profit_amt='$profit_amt',
                min_qty='$min_qty', reorder_qty='$reorder_qty',
                status='$status', update_date='$update_date', about_1='$about_1', about_2='$about_2', about_3='$about_3', about_4='$about_4'
            WHERE id='$edit_id'";

    $run = mysqli_query($conn, $sql);
    if ($run) {
        // Save images to tbl_item_images
        saveItemImages($conn, $edit_id, $item_code, $_FILES, $_POST['existing_pictures'] ?? array(), $path, $update_date);
        // Save Variations (with Backend Validation & Duplicate Filtering)
        mysqli_query($conn, "DELETE FROM tbl_item_variations WHERE item_id='$edit_id'");
        if (isset($_POST['var_color_name']) && is_array($_POST['var_color_name'])) {
            $v_colors = $_POST['var_color_name'];
            $v_sizes  = $_POST['var_size_name'] ?? array();
            $v_styles = $_POST['var_style_name'] ?? array();
            $v_qtys   = $_POST['var_qty'] ?? array();
            $v_pps    = $_POST['var_purchase_price'] ?? array();
            $v_mrps   = $_POST['var_mrp'] ?? array();
            $v_dispers = $_POST['var_dis_per'] ?? array();
            $v_disamts = $_POST['var_dis_amt'] ?? array();
            $v_sps    = $_POST['var_sp'] ?? array();

            $u_date = date('Y-m-d H:i:s');
            $seen_combos = array();

            for ($k = 0; $k < count($v_colors); $k++) {
                $vc_color = str_replace("'", "\'", trim($v_colors[$k] ?? ''));
                $vc_size  = str_replace("'", "\'", trim($v_sizes[$k] ?? ''));
                $vc_style = str_replace("'", "\'", trim($v_styles[$k] ?? ''));

                $vc_qty   = (isset($v_qtys[$k]) && is_numeric($v_qtys[$k]) && $v_qtys[$k] >= 0) ? (float)$v_qtys[$k] : 0;
                $vc_pp    = (isset($v_pps[$k]) && is_numeric($v_pps[$k]) && $v_pps[$k] >= 0) ? (float)$v_pps[$k] : 0;
                $vc_mrp   = (isset($v_mrps[$k]) && is_numeric($v_mrps[$k]) && $v_mrps[$k] >= 0) ? (float)$v_mrps[$k] : 0;
                $vc_dis_per = (isset($v_dispers[$k]) && is_numeric($v_dispers[$k]) && $v_dispers[$k] >= 0) ? min(100, (float)$v_dispers[$k]) : 0;
                $vc_dis_amt = (isset($v_disamts[$k]) && is_numeric($v_disamts[$k]) && $v_disamts[$k] >= 0) ? (float)$v_disamts[$k] : 0;
                $vc_sp    = (isset($v_sps[$k]) && is_numeric($v_sps[$k]) && $v_sps[$k] >= 0) ? (float)$v_sps[$k] : 0;

                if (!empty($vc_color) || !empty($vc_size) || !empty($vc_style) || $vc_qty > 0 || $vc_sp > 0 || $vc_mrp > 0) {
                    $combo_key = strtolower($vc_color . '||' . $vc_size . '||' . $vc_style);
                    if (isset($seen_combos[$combo_key])) {
                        continue;
                    }
                    $seen_combos[$combo_key] = true;

                    $v_sql = "INSERT INTO tbl_item_variations 
                                (item_id, item_code, color_name, size_name, style_name, qty, purchase_price, mrp, dis_per, dis_amt, sp, created_date, update_date)
                              VALUES 
                                ('$edit_id', '$item_code', '$vc_color', '$vc_size', '$vc_style', '$vc_qty', '$vc_pp', '$vc_mrp', '$vc_dis_per', '$vc_dis_amt', '$vc_sp', '$u_date', '$u_date')";
                    mysqli_query($conn, $v_sql);
                }
            }
        }

        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            showPopup(
                'Success!',
                'Item Master Updated Successfully 🎉'
            );
            setTimeout(function(){ window.location.href = 'fh_itemmastercreation.php'; }, 1500);
        });
        </script>
        ";
    } else {
        $msg = "<div class='alert alert-danger'>Item Master Update Failed!</div>";
    }
}

// CREATE RECORD
if (isset($_POST['submit'])) {
    $item_code      = $_POST['item_code'];
    $barcode_no     = $_POST['barcode_no'];
    $sku_no         = $_POST['sku_no'];
    $item_name      = $_POST['item_name'];
    $description    = $_POST['description'];
    $group_name     = $_POST['group_name'];
    $subgroup_name  = $_POST['subgroup_name'];
    $brand_name     = $_POST['brand_name'];
    $color_name     = $_POST['color_name'] ?? '';
    $style_name     = $_POST['style_name'] ?? '';
    $size_name      = $_POST['size_name'] ?? '';
    $mou_name       = $_POST['mou_name'];
    $purchase_price = $_POST['purchase_price'] ?? '0';
    $dis_per        = $_POST['dis_per'] ?? '0';
    $dis_amt        = $_POST['dis_amt'] ?? '0';
    $mrp            = $_POST['mrp'] ?? '0';
    $sp             = $_POST['sp'] ?? '0';
    $profit_amt     = $_POST['profit_amt'] ?? '0';
    $min_qty        = $_POST['min_qty'] ?? '0';
    $reorder_qty    = $_POST['reorder_qty'] ?? '0';
    $status         = $_POST['status'];
    $created_date   = date('Y-m-d H:i:s');
    $update_date    = date('Y-m-d H:i:s');
    $about_1        = $_POST['about_1'];
    $about_2        = $_POST['about_2'];
    $about_3        = $_POST['about_3'];
    $about_4        = $_POST['about_4'];

    $check = mysqli_query($conn, "SELECT id FROM tbl_item_master WHERE item_name='$item_name'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert alert-danger'>Item Name Already Exists!</div>";
    } else {
        $sql = "INSERT INTO tbl_item_master (
                    item_code, barcode_no, sku_no, item_name, description,
                    group_name, subgroup_name, brand_name, color_name, style_name, size_name, mou_name,
                    purchase_price, dis_per, dis_amt, mrp, sp, profit_amt,
                    min_qty, reorder_qty,
                    status, created_date, update_date, about_1, about_2, about_3, about_4
                ) VALUES (
                    '$item_code', '$barcode_no', '$sku_no', '$item_name', '$description',
                    '$group_name', '$subgroup_name', '$brand_name', '$color_name', '$style_name', '$size_name', '$mou_name',
                    '$purchase_price', '$dis_per','$dis_amt','$mrp', '$sp', '$profit_amt',
                    '$min_qty', '$reorder_qty',
                    '$status', '$created_date', '$update_date', '$about_1', '$about_2', '$about_3', '$about_4'
                )";

        $run = mysqli_query($conn, $sql);
        if ($run) {
            $new_item_id = mysqli_insert_id($conn);
            // Save images to tbl_item_images
            saveItemImages($conn, $new_item_id, $item_code, $_FILES, $_POST['existing_pictures'] ?? array(), $path, $created_date);
            if (isset($_POST['var_color_name']) && is_array($_POST['var_color_name'])) {
                $v_colors = $_POST['var_color_name'];
                $v_sizes  = $_POST['var_size_name'] ?? array();
                $v_styles = $_POST['var_style_name'] ?? array();
                $v_qtys   = $_POST['var_qty'] ?? array();
                $v_pps    = $_POST['var_purchase_price'] ?? array();
                $v_mrps   = $_POST['var_mrp'] ?? array();
                $v_dispers = $_POST['var_dis_per'] ?? array();
                $v_disamts = $_POST['var_dis_amt'] ?? array();
                $v_sps    = $_POST['var_sp'] ?? array();

                $c_date = date('Y-m-d H:i:s');
                $seen_combos = array();

                for ($k = 0; $k < count($v_colors); $k++) {
                    $vc_color = str_replace("'", "\'", trim($v_colors[$k] ?? ''));
                    $vc_size  = str_replace("'", "\'", trim($v_sizes[$k] ?? ''));
                    $vc_style = str_replace("'", "\'", trim($v_styles[$k] ?? ''));

                    $vc_qty   = (isset($v_qtys[$k]) && is_numeric($v_qtys[$k]) && $v_qtys[$k] >= 0) ? (float)$v_qtys[$k] : 0;
                    $vc_pp    = (isset($v_pps[$k]) && is_numeric($v_pps[$k]) && $v_pps[$k] >= 0) ? (float)$v_pps[$k] : 0;
                    $vc_mrp   = (isset($v_mrps[$k]) && is_numeric($v_mrps[$k]) && $v_mrps[$k] >= 0) ? (float)$v_mrps[$k] : 0;
                    $vc_dis_per = (isset($v_dispers[$k]) && is_numeric($v_dispers[$k]) && $v_dispers[$k] >= 0) ? min(100, (float)$v_dispers[$k]) : 0;
                    $vc_dis_amt = (isset($v_disamts[$k]) && is_numeric($v_disamts[$k]) && $v_disamts[$k] >= 0) ? (float)$v_disamts[$k] : 0;
                    $vc_sp    = (isset($v_sps[$k]) && is_numeric($v_sps[$k]) && $v_sps[$k] >= 0) ? (float)$v_sps[$k] : 0;

                    if (!empty($vc_color) || !empty($vc_size) || !empty($vc_style) || $vc_qty > 0 || $vc_sp > 0 || $vc_mrp > 0) {
                        $combo_key = strtolower($vc_color . '||' . $vc_size . '||' . $vc_style);
                        if (isset($seen_combos[$combo_key])) {
                            continue;
                        }
                        $seen_combos[$combo_key] = true;

                        $v_sql = "INSERT INTO tbl_item_variations 
                                    (item_id, item_code, color_name, size_name, style_name, qty, purchase_price, mrp, dis_per, dis_amt, sp, created_date, update_date)
                                  VALUES 
                                    ('$new_item_id', '$item_code', '$vc_color', '$vc_size', '$vc_style', '$vc_qty', '$vc_pp', '$vc_mrp', '$vc_dis_per', '$vc_dis_amt', '$vc_sp', '$c_date', '$c_date')";
                        mysqli_query($conn, $v_sql);
                    }
                }
            }

            echo "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup(
                    'Success!',
                    'Item Master Saved Successfully 🎉'
                );
                setTimeout(function(){ window.location.href = 'fh_itemmastercreation.php'; }, 1500);
            });
            </script>
            ";
        } else {
            $msg = "<div class='alert alert-danger'>Item Creation Failed!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Item Master <?php echo $is_edit ? 'Edit' : 'Creations'; ?> || TEJASERP</title>
      <?php include('include/css.php');?>  
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <?php include('include/header-top-bar.php');?>
        
         <aside class="main-sidebar">
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?> 
         </aside>
         
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            
            <?php include('include/fh-popup-message-successfully.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?>
            
            <!-- Form Section -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-pencil mr-5"></i>
                              <span><?php echo $is_edit ? 'Item Master Edit !' : 'Item Master Creations !'; ?></span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_itemmastercreation_list.php" class="btn btn-exp btn-sm">
                                 <i class="fa fa-bars mr-5"></i>Item Master List
                              </a>
                              <?php if ($is_edit): ?>
                                 <a href="fh_itemmastercreation.php" class="btn btn-info btn-sm ml-5">
                                    <i class="fa fa-plus mr-5"></i>Add New Item
                                 </a>
                              <?php endif; ?>
                           </div>
                           
                           <form id="itemMasterForm" method="post" enctype="multipart/form-data">
                              <?php echo $msg; ?>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Item Code</label><label class="text-danger">*</label>
                                       <input type="text" name="item_code" class="form-control" id="item_code" readonly value="<?php 
                                          if ($is_edit) {
                                              echo htmlspecialchars($edit_row['item_code']);
                                          } else {
                                              @$no = mysqli_query($conn, "SELECT id FROM tbl_item_master ORDER BY id DESC LIMIT 1");
                                              $po_no = mysqli_fetch_assoc($no);
                                              echo 'IC-000'.(($po_no['id'] ?? 0) + 1);
                                          }
                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Barcode No.</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required value="<?php echo htmlspecialchars($edit_row['barcode_no']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>SKU No.</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="sku_no" id="sku_no" placeholder="Enter Your Article || SKU No" required value="<?php echo htmlspecialchars($edit_row['sku_no']); ?>">
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Item Name</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Your Item Name" required value="<?php echo htmlspecialchars($edit_row['item_name']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Description</label>
                                       <input type="text" class="form-control" name="description" id="description" placeholder="Enter Your Description" value="<?php echo htmlspecialchars($edit_row['description']); ?>">
                                    </div>
                                 </div>
                              </div>
                             
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Group || Category Name</label><label class="text-danger">*</label>
                                       <select name="group_name" class="form-control" required>
                                          <option value="">Select Group || Category Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($edit_row['group_name'] == $row['group_name']) ? 'selected' : '';
                                                echo '<option ' . $selected . '>' . htmlspecialchars($row['group_name']) . '</option>';
                                             } 
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>SubGroup || SubCategory Name</label><label class="text-danger">*</label>
                                       <select name="subgroup_name" class="form-control" required>
                                          <option value="">Select SubGroup || SubCategory Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_subgroup_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($edit_row['subgroup_name'] == $row['subgroup_name']) ? 'selected' : '';
                                                echo '<option ' . $selected . '>' . htmlspecialchars($row['subgroup_name']) . '</option>';
                                             } 
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Brand Name</label><label class="text-danger">*</label>
                                       <select name="brand_name" class="form-control" required>
                                          <option value="">Select Brand Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_brand_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($edit_row['brand_name'] == $row['brand_name']) ? 'selected' : '';
                                                echo '<option ' . $selected . '>' . htmlspecialchars($row['brand_name']) . '</option>';
                                             } 
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Measurement of Units</label><label class="text-danger">*</label>
                                       <select name="mou_name" class="form-control" required>
                                          <option value="">Select Measurement of Units</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_mou_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($edit_row['mou_name'] == $row['mou_name']) ? 'selected' : '';
                                                echo '<option ' . $selected . '>' . htmlspecialchars($row['mou_name']) . '</option>';
                                             } 
                                          ?>
                                       </select>
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="form-group">
                                       <label>Minimum Quantity</label><label class="text-danger">*</label>
                                       <input type="text" class="form-control" name="min_qty" id="min_qty" placeholder="Enter Your Minimum Qty" required value="<?php echo htmlspecialchars($edit_row['min_qty'] ?? ''); ?>">
                                    </div>
                                 </div>
                              </div>

                              <br>
                              <label>About this Item :</label>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>1st Point</label>
                                       <input type="text" class="form-control" name="about_1" id="about_1" placeholder="Enter Details" value="<?php echo htmlspecialchars($edit_row['about_1']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>2nd Point</label>
                                       <input type="text" class="form-control" name="about_2" id="about_2" placeholder="Enter Details" value="<?php echo htmlspecialchars($edit_row['about_2']); ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>3rd Point</label>
                                       <input type="text" class="form-control" name="about_3" id="about_3" placeholder="Enter Details" value="<?php echo htmlspecialchars($edit_row['about_3']); ?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <div class="form-group">
                                       <label>4th Point</label>
                                       <input type="text" class="form-control" name="about_4" id="about_4" placeholder="Enter Details" value="<?php echo htmlspecialchars($edit_row['about_4']); ?>">
                                    </div>
                                 </div>
                              </div>

                              <br>
                              <!-- Item Product Images Panel Section (Max 10 Images) -->
                               <hr class="m-t-20 m-b-20">
                               <div class="row">
                                  <div class="col-sm-12">
                                     <div class="panel panel-bd lobidisable">
                                        <div class="panel-heading" style="background: linear-gradient(135deg, #3f51b5 0%, #303f9f 100%); color: #fff; padding: 12px 15px; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                                           <div class="panel-title" style="display: flex; align-items: center; justify-content: space-between; font-size: 15px; font-weight: 600;">
                                              <span>
                                                 <i class="fa fa-picture-o mr-8" style="font-size: 18px;"></i> Product Images Gallery (Max 10 Images)
                                              </span>
                                              <span class="badge" id="imageCounterBadge" style="background-color: rgba(255, 255, 255, 0.25); color: #fff; font-size: 12px; font-weight: normal; padding: 4px 10px; border-radius: 12px;">
                                                 0 / 10 Images
                                              </span>
                                           </div>
                                        </div>
                                        <div class="panel-body" style="padding: 20px; background-color: #fcfcfc;">
                                           <div class="alert alert-info" style="margin-bottom: 15px; font-size: 13px; background-color: #e8eaf6; color: #1a237e; border-color: #c5cae9;">
                                              <i class="fa fa-info-circle mr-5"></i> <strong>Note:</strong> Upload product images (Front View, Back View, Side Angles, Packaging, etc.). You can add up to 10 images max. Supported formats: JPG, JPEG, PNG, WEBP, GIF.
                                           </div>

                                           <div class="table-responsive">
                                              <table class="table table-bordered table-hover" id="itemImagesTable" style="background-color: #fff; border-radius: 4px;">
                                                 <thead>
                                                    <tr style="background-color: #3f51b5; color: #ffffff;">
                                                       <th width="70%" style="vertical-align: middle;">Select Image File</th>
                                                       <th width="20%" class="text-center" style="vertical-align: middle;">Preview</th>
                                                       <th width="10%" class="text-center" style="vertical-align: middle;">Action</th>
                                                    </tr>
                                                 </thead>
                                                 <tbody id="imageTableBody">
                                                     <?php 
                                                     if (!empty($edit_images)) {
                                                        foreach ($edit_images as $idx => $imgInfo) {
                                                     ?>
                                                        <tr class="image-row">
                                                           <td>
                                                              <input type="file" name="item_pictures[]" class="form-control input-sm img-file-input" accept="image/*">
                                                              <input type="hidden" name="existing_pictures[]" value="<?php echo htmlspecialchars($imgInfo['image_name']); ?>">
                                                           </td>
                                                           <td class="text-center">
                                                              <img src="uploads/item-master/<?php echo htmlspecialchars($imgInfo['image_name']); ?>" class="img-thumbnail thumb-img-40 img-preview" alt="Preview">
                                                           </td>
                                                           <td class="text-center">
                                                              <button type="button" class="btn btn-danger btn-sm remove-img-row" title="Remove Image"><i class="fa fa-trash-o"></i></button>
                                                           </td>
                                                        </tr>
                                                     <?php 
                                                        }
                                                     } else {
                                                     ?>
                                                        <tr class="image-row">
                                                           <td>
                                                              <input type="file" name="item_pictures[]" class="form-control input-sm img-file-input" accept="image/*">
                                                              <input type="hidden" name="existing_pictures[]" value="">
                                                           </td>
                                                           <td class="text-center">
                                                              <span class="text-muted img-preview-empty"><i class="fa fa-picture-o fa-2x"></i></span>
                                                              <img src="" class="img-thumbnail thumb-img-40 img-preview" alt="Preview" style="display:none;">
                                                           </td>
                                                           <td class="text-center">
                                                              <button type="button" class="btn btn-danger btn-sm remove-img-row" title="Remove Image"><i class="fa fa-trash-o"></i></button>
                                                           </td>
                                                        </tr>
                                                     <?php } ?>
                                                 </tbody>
                                              </table>
                                           </div>

                                           <div class="row m-t-10">
                                              <div class="col-sm-12">
                                                 <button type="button" class="btn btn-indigo btn-sm" id="addImageRow" style="background-color: #3f51b5; border-color: #303f9f; color: #fff; font-weight: 500; padding: 6px 16px; border-radius: 4px;">
                                                    <i class="fa fa-plus-circle mr-5"></i> Add More Image
                                                 </button>
                                                 <span class="text-muted ml-10 style-italic" style="font-size: 12px;" id="maxImageNotice">(Maximum 10 images allowed)</span>
                                              </div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>

                               <hr class="m-t-20 m-b-20">
                               <!-- Item Variations Panel Section -->
                               <div class="row">
                                  <div class="col-sm-12">
                                     <div class="panel panel-bd lobidisable">
                                        <div class="panel-heading" style="background: linear-gradient(135deg, #009688 0%, #00796b 100%); color: #fff; padding: 12px 15px; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                                           <div class="panel-title" style="display: flex; align-items: center; justify-content: space-between; font-size: 15px; font-weight: 600;">
                                              <span>
                                                 <i class="fa fa-cubes mr-8" style="font-size: 18px;"></i> Item Variations & Variant-Wise Pricing
                                              </span>
                                              <span class="badge" id="variationCounterBadge" style="background-color: rgba(255, 255, 255, 0.25); color: #fff; font-size: 12px; font-weight: normal; padding: 4px 10px; border-radius: 12px;">
                                                 1 Variant(s)
                                              </span>
                                           </div>
                                        </div>
                                        <div class="panel-body" style="padding: 20px; background-color: #fbfbfb;">
                                           <div class="alert alert-info alert-dismissible" role="alert" style="margin-bottom: 15px; font-size: 13px; background-color: #e0f2f1; color: #004d40; border-color: #b2dfdb;">
                                              <i class="fa fa-info-circle mr-5"></i> <strong>Note:</strong> Set specific Color, Size, Style, Stock Quantity, Purchase Price (CP), MRP, Discount, and Selling Price (SP) for each variant. Selling Price automatically calculates when MRP and Discount are entered.
                                           </div>

                                           <div class="table-responsive">
                                              <table class="table table-bordered table-hover" id="itemVariationsTable" style="background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 4px;">
                                                 <thead>
                                                     <tr style="background-color: #009688; color: #ffffff;">
                                                        <th width="14%" style="vertical-align: middle;">Color Name</th>
                                                        <th width="14%" style="vertical-align: middle;">Size Name</th>
                                                        <th width="14%" style="vertical-align: middle;">Style / Design Name</th>
                                                        <th width="7%" style="vertical-align: middle;">Qty</th>
                                                        <th width="9%" style="vertical-align: middle;">Purchase Price (CP)</th>
                                                        <th width="10%" style="vertical-align: middle;">MRP</th>
                                                        <th width="8%" style="vertical-align: middle;">Disc %</th>
                                                        <th width="10%" style="vertical-align: middle;">Disc Amt.</th>
                                                        <th width="10%" style="vertical-align: middle;">Selling Price (SP)</th>
                                                        <th width="4%" class="text-center" style="vertical-align: middle;">Action</th>
                                                     </tr>
                                                  </thead>
                                                 <tbody id="variationTableBody">
                                                    <?php 
                                                    if (!empty($edit_variations)) {
                                                       foreach ($edit_variations as $idx => $v) {
                                                    ?>
                                                       <tr class="variation-row">
                                                          <td>
                                                             <select name="var_color_name[]" class="form-control input-sm">
                                                                <option value="">Select Color</option>
                                                                <?php foreach ($colors_list as $c_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($c_name); ?>" <?php echo ($v['color_name'] == $c_name) ? 'selected' : ''; ?>>
                                                                      <?php echo htmlspecialchars($c_name); ?>
                                                                   </option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <select name="var_size_name[]" class="form-control input-sm">
                                                                <option value="">Select Size</option>
                                                                <?php foreach ($sizes_list as $s_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($s_name); ?>" <?php echo ($v['size_name'] == $s_name) ? 'selected' : ''; ?>>
                                                                      <?php echo htmlspecialchars($s_name); ?>
                                                                   </option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <select name="var_style_name[]" class="form-control input-sm">
                                                                <option value="">Select Style/Design</option>
                                                                <?php foreach ($styles_list as $st_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($st_name); ?>" <?php echo ($v['style_name'] == $st_name) ? 'selected' : ''; ?>>
                                                                      <?php echo htmlspecialchars($st_name); ?>
                                                                   </option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_qty[]" class="form-control input-sm text-right" placeholder="Qty" value="<?php echo htmlspecialchars($v['qty'] ?? '0'); ?>">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_purchase_price[]" class="form-control input-sm var-cp text-right" placeholder="CP" value="<?php echo htmlspecialchars($v['purchase_price'] ?? '0'); ?>">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_mrp[]" class="form-control input-sm var-mrp text-right" placeholder="MRP" value="<?php echo htmlspecialchars($v['mrp'] ?? '0'); ?>">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_dis_per[]" class="form-control input-sm var-dis text-right" placeholder="Disc %" value="<?php echo htmlspecialchars($v['dis_per'] ?? '0'); ?>">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_dis_amt[]" class="form-control input-sm var-dis-amt text-right" placeholder="Disc Amt" value="<?php echo htmlspecialchars($v['dis_amt'] ?? '0'); ?>">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_sp[]" class="form-control input-sm var-sp text-right" placeholder="SP" value="<?php echo htmlspecialchars($v['sp'] ?? '0'); ?>">
                                                          </td>
                                                          <td class="text-center" style="vertical-align: middle;">
                                                             <button type="button" class="btn btn-danger btn-sm remove-var-row" title="Remove Row"><i class="fa fa-trash-o"></i></button>
                                                          </td>
                                                       </tr>
                                                    <?php 
                                                       }
                                                    } else {
                                                    ?>
                                                       <tr class="variation-row">
                                                          <td>
                                                             <select name="var_color_name[]" class="form-control input-sm">
                                                                <option value="">Select Color</option>
                                                                <?php foreach ($colors_list as $c_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($c_name); ?>"><?php echo htmlspecialchars($c_name); ?></option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <select name="var_size_name[]" class="form-control input-sm">
                                                                <option value="">Select Size</option>
                                                                <?php foreach ($sizes_list as $s_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($s_name); ?>"><?php echo htmlspecialchars($s_name); ?></option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <select name="var_style_name[]" class="form-control input-sm">
                                                                <option value="">Select Style/Design</option>
                                                                <?php foreach ($styles_list as $st_name): ?>
                                                                   <option value="<?php echo htmlspecialchars($st_name); ?>"><?php echo htmlspecialchars($st_name); ?></option>
                                                                <?php endforeach; ?>
                                                             </select>
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_qty[]" class="form-control input-sm text-right" placeholder="Qty" value="0">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_purchase_price[]" class="form-control input-sm var-cp text-right" placeholder="CP" value="0">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_mrp[]" class="form-control input-sm var-mrp text-right" placeholder="MRP" value="0">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_dis_per[]" class="form-control input-sm var-dis text-right" placeholder="Disc %" value="0">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_dis_amt[]" class="form-control input-sm var-dis-amt text-right" placeholder="Disc Amt" value="0">
                                                          </td>
                                                          <td>
                                                             <input type="number" step="any" min="0" name="var_sp[]" class="form-control input-sm var-sp text-right" placeholder="SP" value="0">
                                                          </td>
                                                          <td class="text-center" style="vertical-align: middle;">
                                                             <button type="button" class="btn btn-danger btn-sm remove-var-row" title="Remove Row"><i class="fa fa-trash-o"></i></button>
                                                          </td>
                                                       </tr>
                                                    <?php } ?>
                                                 </tbody>
                                              </table>
                                           </div>

                                           <div class="row m-t-15 m-b-10">
                                              <div class="col-sm-12">
                                                 <button type="button" class="btn btn-success" id="addVariationRow" style="background: linear-gradient(135deg, #00897b 0%, #00695c 100%); border: none; color: #ffffff; font-weight: 600; font-size: 13px; padding: 8px 22px; border-radius: 4px; box-shadow: 0 3px 8px rgba(0, 137, 123, 0.4); display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fa fa-plus-circle" style="font-size: 16px;"></i> Add More Variation Row
                                                 </button>
                                                 <span class="text-muted ml-10 style-italic" style="font-size: 12px; vertical-align: middle;">(Click to add another product variant row)</span>
                                              </div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>

                               <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><label class="text-danger">*</label><br>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="true" <?php echo ($edit_row['status'] == 'true' || strtolower($edit_row['status']) == 'active') ? 'checked="checked"' : ''; ?>>Active
                                       </label>
                                       <label class="radio-inline">
                                          <input type="radio" name="status" value="false" <?php echo ($edit_row['status'] == 'false' || strtolower($edit_row['status']) == 'inactive') ? 'checked="checked"' : ''; ?>>Inactive
                                       </label>
                                    </div>
                                 </div>
                              </div>

                              <br><br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <?php if ($is_edit): ?>
                                       <a href="fh_itemmastercreation.php" class="btn btn-default btn-w-100">Cancel</a>
                                       <button type="submit" name="update" class="btn btn-teal-100">Update</button>
                                    <?php else: ?>
                                       <button type="reset" class="btn btn-warning btn-w-100">Reset</button>
                                       <button type="submit" name="submit" class="btn btn-teal-100">Save</button>
                                    <?php endif; ?>
                                 </div>
                              </div>
                              <br>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- List Section -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <span class="fs-15 panel-header-text">
                              <i class="fa fa-th-list mr-5"></i>
                              <span>Item Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                            <!-- Toolbar & Search Box Row under Header Panel -->
                            <div class="row m-b-15">
                               <div class="col-sm-6 col-xs-12 m-b-5">
                                  <div class="btn-group">
                                     <a href="fh_itemmastercreation.php" class="btn btn-exp btn-sm">
                                        <i class="fa fa-plus mr-5"></i>Add New Item Master Creation
                                     </a>
                                  </div>
                                  <?php include('include/button-export-to-data.php');?>
                               </div>
                               <div class="col-sm-6 col-xs-12">
                                  <!-- Search Form Box under Header Panel -->
                                  <form id="itemMasterSearchForm" class="search-form-wrap pull-right" onsubmit="return false;" style="width: 100%; max-width: 360px;">
                                     <input type="hidden" name="page" value="1">
                                     <div class="input-group search-box-group">
                                        <input type="text" name="search" class="form-control search-input search-input-field" placeholder="Search Item Code, Name, Barcode, Group...">
                                        <span class="input-group-addon search-addon-btn">
                                           <i class="fa fa-search search-icon-sm"></i>
                                        </span>
                                     </div>
                                  </form>
                                  <div class="clearfix"></div>
                               </div>
                            </div>

                           <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th width="50px">SrNo</th>
                                       <th width="150px">Action</th>
                                       <th width="80px">Status</th>
                                       <th width="70px">Image</th>
                                       <th>Item Code</th>
                                       <th>Item Name</th>
                                       <th>Barcode No</th>
                                       <th>SKU No</th>
                                       <th>Group</th>
                                       <th>SubGroup</th>
                                       <th>Brand</th>
                                       <th>MRP</th>
                                       <th>Selling Price</th>
                                    </tr>
                                 </thead>
                                 <tbody id="itemmaster-list-results">
                                    <!-- Populated via AJAX -->
                                 </tbody>
                              </table>
                           </div>

                           <!-- Pagination -->
                           <div id="pagination-result"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         </div>

         <!-- Container for View Modals -->
         <div id="item-modals-container"></div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?>
      </div>

      <?php include('include/js.php');?>
      <!-- Item Master AJAX Search & Pagination JS -->
      <script src="assets/dist/js/page/itemmaster-list.js"></script>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
         // --- 1. PRODUCT IMAGES GALLERY JS LOGIC (Max 10 Images) ---
         var addImgBtn = document.getElementById('addImageRow');
         var imgTableBody = document.getElementById('imageTableBody');
         var imgBadge = document.getElementById('imageCounterBadge');
         var maxImgNotice = document.getElementById('maxImageNotice');

         function updateImageCounter() {
            if (imgBadge && imgTableBody) {
               var count = imgTableBody.querySelectorAll('.image-row').length;
               imgBadge.textContent = count + ' / 10 Images';

               if (count >= 10) {
                  if (addImgBtn) {
                     addImgBtn.disabled = true;
                     addImgBtn.style.opacity = '0.6';
                     addImgBtn.style.cursor = 'not-allowed';
                  }
                  if (maxImgNotice) {
                     maxImgNotice.textContent = '(Maximum limit of 10 images reached)';
                     maxImgNotice.style.color = '#d9534f';
                     maxImgNotice.style.fontWeight = 'bold';
                  }
               } else {
                  if (addImgBtn) {
                     addImgBtn.disabled = false;
                     addImgBtn.style.opacity = '1';
                     addImgBtn.style.cursor = 'pointer';
                  }
                  if (maxImgNotice) {
                     maxImgNotice.textContent = '(Maximum 10 images allowed)';
                     maxImgNotice.style.color = '#737373';
                     maxImgNotice.style.fontWeight = 'normal';
                  }
               }
            }
         }

         if (addImgBtn && imgTableBody) {
            updateImageCounter();

            addImgBtn.addEventListener('click', function() {
               var currentRows = imgTableBody.querySelectorAll('.image-row');
               if (currentRows.length >= 10) {
                  alert('Maximum limit reached: You can upload up to 10 product images only.');
                  return;
               }

               var newRow = document.createElement('tr');
               newRow.className = 'image-row';

               newRow.innerHTML = '<td><input type="file" name="item_pictures[]" class="form-control input-sm img-file-input" accept="image/*"><input type="hidden" name="existing_pictures[]" value=""></td>' +
                  '<td class="text-center"><span class="text-muted img-preview-empty"><i class="fa fa-picture-o fa-2x"></i></span><img src="" class="img-thumbnail thumb-img-40 img-preview" alt="Preview" style="display:none;"></td>' +
                  '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-img-row" title="Remove Image"><i class="fa fa-trash-o"></i></button></td>';

               imgTableBody.appendChild(newRow);
               updateImageCounter();
            });

            // Remove Image Row
            imgTableBody.addEventListener('click', function(e) {
               var btn = e.target.closest('.remove-img-row');
               if (btn) {
                  var rows = imgTableBody.querySelectorAll('.image-row');
                  if (rows.length > 1) {
                     var row = btn.closest('.image-row');
                     if (row) {
                        row.remove();
                        updateImageCounter();
                     }
                  } else {
                     // Last remaining row: reset inputs instead of removing row
                     var row = btn.closest('.image-row');
                     if (row) {
                        var fileInp = row.querySelector('.img-file-input');
                        var existInp = row.querySelector('input[name="existing_pictures[]"]');
                        var prevImg = row.querySelector('.img-preview');
                        var prevEmpty = row.querySelector('.img-preview-empty');

                        if (fileInp) fileInp.value = '';
                        if (existInp) existInp.value = '';
                        if (prevImg) { prevImg.src = ''; prevImg.style.display = 'none'; }
                        if (prevEmpty) prevEmpty.style.display = 'inline-block';
                     }
                     updateImageCounter();
                  }
               }
            });

            // Live Image Preview via FileReader
            imgTableBody.addEventListener('change', function(e) {
               if (e.target.classList.contains('img-file-input')) {
                  var fileInp = e.target;
                  var row = fileInp.closest('.image-row');
                  if (!row) return;

                  var prevImg = row.querySelector('.img-preview');
                  var prevEmpty = row.querySelector('.img-preview-empty');
                  var existInp = row.querySelector('input[name="existing_pictures[]"]');

                  if (fileInp.files && fileInp.files[0]) {
                     var file = fileInp.files[0];
                     var reader = new FileReader();
                     reader.onload = function(evt) {
                        if (prevImg) {
                           prevImg.src = evt.target.result;
                           prevImg.style.display = 'inline-block';
                        }
                        if (prevEmpty) {
                           prevEmpty.style.display = 'none';
                        }
                     };
                     reader.readAsDataURL(file);
                     if (existInp) {
                        existInp.value = ''; // clear existing picture when new file selected
                     }
                  }
               }
            });
         }

         // --- 2. ITEM VARIATION TABLE JS LOGIC ---
         var addVarBtn = document.getElementById('addVariationRow');
         var varTableBody = document.getElementById('variationTableBody');
         var varBadge = document.getElementById('variationCounterBadge');
         var mainForm = document.getElementById('itemMasterForm') || document.querySelector('form[method="post"]');

         function updateVariationCounter() {
            if (varBadge && varTableBody) {
               var count = varTableBody.querySelectorAll('.variation-row').length;
               varBadge.textContent = count + ' Variant(s)';
            }
         }

         // Options templates for dynamic variation rows
         var varColorOpts = '<option value="">Select Color</option><?php foreach ($colors_list as $c_name): ?><option value="<?php echo htmlspecialchars(addslashes($c_name)); ?>"><?php echo htmlspecialchars(addslashes($c_name)); ?></option><?php endforeach; ?>';
         var varSizeOpts  = '<option value="">Select Size</option><?php foreach ($sizes_list as $s_name): ?><option value="<?php echo htmlspecialchars(addslashes($s_name)); ?>"><?php echo htmlspecialchars(addslashes($s_name)); ?></option><?php endforeach; ?>';
         var varStyleOpts = '<option value="">Select Style/Design</option><?php foreach ($styles_list as $st_name): ?><option value="<?php echo htmlspecialchars(addslashes($st_name)); ?>"><?php echo htmlspecialchars(addslashes($st_name)); ?></option><?php endforeach; ?>';

         if (addVarBtn && varTableBody) {
            updateVariationCounter();

            addVarBtn.addEventListener('click', function() {
               var tr = document.createElement('tr');
               tr.className = 'variation-row';
               tr.innerHTML = '<td><select name="var_color_name[]" class="form-control input-sm">' + varColorOpts + '</select></td>' +
                  '<td><select name="var_size_name[]" class="form-control input-sm">' + varSizeOpts + '</select></td>' +
                  '<td><select name="var_style_name[]" class="form-control input-sm">' + varStyleOpts + '</select></td>' +
                  '<td><input type="number" step="any" min="0" name="var_qty[]" class="form-control input-sm text-right" placeholder="Qty" value="0"></td>' +
                  '<td><input type="number" step="any" min="0" name="var_purchase_price[]" class="form-control input-sm var-cp text-right" placeholder="CP" value="0"></td>' +
                  '<td><input type="number" step="any" min="0" name="var_mrp[]" class="form-control input-sm var-mrp text-right" placeholder="MRP" value="0"></td>' +
                  '<td><input type="number" step="any" min="0" name="var_dis_per[]" class="form-control input-sm var-dis text-right" placeholder="Disc %" value="0"></td>' +
                  '<td><input type="number" step="any" min="0" name="var_dis_amt[]" class="form-control input-sm var-dis-amt text-right" placeholder="Disc Amt" value="0"></td>' +
                  '<td><input type="number" step="any" min="0" name="var_sp[]" class="form-control input-sm var-sp text-right" placeholder="SP" value="0"></td>' +
                  '<td class="text-center" style="vertical-align: middle;"><button type="button" class="btn btn-danger btn-sm remove-var-row" title="Remove Row"><i class="fa fa-trash-o"></i></button></td>';

               varTableBody.appendChild(tr);
               updateVariationCounter();
            });

            varTableBody.addEventListener('click', function(e) {
               var btn = e.target.closest('.remove-var-row');
               if (btn) {
                  var rows = varTableBody.querySelectorAll('.variation-row');
                  if (rows.length > 1) {
                     var row = btn.closest('.variation-row');
                     if (row) {
                        row.remove();
                        updateVariationCounter();
                     }
                  } else {
                     alert('At least one variation row must remain.');
                  }
               }
            });

            // Auto-calculate SP, Discount %, or Discount Amt per variation row
            varTableBody.addEventListener('input', function(e) {
               var row = e.target.closest('.variation-row');
               if (!row) return;

               var mrpInp    = row.querySelector('.var-mrp');
               var disPerInp = row.querySelector('.var-dis');
               var disAmtInp = row.querySelector('.var-dis-amt');
               var spInp     = row.querySelector('.var-sp');

               if (mrpInp && spInp) {
                  var mrp = parseFloat(mrpInp.value) || 0;

                  if (e.target.classList.contains('var-mrp') || e.target.classList.contains('var-dis')) {
                     var disPer = parseFloat(disPerInp ? disPerInp.value : 0) || 0;
                     var disAmt = mrp * (disPer / 100);
                     var spVal  = mrp - disAmt;
                     if (disAmtInp) disAmtInp.value = Math.max(0, disAmt).toFixed(2);
                     if (spInp) spInp.value = Math.max(0, spVal).toFixed(2);
                  } else if (e.target.classList.contains('var-dis-amt')) {
                     var disAmt = parseFloat(disAmtInp ? disAmtInp.value : 0) || 0;
                     var disPer = (mrp > 0) ? (disAmt / mrp) * 100 : 0;
                     var spVal  = mrp - disAmt;
                     if (disPerInp) disPerInp.value = Math.max(0, disPer).toFixed(2);
                     if (spInp) spInp.value = Math.max(0, spVal).toFixed(2);
                  } else if (e.target.classList.contains('var-sp')) {
                     var spVal = parseFloat(spInp.value) || 0;
                     if (mrp > 0) {
                        var disAmt = mrp - spVal;
                        var disPer = (disAmt / mrp) * 100;
                        if (disAmtInp) disAmtInp.value = Math.max(0, disAmt).toFixed(2);
                        if (disPerInp) disPerInp.value = Math.max(0, disPer).toFixed(2);
                     }
                  }
               }
            });
         }

         // Client-Side Form Submission Validation
         if (mainForm) {
            mainForm.addEventListener('submit', function(e) {
               if (!varTableBody) return;
               var rows = varTableBody.querySelectorAll('.variation-row');
               var seenCombos = {};

               for (var i = 0; i < rows.length; i++) {
                  var row = rows[i];
                  var cSel = row.querySelector('select[name="var_color_name[]"]');
                  var sSel = row.querySelector('select[name="var_size_name[]"]');
                  var stSel = row.querySelector('select[name="var_style_name[]"]');

                  var color = cSel ? cSel.value.trim() : '';
                  var size = sSel ? sSel.value.trim() : '';
                  var style = stSel ? stSel.value.trim() : '';

                  var comboKey = (color + '|' + size + '|' + style).toLowerCase();

                  // Check duplicate combinations
                  if (color !== '' || size !== '' || style !== '') {
                     if (seenCombos[comboKey]) {
                        e.preventDefault();
                        alert('Validation Error (Variation Row #' + (i + 1) + '):\n\nDuplicate variation combination detected:\n- Color: ' + (color || 'N/A') + '\n- Size: ' + (size || 'N/A') + '\n- Style: ' + (style || 'N/A') + '\n\nPlease ensure each variation row has a unique combination.');
                        if (cSel) cSel.focus();
                        return false;
                     }
                     seenCombos[comboKey] = true;
                  }

                  // Validate negative values
                  var qtyInp    = row.querySelector('input[name="var_qty[]"]');
                  var cpInp     = row.querySelector('input[name="var_purchase_price[]"]');
                  var mrpInp    = row.querySelector('input[name="var_mrp[]"]');
                  var disInp    = row.querySelector('input[name="var_dis_per[]"]');
                  var disAmtInp = row.querySelector('input[name="var_dis_amt[]"]');
                  var spInp     = row.querySelector('.var-sp');

                  if (qtyInp && parseFloat(qtyInp.value) < 0) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nQuantity cannot be negative.');
                     qtyInp.focus();
                     return false;
                  }
                  if (cpInp && parseFloat(cpInp.value) < 0) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nPurchase Price (CP) cannot be negative.');
                     cpInp.focus();
                     return false;
                  }
                  if (mrpInp && parseFloat(mrpInp.value) < 0) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nMRP cannot be negative.');
                     mrpInp.focus();
                     return false;
                  }
                  if (disInp && (parseFloat(disInp.value) < 0 || parseFloat(disInp.value) > 100)) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nDiscount % must be between 0 and 100.');
                     disInp.focus();
                     return false;
                  }
                  if (disAmtInp && parseFloat(disAmtInp.value) < 0) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nDiscount Amt. cannot be negative.');
                     disAmtInp.focus();
                     return false;
                  }
                  if (spInp && parseFloat(spInp.value) < 0) {
                     e.preventDefault();
                     alert('Validation Error (Variation Row #' + (i + 1) + '):\nSelling Price (SP) cannot be negative.');
                     spInp.focus();
                     return false;
                  }
               }
            });
         }
      });
      </script>
   </body>
</html>