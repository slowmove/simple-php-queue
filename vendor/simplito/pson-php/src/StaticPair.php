<?php
namespace PSON;

class StaticPair extends Pair {
    public function __construct($dict = array(), $options = array()) {
        $this->encoder = new Encoder($dict, false, $options);
        $this->decoder = new Decoder($dict, false, $options);
    }
}
