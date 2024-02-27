<?php

class PSONTests extends PHPUnit_Framework_TestCase {

    public function test_encode_decode() {
        $pson = new PSON\StaticPair();
        $data = <<<EOT
{
    "hello": "world!",
    "time": 1234567890,
    "float": 0.01234,
    "boolean": true,
    "otherbool": false,
    "null": null,
    "obj": {
        "what": "that"
    },
    "arr": [1,2,3]
}
EOT;
        $obj  = json_decode($data);
        $obj->binary = PSON\ByteBuffer::wrap("binary\xffcon\x00tent");
        $enc  = $pson->encode( $obj );

        $this->assertEquals("f609fc0568656c6c6ffc06776f726c6421fc0474696d65f8a48bb09909fc05666c6f6174fbf60b76c3b645893ffc07626f6f6c65616ef1fc096f74686572626f6f6cf2fc046e756c6cf0fc036f626af601fc0477686174fc0474686174fc03617272f703020406fc0662696e617279ff0f62696e617279ff636f6e0074656e74", $enc->toHex());

        $obj2 = $pson->decode( $enc );

        $this->assertEquals($obj->binary->toBinary(), $obj2->binary->toBinary());
        unset($obj->binary);
        unset($obj2->binary);
        $this->assertJsonStringEqualsJsonString(json_encode($obj), json_encode($obj2));
    }

    public function test_encode_decode_with_dictionary() {
        $pson = new PSON\StaticPair(array("hello", "world!", "time", "float", "boolean", "otherbool", "null", "obj", "what", "that", "arr", "binary"));
        $data = <<<EOT
{
    "hello": "world!",
    "time": 1234567890,
    "float": 0.01234,
    "boolean": true,
    "otherbool": false,
    "null": null,
    "obj": {
        "what": "that"
    },
    "arr": [1,2,3]
}
EOT;
        $obj  = json_decode($data);
        $obj->binary = PSON\ByteBuffer::wrap("binary\xffcon\x00tent");

        $enc  = $pson->encode( $obj );

        $this->assertEquals("f609fe00fe01fe02f8a48bb09909fe03fbf60b76c3b645893ffe04f1fe05f2fe06f0fe07f601fe08fe09fe0af703020406fe0bff0f62696e617279ff636f6e0074656e74", $enc->toHex());

        $obj2 = $pson->decode( $enc );

        $this->assertEquals($obj->binary->toBinary(), $obj2->binary->toBinary());
        unset($obj->binary);
        unset($obj2->binary);
        $this->assertJsonStringEqualsJsonString(json_encode($obj), json_encode($obj2));
    }

}
