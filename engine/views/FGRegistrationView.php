<?php

class FGRegistrationView extends StdClass
{

    function registrationFormFields() {

        $first_name = ( ! empty( $_POST['first_name'] ) ) ? trim( $_POST['first_name'] ) : '';

        ?>
        <p>
            <label for="first_name"><?php _e( 'First Name', FG_LANG ) ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr( wp_unslash( $first_name ) ); ?>" size="25" />
            </label>
        </p>
        <?php

        $last_name = ( ! empty( $_POST['last_name'] ) ) ? trim( $_POST['last_name'] ) : '';

        ?>
        <p>
            <label for="last_name"><?php _e( 'Last Name', FG_LANG ) ?><br />
                <input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr( wp_unslash( $last_name ) ); ?>" size="25" />
            </label>
        </p>
        <?php

        $sex = ( ! empty( $_POST['sex'] ) ) ? trim( $_POST['sex'] ) : 'M';

        $variants = ['M', 'F', 'M + F'];
        ?>
        <p>
            <label for="sex"><?php _e( 'Sex', FG_LANG ) ?><br />
                <select name="sex" id="sex" class="input">
                    <?php foreach($variants as $variant){?>
                    <option value="<?php echo $variant;?>" <?php selected($variant, $sex);?>><?php echo $variant;?></option>
                    <?php }?>
                </select>
            </label>
        </p>
        <?php

        $age = ( ! empty( $_POST['age'] ) ) ? intval(trim( $_POST['age'] )) : 21;

        ?>
        <p>
            <label for="age"><?php _e( 'Age', FG_LANG ) ?><br />
                <input type="number" name="age" id="age" min="21" max="121" class="input" value="<?php echo esc_attr( wp_unslash( $age ) ); ?>" size="25" />
            </label>
        </p>
        <?php

        $city = ( ! empty( $_POST['city'] ) ) ? intval(trim( $_POST['city'] )) : 'NYC';

        $variants = fg_get_known_and_saved_cities();
        ?>
        <p>
            <label for="city"><?php _e( 'City', FG_LANG ) ?><br />
                <select name="city" id="city" class="input">
                    <?php foreach($variants as $variant){?>
                        <option value="<?php echo $variant;?>" <?php selected($variant, $city);?>><?php echo $variant;?></option>
                    <?php }?>
                </select>
            </label>
        </p>

        <div id='captcha-validation'></div>

        <script type='text/javascript'>

            var verifyCallback = function() {
                var captchaBlock = jQuery('#captcha-validation');
                if(grecaptcha.getResponse().trim() == ''){
                    e.preventDefault();
                    captchaBlock.addClass('need-captcha-validation');
                }
                else{
                    captchaBlock.removeClass('need-captcha-validation');
                }
            };

            var onloadCallback = function() {
                grecaptcha.render('captcha-validation', {
                    'sitekey' : '6LeKLSMTAAAAAC79RE1UhbtMqXfOnTFWxNraW9pc',
                    'callback' : verifyCallback,
                    'theme' : 'light'
                });
            };

        </script>
        <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>

        <?php
    }

    function extraProfileFields( $user ) { ?>

        <h3><?php _e( 'Extra profile information', FG_LANG ) ?></h3>

        <table class="form-table">

            <?php

            $sex = esc_attr( get_user_meta( 'sex', $user->ID ) );

            $variants = ['M', 'F', 'M + F'];
            ?>
            <tr>
                <th><label for="sex"><?php _e( 'Sex', FG_LANG ) ?></label></th>
                <td>
                    <select name="sex" id="sex" class="input">
                        <?php foreach($variants as $variant){?>
                            <option value="<?php echo esc_attr( wp_unslash( $variant ) );?>" <?php selected($variant, $sex);?>><?php echo $variant;?></option>
                        <?php }?>
                    </select>
                </td>
            </tr>
            <?php

            $age = esc_attr( get_user_meta( 'age', $user->ID ) );

            ?>
            <tr>
                <th><label for="age"><?php _e( 'Age', FG_LANG ) ?></label></th>
                <td>
                    <input type="number" name="age" id="age" min="21" max="121" class="input" value="<?php echo esc_attr( wp_unslash( $age ) ); ?>" size="25" />
                </td>
            </tr>
            <?php

            $city = ( ! empty( $_POST['city'] ) ) ? intval(trim( $_POST['city'] )) : 'NYC';

            $variants = fg_get_known_and_saved_cities();
            ?>
            <tr>
                <th><label for="city"><?php _e( 'City', FG_LANG ) ?></label></th>
                <td>
                    <select name="city" id="city" class="input">
                        <?php foreach($variants as $variant){?>
                            <option value="<?php echo esc_attr( wp_unslash( $variant ) );?>" <?php selected($variant, $city);?>><?php echo $variant;?></option>
                        <?php }?>
                    </select>
                </td>
            </tr>

        </table>
    <?php }

}
