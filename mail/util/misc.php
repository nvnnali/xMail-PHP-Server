<?php
function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
}
function in_arrayi($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}
?>