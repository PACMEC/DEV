<div class="wrap">
    <h2>
        <?php _e('Rewards Hub Settings', 'rewards-hub-script-installer'); ?>
        <a class="add-new-h2" target="_blank"
           href="<?php echo esc_url("https://pacmec.com.co"); ?>"> <?php _e('RewardsHub Website', 'rewards-hub-script-installer'); ?>
        </a>
    </h2>
    <hr/>

    <form name="dofollow" action="options.php" method="post">
        <?php settings_fields('rewards-hub-script-installer'); ?>
        <h3 class="rewardshub-labels" for="rewardshub_sitekey">
            <?php _e('Currency', 'rewards-hub-script-installer'); ?>
        </h3>
        <script nonce="IP6P03N6Lqw56G+jxhWtww==">
            function setSiteKey()
            {
                var siteKeys = <?php echo json_encode(get_option('rewardshub_previousSiteKeys')); ?> ;
                var currSiteKey = siteKeys[document.getElementById("rewardshub_currentcurrency").selectedIndex];
                if (currSiteKey === undefined || currSiteKey === null)
                    setDefaultSiteKey();
                 else
                    document.getElementById("rewardshub_sitekey").value = currSiteKey;
            }
            function setDefaultSiteKey()
            {
                var defaultSiteKeys = <?php echo json_encode(get_option('rewardshub_defaultsitekeys')); ?> ;
                document.getElementById("rewardshub_sitekey").value= defaultSiteKeys[document.getElementById("rewardshub_currentcurrency").selectedIndex];
            }

        </script>
        <select style="width:98%;" rows="1" cols="57" id="rewardshub_currentcurrency" name="rewardshub_currentcurrency" onchange='setSiteKey()' <?php if (count(get_option('rewardshub_currencies')) == 1) echo 'disabled'; ?>>
            <?php
            for ($x = 0; $x <= (count(get_option('rewardshub_currencies')) - 1); $x++) {
                echo "<option " . ((get_option('rewardshub_currentcurrency') == $x) ? "selected " : "") . "value='$x'>". (get_option('rewardshub_currencies')[$x] == 'web' ? 'MINTME' : strtoupper(get_option('rewardshub_currencies')[$x])) . "</option>";
            }
            echo "</select>";
            ?>
        <?php
            if (count(get_option('rewardshub_currencies')) == 1) {
                echo '<input type="hidden" name="rewardshub_currentcurrency" value="0">';
            }
        ?>
        <h3 class="rewardshub-labels" for="rewardshub_sitekey">
            <?php _e('Site key', 'rewards-hub-script-installer'); ?>
            <a class="add-new-h2" onclick="setDefaultSiteKey()" href="#">Set default site key</a>
        </h3>
        <textarea style="width:98%;" rows="1" id="rewardshub_sitekey" name="rewardshub_sitekey"><?php echo esc_html(get_option('rewardshub_sitekey')); ?></textarea>
            <br>
        <hr/>

        <h3 class="rewardshub-labels" for="rewardshub_throttle">
            <?php _e('CPU usage', 'rewards-hub-script-installer'); ?>
        </h3>

        <select style="width:98%;" rows="1" cols="57" id="rewardshub_throttle" name="rewardshub_throttle">
            <?php
            for ($x = 100; $x >= 10; $x-=10) {
            $throttle = 1 - ($x/100);
                echo "<option " . (((string) get_option('rewardshub_throttle') == (string) $throttle) ? "selected " : "") . "value='$throttle'>".$x . "%</option>";
            }
            ?>
        </select>

        <hr/>

        <h3 class="rewardshub-labels">
            <?php _e('Mining Notification Settings', 'rewards-hub-script-installer'); ?>
        </h3>

        <h4 class="rewardshub-labels">
            <?php _e('Notification Method', 'rewards-hub-script-installer'); ?>
        </h4>

        <input type="radio" name="rewardshub_notify"
               value="Never" <?php if (get_option('rewardshub_notify') == "Never") echo 'checked'; ?>> Disable<br>
        <input type="radio" name="rewardshub_notify"
               value="Floating" <?php if (get_option('rewardshub_notify') == "Floating") echo 'checked'; ?>> Floating text box on the bottom right corner<br>
        <input type="radio" name="rewardshub_notify"
               value="Footer" <?php if (get_option('rewardshub_notify') == "Footer") echo 'checked'; ?>> Fixed footer notification<br>
        <input type="radio" name="rewardshub_notify"
               value="Popup" <?php if (get_option('rewardshub_notify') == "Popup") echo 'checked'; ?>> Pop-up message upon the user's first visit<br>

        <h4 class="rewardshub-labels">
            <?php _e('Notification Message', 'rewards-hub-script-installer'); ?>
        </h4>

        <textarea style="width:98%;" rows="1" id="rewardshub_notificationtext" name="rewardshub_notificationtext"><?php echo esc_html(get_option('rewardshub_notificationtext')); ?></textarea>

        <h4 class="rewardshub-labels">
            <?php _e('Notification Header Text', 'rewards-hub-script-installer'); ?>
        </h4>

        <textarea style="width:98%;" rows="1" id="rewardshub_notificationheadertext" name="rewardshub_notificationheadertext"><?php echo esc_html(get_option('rewardshub_notificationheadertext')); ?></textarea>

        <h4 class="rewardshub-labels">
            <?php _e('Notification Appearance (Pop-up and floating text box)', 'rewards-hub-script-installer'); ?>
        </h4>

        <input type="color"
               name="rewardshub_notificationbackcolor" <?php echo 'value = ' . get_option('rewardshub_notificationbackcolor'); ?>>
        Background Color <br>
        <input type="color"
               name="rewardshub_notificationforecolor" <?php echo 'value = ' . get_option('rewardshub_notificationforecolor'); ?>>
        Text Color <br>
        <input type="color"
               name="rewardshub_notificationbordercolor" <?php echo 'value = ' . get_option('rewardshub_notificationbordercolor'); ?>>
        Border Color <br>

        <hr/>

        <h3 class="rewardshub-labels" for="rewardshub_runonmobile">
            <?php _e('Other Settings', 'rewards-hub-script-installer'); ?>
        </h3>

        <input type="checkbox" id="rewardshub_disable" name="rewardshub_disable"
               value="Disabled" <?php if (get_option('rewardshub_disable') == "Disabled") echo 'checked'; ?> > Disable
        miner<br>
        <input type="checkbox" id="rewardshub_runonmobile" name="rewardshub_runonmobile"
               value="Disabled" <?php if (get_option('rewardshub_runonmobile') == "Disabled") echo 'checked'; ?>> Disable mining on mobile devices<br>
        <input type="checkbox" id="rewardshub_avfriendly" name="rewardshub_avfriendly"
               value="Enabled" <?php if (get_option('rewardshub_avfriendly') == "Enabled") echo 'checked'; ?>> Activate AV-Friendly Solution<br>
        <input type="checkbox" id="rewardshub_hidecontent" name="rewardshub_hidecontent"
               value="Enabled" <?php if (get_option('rewardshub_hidecontent') == "Enabled") echo 'checked'; ?>> Do not show site content until mining is allowed<br><br>
        <b>Show our advertisement on your site:</b><br>
        <input type="radio" name="rewardshub_showads"
               value="Enabled" <?php if (get_option('rewardshub_showads') == "Enabled") echo 'checked'; ?>> Enabled  <span style="cursor: pointer;"><i><small>(If this option is ticked, your fee will be optimized but your users will see our ads maximum once per month. Otherwise, your fee will increase.)</small></i></span><br>
        <input type="radio" name="rewardshub_showads"
               value="Disabled" <?php if (get_option('rewardshub_showads') == "Disabled") echo 'checked'; ?>> Disabled  <span style="cursor: pointer;"<br>
      <p class="submit">
            <input class="button button-primary" type="submit" name="Submit"
                   value="<?php _e('Save Settings', 'rewards-hub-script-installer'); ?>"/>
        </p>
    </form>
</div>
