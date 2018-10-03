<?php
namespace Indeed\Uap;

class RewriteDefaultWpAvatar
{
    private $metaKey = 'uap_avatar';

    public function __construct()
    {
        add_filter( 'get_avatar' , array($this, 'returnAvatar') , 2 , 5 );
    }

    public function returnAvatar($avatar='', $id_or_email='', $size, $default, $alt)
    {
        $user = false;
        if ( is_numeric( $id_or_email ) ) {
          $id = (int) $id_or_email;
          $user = get_user_by( 'id' , $id );
        } elseif ( is_object( $id_or_email ) ) {
          if ( ! empty( $id_or_email->user_id ) ) {
              $id = (int) $id_or_email->user_id;
              $user = get_user_by( 'id' , $id );
          }
        } else {
          $user = get_user_by( 'email', $id_or_email );
        }
        if ( $user && is_object( $user ) ) {

            if ( isset($user->data->ID) ) {
                $avatarData = get_user_meta($user->data->ID, $this->metaKey, TRUE);
                if (!empty($avatarData)){
                  if (strpos($avatarData, "http")===0){
                    $avatar_url = $avatarData;
                  } else {
                    $avatar_data = wp_get_attachment_image_src($avatarData, 'full');
                    if (!empty($avatar_data[0])){
                      $avatar_url = $avatar_data[0];
                    }
                  }
                }
                if (!empty($avatar_url)){
                    $avatar = $avatar_url;
                    $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
                }
            }
        }
        return $avatar;
    }


}
