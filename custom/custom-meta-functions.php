<?php

function metapress_save_custom_metabox_data( $post_id, $save_nonce, $save_nonce_name ) {
    if ( ! isset( $_POST[$save_nonce] ) ) {
        return false;
    }
    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST[$save_nonce], $save_nonce_name ) ) {
        return false;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return false;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return false;
    }
    
    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return false;
    }

    return true;
}
