<?php

defined( 'ABSPATH' ) or exit;

$designs = pwgc_get_designs();
$design = reset( $designs );
$design_id = key( $designs );

?>
<div id="pwgc-designer-main">
    <div id="pwgc-designer-panel-container">
        <?php
            require_once( 'designer-panel.php' );
        ?>
    </div>
</div>