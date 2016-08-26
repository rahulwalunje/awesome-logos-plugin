<?php
function wps_awesome_logos_manage_page() {
    if(isset($_POST['wps_logos_slidername']) && isset($_POST['logostype'])) {
        $logos_name = $_POST['wps_logos_slidername'];
        $logos_type = $_POST['logostype'];
            global $wpdb,$table_prefix;
            $wps_logos_meta = $table_prefix.WPS_AWESOME_LOGOS_META;
            $sql = "INSERT INTO $wps_logos_meta (slider_name,slider_type) VALUES('$logos_name','$logos_type');";
            $result = $wpdb->query($sql);
        $id = $wpdb->insert_id;
        $urldata = array();
        $current_url = admin_url('edit.php?page=awesome-logos-admin?post_type=wps_logos');
        $urldata['s_id'] = $id;
        $query_data = add_query_arg($urldata,$current_url);
        #create new settings
        $default_wps_settings = wps_default_awesome_logos_settings();
        $wps_logos_sett = array();
        foreach($default_wps_settings as $key=>$value) {
          if(!isset($wps_logos_sett[$key])) {
             $wps_logos_sett[$key] = $value;
          }
        }
        update_option('wps_logos_settings'.$id,$wps_logos_sett);
    }
?>
<div class="wrapper wps-awesome-logos">
    <h2 class="logos-settings-page-title"> Awesome Logos Dashboard </h2>
    <?php
    if ( isset( $_GET['del_id'] ) ) {
        global $wpdb, $table_prefix;
        $del_id = $_GET['del_id'];
        $wps_logos_meta = $table_prefix.WPS_AWESOME_LOGOS_META;
        $where = "slider_id =".$del_id;  
        $sql = "DELETE FROM $wps_logos_meta WHERE $where";
        $wpdb->query($sql);
        delete_option('wps_logos_settings'.$del_id);    #delete associated logos settings
    ?>
        <div class="wrap">
            <div id="message" class="updated notice is-dismissible">
                <p>Logos <strong>deleted</strong>.</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        </div>
<?php
    }
$results = wps_get_logos_info();
?>
<div class="manage-logos-headn"> Manage Logos </div>     
    <div class="table-layout setting-content" style="margin-right:2%;" >
        <div class="table-layout-body">
            <table id="data-table-slider" class="widefat display no-wrap dataTable" >
                <thead>
                    <tr>
                        <th style="width:24%"><?php _e( "Logos Name" ); ?></th>
                        <th style="width:18%"><?php _e( "Edit" ); ?></th>
                        <th style="width:18%"><?php _e( "Delete" ); ?></th>
                        <th style="width:20%"><?php _e( "Short-Code" ); ?></th>   
                        <th style="width:20%"><?php _e( "Type"); ?></th>                               
                    </tr>
                </thead>
                <tbody>
                 <?php foreach($results as $res) { 
                    $del_sldr_url = admin_url('edit.php?post_type=wps_logos&page=awesome-logos-manage&del_id='.$res->slider_id);
                 ?>  
                    <tr>
                        <td style="padding:20px;"><?php echo $res->slider_name; ?></td>
                        <td style="padding:18px 0 0 40px;" >
                            <a class="hovertip" href="edit.php?post_type=wps_logos&page=awesome-logos-admin&s_id=<?php echo $res->slider_id ?>" style="cursor:pointer;" title="Edit Slider" >
                                <span class="dashicons dashicons-edit"></span>
                            </a>    
                        </td> 
                        <td style="padding:18px 0 0 40px;" >
                            <a class="hovertip" style="cursor: pointer;" title="Delete Slider" onclick="return delete_wpslogos_instance();" href="<?php echo $del_sldr_url;?>" >
                                <span class="dashicons dashicons-trash"></span>
                            </a>    
                        </td> 
                        <td><pre>[awesomelogos <?php echo $res->slider_id;?>]</pre></td>
                        <td style="padding-top:20px;"><?php echo $res->slider_type;?></td>
                    </tr>                
                <?php }  ?>  
                </tbody>
            </table>        
        </div>
    </div>
    <!-- Data table ends -->
    <div class="manage-slider-wrapper">
        <div class="accordion" style="margin-right:2%;" id="section1"><?php _e('Create New Awesome Logo','wps-awesome-logos'); ?><span></span></div>
        <div class="setting-content" style="margin-right:2%;background: #4E4E4E;">
            <form method="post" action="#" id="logos_slider_form" >
                <table class="logos-dashboard-table" style="margin:0 auto;" >
                <tr valign="top" class="wps-center">
                    <td> 
                        <input name="wps_logos_slidername" type="text" placeholder="Name your Awesome Logos" id="wps_logos_slidername" /> 
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <span class="logo-type-radio" style="padding:10px;">
                            <input type="radio" name="logostype" value="Slider"> Slider Carousel</input>
                        </span>
                       
                        <span class="logo-type-radio">
                            <input type="radio" name="logostype" value="Simple-Grid"> Simple Grid</input>
                        </span>

                        <span class="logo-type-radio">
                            <input type="radio" name="logostype" value="Responsive-Grid"> Responsive Grid</input>
                        </span>
                    </td>
                </tr>

                </table>
                <p class="submit">
                        <input type="submit" class="create_go" id="create_save_btn" value="<?php _e('Go >') ?>" />
                </p>
            </form>
        </div>
</div>
</div>
<script>
function delete_wpslogos_instance() {
    var x;
    if (confirm("Delete this logo?") == true) {
        return true;
    } else {
        return false;
    }
}
</script>
<?php
}
?>