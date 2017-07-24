<?php

require_once "views/FGRegistrationView.php";

class FGRegistration extends StdClass {

    public $view;

    function __construct()
    {
        $this->view = new FGRegistrationView();
    }

    function addRegistrationActions()
    {

        add_action('register_form', array($this->view, 'registrationFormFields'));
        add_filter('registration_errors', array($this, 'registrationValidation'), 10, 3);
        add_filter('user_register', array($this, 'registrationSaveFields'), 10, 3);

        add_action('show_user_profile', array($this->view, 'extraProfileFields'));
        add_action('edit_user_profile', array($this->view, 'extraProfileFields'));
        add_action('personal_options_update', array($this, 'saveExtraProfileFields'));
        add_action('edit_user_profile_update', array($this, 'saveExtraProfileFields'));

    }


    function registrationValidation( $errors, $sanitized_user_login, $user_email ) {

        if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
            $errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', FG_LANG ) );
        }
        if ( empty( $_POST['last_name'] ) || ! empty( $_POST['last_name'] ) && trim( $_POST['last_name'] ) == '' ) {
            $errors->add( 'last_name_error', __( '<strong>ERROR</strong>: You must include a last name.', FG_LANG ) );
        }
        if ( empty( $_POST['g-recaptcha-response'] ) || ! empty( $_POST['g-recaptcha-response'] ) && trim( $_POST['g-recaptcha-response'] ) == '' ) {
            $errors->add( 'recaptcha_error', __( '<strong>ERROR</strong>: You need verify that you are not robot.', FG_LANG ) );
        }

        return $errors;
    }

    function registrationSaveFields( $user_id ) {
        if ( ! empty( $_POST['first_name'] ) ) {
            update_user_meta( $user_id, 'first_name', trim( $_POST['first_name'] ) );
        }
        if ( ! empty( $_POST['last_name'] ) ) {
            update_user_meta( $user_id, 'last_name', trim( $_POST['last_name'] ) );
        }
        if ( ! empty( $_POST['sex'] ) ) {
            update_user_meta( $user_id, 'sex', trim( $_POST['sex'] ) );
        }
        if ( ! empty( $_POST['age'] ) ) {
            update_user_meta( $user_id, 'age', trim( $_POST['age'] ) );
        }
        if ( ! empty( $_POST['city'] ) ) {
            update_user_meta( $user_id, 'city', trim( $_POST['city'] ) );
        }
    }

    function saveExtraProfileFields( $user_id ) {

        if ( !current_user_can( 'edit_user', $user_id ) )
            return false;

        update_user_meta( $user_id, 'sex', $_POST['sex'] );
        update_user_meta( $user_id, 'age', $_POST['age'] );
        update_user_meta( $user_id, 'city', $_POST['city'] );
    }

}
