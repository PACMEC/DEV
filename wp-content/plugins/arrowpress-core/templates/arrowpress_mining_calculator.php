<?php
$output = $el_id = $el_class =$type = $button_bg = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'title' => '',
    'coin_select'=> '',
    'button_bg' => 'f5b71f',
    'coin_select_active'=> '',
), $atts));
$button_bg = str_replace('#', '', $button_bg);
$el_class = arrowpress_shortcode_extract_class($el_class);
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

$args = array();
$coin_select_array = explode(',', $coin_select);

?>
<script type="text/javascript">
  function showCalc(calc, width, autosize, borderwidth, bordercolor, calcButtonColor, backButtonColor, dailyColor, weeklyColor, monthlyColor, yearlyColor, backColor, addonColor, dev) {
      calcButtonColor=typeof calcButtonColor==="undefined"?"ff7f24": calcButtonColor;
      backButtonColor=typeof backButtonColor==="undefined"?"ff7f24": backButtonColor;
      dailyColor=typeof dailyColor==="undefined"?"4e9f15": dailyColor;
      weeklyColor=typeof weeklyColor==="undefined"?"09c": weeklyColor;
      monthlyColor=typeof monthlyColor==="undefined"?"f0ad4e": monthlyColor;
      yearlyColor=typeof yearlyColor==="undefined"?"d9534f": yearlyColor;
      backColor=typeof backColor==="undefined"?"f5f5f5": backColor;
      addonColor=typeof addonColor==="undefined"?"eee": addonColor;
      dev=typeof dev==="undefined"?false: dev;
      var copyright=document.getElementById("cr-copyright");
      if(copyright) {
          copyright.parentNode.removeChild(copyright)
      }
      var height=495;
      var minWidth=320;
      var borderpadding=10;
      var linkheight=15;
      var outerwidth=0;
      var outerheight=0;
      var outerwidthStr="";
      var widthStr="";
      var paddingStr="";
      if(autosize==true) {
          outerwidthStr="100%";
          widthStr="100%"
      }
      else {
          outerwidth=Number(width);
          outerwidthStr=outerwidth.toString()+"px; ";
          widthStr=width.toString()+"px; "
      }
      outerheight=height;
      outerheight+=linkheight;
      if(Number(width)<415) {
          height+=linkheight;
          outerheight+=linkheight
      }
      if(document.body.clientWidth<415) {
          height=510;
          outerwidthStr="100%";
          widthStr="100%"
      }
      if(borderwidth>0) {
          paddingStr=" padding:"+borderpadding+"px; "
      }
      var iURL="";
      if(dev==true) {
          iURL="//"+location.hostname
      }
      else {
          iURL="https://cryptorival.com"
      }
      var embed_widget=""+
      '<div style="min-width:'+minWidth+"px; width:"+outerwidthStr+"; min-height:"+outerheight+"px; height:"+outerheight+"px; max-height:"+outerheight+"px;"+paddingStr+"border:"+borderwidth+"px solid #"+bordercolor+";"+'display:inline-block; box-sizing:unset;">'+'<iframe name="frame1" src="'+iURL+"/widget/calcs/"+calc+"?calcButtonColor="+calcButtonColor+"&backButtonColor="+backButtonColor+"&dailyColor="+dailyColor+"&weeklyColor="+weeklyColor+"&monthlyColor="+monthlyColor+"&yearlyColor="+yearlyColor+"&backColor="+backColor+"&addonColor="+addonColor+'" allowtransparency="false" scrolling="no" frameborder="0" border="0" cellspacing="0" style="height:'+height+"px; width:"+widthStr+"; min-width:"+minWidth+"px; min-height:"+height+"px; max-height:"+height+'px;"></iframe>'+'<div style="text-align:right; padding: 0 5px 0px 0;">'+"</div>"+"</div>";
      document.write(embed_widget)
  }
    var contents = jQuery('iframe').contents(),
    body = contents.find('body'),
    styleTag = jQuery('<style>body{background: red;}</style>').appendTo(contents.find('head'));
     
    // showCalc('bitcoin', '500', false, '0', 'ff7f24', 'ff7f24', 'ff7f24', '4e9f15', '09c', 'f0ad4e', 'd9534f', 'f5f5f5', 'eee');
    // showCalc('ethereum', '500', false, '0', 'ff7f24', 'ff7f24', 'ff7f24', '4e9f15', '09c', 'f0ad4e', 'd9534f', 'f5f5f5', 'eee');
</script>
<?php if((is_array($coin_select_array) || is_object($coin_select_array))):?>
  <div class="apr_mining_calc">
    <ul class="nav nav-tabs">
    <?php foreach ($coin_select_array as $coin_name) {?>
      <?php if($coin_name==$coin_select_active):?>
        <li class="active"><a data-toggle="tab" href="#<?php echo $coin_name;?>"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/assets/images/'. $coin_name.'.png' ;?>" alt="<?php echo $coin_name;?>"></a></li>
      <?php else:?>
        <li><a data-toggle="tab" href="#<?php echo $coin_name;?>"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/assets/images/'. $coin_name.'.png' ;?>" alt="<?php echo $coin_name;?>"></a></li>
      <?php endif;?>
      
    <?php }?>
    </ul>

    <div class="tab-content">
      <?php foreach ($coin_select_array as $coin_name) {
        $showCalc = "showCalc('".$coin_name."', '320', true, '0', 'ff7f24', '".$button_bg."', '".$button_bg."', '4e9f15', '09c', 'f0ad4e', 'd9534f', 'fff', 'eee')";
        ?>
        <?php if($coin_name==$coin_select_active):?>
          <div id="<?php echo $coin_name;?>" class="tab-pane fade in active">
            <script type="text/javascript">
                <?php echo $showCalc.';';?>
            </script>
          </div>          
        <?php else:?>  
          <div id="<?php echo $coin_name;?>" class="tab-pane fade in">
            <script type="text/javascript">
                <?php echo $showCalc.';';?>
            </script>
          </div>          
        <?php endif;?>
      <?php }?>
    </div>
  </div>  
<?php endif;?>
