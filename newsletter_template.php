<?php
/**
 * Created by PhpStorm.
 * User: matteoerrera
 * Date: 22/08/18
 * Time: 15:11
 */

if(strpos($content, $match) !== false):
    $truncated_content = substr($content, 0, strpos($content, $match));
?>
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php')?>";
</script>
<!--- Post Truncated By Sub-To-Read WP Plugin ---->
<?php if($_COOKIE['str_is_yet_registered'] == true):
    echo $content;
else:
    echo $truncated_content;
    ?>
    <div class="sub-continue">
        <div class="shadow"></div>
    </div>
    <section class="sub-to-read-cta">
        <h2><?php echo __("title", "strwp")?></h2>
        <p><?php echo __("subtitle", "strwp")?></p>
        <form class="form" onsubmit="return sendData()">
            <div class="form-group">
                <label for="name"><?php echo __("your_name", "strwp")?></label>
                <input placeholder="<?php echo __("name_placeholder", "strwp")?>" id="name" type="text" required>
            </div>
            <div class="form-group">
                <label for="name"><?php echo __("your_email", "strwp")?></label>
                <input placeholder="<?php echo __("email_placeholder", "strwp")?>" id="email" type="email" required>
            </div>
            <div class="form-group">
                <label for="tos"><input id="tos" type="checkbox" required> <?php echo __("tos_text", "strwp")?></label>
            </div>
            <input name="post_id" id="postid" type="hidden" value="<?php the_id() ?>">
            <input id="str_submit" type="submit" class="btn btn-primary" value="<?php echo __("submit_button_text", "strwp")?>">
        </form>
    </section>

<?php
endif;
else:
    echo $content;
endif;
?>

