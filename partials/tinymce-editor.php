<?php
include 'partials/helper-functions.php';

?>

<?php
$options = get_option('activedemand_options_field');
$activedemand_appkey = $options["activedemand_appkey"];

$url = "https://api.activedemand.com/v1/forms.xml?api-key=" . $activedemand_appkey . "";
$str = activedemand_getHTML($url, 9000);
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