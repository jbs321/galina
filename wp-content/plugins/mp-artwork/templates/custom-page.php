<?php 

if (has_action(MP_Artwork_Plugin_Instance()->get_prefix().'front_page')) {
    do_action(MP_Artwork_Plugin_Instance()->get_prefix().'front_page');
}