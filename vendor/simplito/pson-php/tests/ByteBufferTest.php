<?php

use PSON\ByteBuffer;

class ByteBufferTests extends PHPUnit_Framework_TestCase {

    public function test_should_constructor_work(){
        $b = new ByteBuffer();
        $this->assertEquals($b->capacity(), ByteBuffer::DEFAULT_CAPACITY);
    }

    public function test_cstring() {
        $b = new ByteBuffer();
        $this->assertEquals(6, $b->writeCString("12345", 0));
        $b->skip(6);
        $b->writeCString("end");
        $b->flip();
        $this->assertEquals("12345", $b->readCString());
        $this->assertEquals("end", $b->readCString());
    }

    public function test_varint32() {
        $b = new ByteBuffer();
        $b->writeVarint32(1);
        $b->flip();
        $this->assertEquals(1, $b->readVarint32());
    }
    public function test_endian() {
        $b = new ByteBuffer();
        $i = 0x0102;
        $le= "0201";
        $be= "0102";
        $b->LE();
        $b->writeUint16($i);
        $b->flip();
        $this->assertEquals($i, $b->readUint16(0));
        $this->assertEquals($le, $b->toHex());
        $b->BE();
        $b->writeUint16($i, 0);
        $this->assertEquals($i, $b->readUint16(0));
        $this->assertEquals($be, $b->toHex());

        $b->reset();
        $i = 0x01020304;
        $le= "04030201";
        $be= "01020304";
        $b->LE();
        $b->writeUint32($i);
        $b->flip();
        $this->assertEquals($i, $b->readUint32(0));
        $this->assertEquals($le, $b->toHex());
        $b->BE();
        $b->writeUint32($i, 0);
        $this->assertEquals($i, $b->readUint32(0));
        $this->assertEquals($be, $b->toHex());

        $b->reset();
        if (PHP_INT_SIZE == 4) {
            $i = 0x0012030405067800;
            $le= "0078060504031200";
            $be= "0012030405067800";
        } else {
            $i = 0x0102030405060708;
            $le= "0807060504030201";
            $be= "0102030405060708";
        }
        $b->LE();
        $b->writeUint64($i);
        $b->flip();
        $this->assertEquals($i, $b->readUint64(0));
        $this->assertEquals($le, $b->toHex());
        $b->BE();
        $b->writeUint64($i, 0);
        $this->assertEquals($i, $b->readUint64(0));
        $this->assertEquals($be, $b->toHex());

        $b = ByteBuffer::wrap("3f800000", "hex", ByteBuffer::BIG_ENDIAN);
        $this->assertEquals(1.0, $b->readFloat32());

        $b = ByteBuffer::wrap("0000803f", "hex", ByteBuffer::LITTLE_ENDIAN);
        $this->assertEquals(1.0, $b->readFloat32());

        $b = ByteBuffer::wrap("3ff0000000000000", "hex", ByteBuffer::BIG_ENDIAN);
        $this->assertEquals(1.0, $b->readFloat64());

        $b = ByteBuffer::wrap("000000000000f03f", "hex", ByteBuffer::LITTLE_ENDIAN);
        $this->assertEquals(1.0, $b->readFloat64());
    }
}
