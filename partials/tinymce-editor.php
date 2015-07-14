<?php
$options = get_option('activedemand_options_field');
$activedemand_appkey = $options["activedemand_appkey"];

$url = "https://api.activedemand.com/v1/forms.xml?api-key=" . $activedemand_appkey . "";


$ch = curl_init($url); // initialize curl with given url
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 9000); // max. seconds to execute
curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);//force IP4
$str = curl_exec($ch);
if (curl_exec($ch) === false) {
    echo 'ActiveDEMAND Web Form error: ' . curl_error($ch);
}

curl_close($ch);

$xml = simplexml_load_string($str);
?>
<div id="activedemand_editor" class="shortcode_editor" title="Insert ActiveDEMAND Web Form Shortcode"
     style="display:none;height:500px">
    <?php if ("" != $xml) { ?>
        <h3>Available ActiveDEMAND Web Forms:</h3><br/>
        <style scoped="scoped" type="text/css">
            div.ad-form-list {
            }

            div.ad-form-list ul li span {
                margin-left: 20px;
                font-size: 1.2em;
                font-weight: bold;
            }
        </style>
        <div class="ad-form-list">
            <ul>
                <?php
                foreach ($xml->children() as $child) {
                    echo "<li>";
                    echo "<input type='radio' name='form_id' value='";
                    echo $child->id;
                    echo "' />";
                    echo "<span>";
                    echo $child->name;
                    echo "</span>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>
    <?php } else { ?>
        <h2>No Web Forms Configured</h2>
        <p>To use the ActiveDEMAND web form shortcodes, you will first have to add some web forms to your account in
            ActiveDEMAND. Once you do have web forms configured, the available shortcodes will be displayed here.</p>
    <?php } ?>

</div>