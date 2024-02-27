<?php
namespace PSON;

abstract class Pair {
    protected $encoder;
    protected $decoder;

    public function encode($json) {
        return $this->encoder->encode($json);
    }    

    public function toArrayBuffer($json) {
        return $this->encoder->encode($json)->toArrayBuffer();
    }

    public function toBuffer($json) {
        return $this->encoder->encode($json)->toBuffer();
    }

    public function decode($pson) {
        return $this->decoder->decode($pson);
    }
}
