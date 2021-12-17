<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\ByteString;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int128;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int16;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int256;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int32;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int64;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int8;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint8;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint16;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint32;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint64;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint128;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint256;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarString;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Vector;
class TypeFactory implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TypeFactoryInterface
{
    /**
     * Add a Uint8 serializer to the template
     *
     * @return Uint8
     */
    public function uint8() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint8
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint8(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint8 serializer to the template
     *
     * @return Uint8
     */
    public function uint8le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint8
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint8(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a Uint16 serializer to the template
     *
     * @return Uint16
     */
    public function uint16() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint16
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint16(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint16 serializer to the template
     *
     * @return Uint16
     */
    public function uint16le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint16
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint16(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a Uint32 serializer to the template
     *
     * @return Uint32
     */
    public function uint32() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint32
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint32(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint32 serializer to the template
     *
     * @return Uint32
     */
    public function uint32le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint32
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint32(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a Uint64 serializer to the template
     *
     * @return Uint64
     */
    public function uint64() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint64
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint64(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint64 serializer to the template
     *
     * @return Uint64
     */
    public function uint64le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint64
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint64(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a Uint128 serializer to the template
     *
     * @return Uint128
     */
    public function uint128() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint128
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint128(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint128 serializer to the template
     *
     * @return Uint128
     */
    public function uint128le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint128
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint128(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a Uint256 serializer to the template
     *
     * @return Uint256
     */
    public function uint256() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint256
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint256(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Uint256 serializer to the template
     *
     * @return Uint256
     */
    public function uint256le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint256
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Uint256(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int8 serializer to the template
     *
     * @return Int8
     */
    public function int8() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int8
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int8(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int8 serializer to the template
     *
     * @return Int8
     */
    public function int8le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int8
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int8(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int16 serializer to the template
     *
     * @return Int16
     */
    public function int16() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int16
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int16(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int16 serializer to the template
     *
     * @return Int16
     */
    public function int16le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int16
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int16(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int32 serializer to the template
     *
     * @return Int32
     */
    public function int32() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int32
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int32(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int serializer to the template
     *
     * @return Int32
     */
    public function int32le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int32
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int32(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int64 serializer to the template
     *
     * @return Int64
     */
    public function int64() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int64
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int64(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int64 serializer to the template
     *
     * @return Int64
     */
    public function int64le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int64
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int64(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int128 serializer to the template
     *
     * @return Int128
     */
    public function int128() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int128
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int128(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int128 serializer to the template
     *
     * @return Int128
     */
    public function int128le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int128
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int128(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a int256 serializer to the template
     *
     * @return Int256
     */
    public function int256() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int256
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int256(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian Int256 serializer to the template
     *
     * @return Int256
     */
    public function int256le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int256
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Int256(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a VarInt serializer to the template
     *
     * @return VarInt
     */
    public function varint() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt();
    }
    /**
     * Add a VarString serializer to the template
     *
     * @return VarString
     */
    public function varstring() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarString
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarString(new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\VarInt());
    }
    /**
     * Add a byte string serializer to the template. This serializer requires a length to
     * pad/truncate to.
     *
     * @param  int $length
     * @return ByteString
     */
    public function bytestring(int $length) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\ByteString
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\ByteString($length, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::BE);
    }
    /**
     * Add a little-endian byte string serializer to the template. This serializer requires
     * a length to pad/truncate to.
     *
     * @param  int $length
     * @return ByteString
     */
    public function bytestringle(int $length) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\ByteString
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\ByteString($length, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\ByteOrder::LE);
    }
    /**
     * Add a vector serializer to the template. A $readHandler must be provided if the
     * template will be used to deserialize a vector, since it's contents are not known.
     *
     * The $readHandler should operate on the parser reference, reading the bytes for each
     * item in the collection.
     *
     * @param  callable $readHandler
     * @return Vector
     */
    public function vector(callable $readHandler) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Vector
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types\Vector($this->varint(), $readHandler);
    }
}
