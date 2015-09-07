<?php

function filter_password($msg, $pw) {
    return str_replace($pw, '', $msg);
}

