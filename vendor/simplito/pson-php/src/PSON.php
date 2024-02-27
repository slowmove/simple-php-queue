<?php
namespace PSON;

class PSON {
    public static function exclude($obj) {
        if (is_object($obj)) {
            $obj->_PSON_EXCL_ = true;
        }
    }
}



