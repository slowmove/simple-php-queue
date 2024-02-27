<?php
namespace PSON;

class ProgressivePair extends Pair {
    public function __construct(array $dict = array(), array $options = array()) {
        $this->encoder = new Encoder($dict, true, $options);
        $this->decoder = new Decoder($dict, true, $options);
    }

    public function exclude($obj) {
        PSON::exclude($obj);
    }
}
