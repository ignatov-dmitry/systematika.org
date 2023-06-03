<?php



function emojiCode($src=''){
    $replaced = var_dump(preg_replace("/\\\\u([0-9A-F]{1,4})/i", "&#x$1;", $src));
    $result1 = var_dump(mb_convert_encoding($replaced, "UTF-16", "HTML-ENTITIES"));
    $result = var_dump(mb_convert_encoding($result1, 'utf-8', 'utf-16'));
    return $result;
}



$text = '\ud83d\udc4c Мои тренинги';

var_dump(emojiCode($text));