<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools;

class TemplateFactory
{
    /**
     * @var \BitWasp\Buffertools\Template
     */
    private $template;
    /**
     * @var TypeFactoryInterface
     */
    private $types;
    /**
     * TemplateFactory constructor.
     * @param Template|null $template
     * @param TypeFactoryInterface|null $typeFactory
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template $template = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TypeFactoryInterface $typeFactory = null)
    {
        $this->template = $template ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Template();
        $this->types = $typeFactory ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\CachingTypeFactory();
    }
    /**
     * Return the Template as it stands.
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
    /**
     * Add a Uint8 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint8()
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint8 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint8le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a Uint16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint16() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint16le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a Uint32 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint32() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint32 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint32le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a Uint64 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint64() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint64 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint64le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a Uint128 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint128() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint128 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint128le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a Uint256 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint256() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Uint256 serializer to the template
     *
     * @return TemplateFactory
     */
    public function uint256le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int8 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int8() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int8 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int8le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int16() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int16 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int16le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int32 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int32() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int serializer to the template
     *
     * @return TemplateFactory
     */
    public function int32le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int64 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int64() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int64 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int64le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int128 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int128() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int128 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int128le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a int256 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int256() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a little-endian Int256 serializer to the template
     *
     * @return TemplateFactory
     */
    public function int256le() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a VarInt serializer to the template
     *
     * @return TemplateFactory
     */
    public function varint() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a VarString serializer to the template
     *
     * @return TemplateFactory
     */
    public function varstring() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}());
        return $this;
    }
    /**
     * Add a byte string serializer to the template. This serializer requires a length to
     * pad/truncate to.
     *
     * @param  int $length
     * @return TemplateFactory
     */
    public function bytestring(int $length) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}($length));
        return $this;
    }
    /**
     * Add a little-endian byte string serializer to the template. This serializer requires
     * a length to pad/truncate to.
     *
     * @param  int $length
     * @return TemplateFactory
     */
    public function bytestringle(int $length) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}($length));
        return $this;
    }
    /**
     * Add a vector serializer to the template. A $readHandler must be provided if the
     * template will be used to deserialize a vector, since it's contents are not known.
     *
     * The $readHandler should operate on the parser reference, reading the bytes for each
     * item in the collection.
     *
     * @param  callable $readHandler
     * @return TemplateFactory
     */
    public function vector(callable $readHandler) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\TemplateFactory
    {
        $this->template->addItem($this->types->{__FUNCTION__}($readHandler));
        return $this;
    }
}
