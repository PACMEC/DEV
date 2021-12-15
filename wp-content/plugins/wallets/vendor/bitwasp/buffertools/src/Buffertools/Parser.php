<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange;
class Parser
{
    /**
     * @var string
     */
    private $string;
    /**
     * @var int
     */
    private $size = 0;
    /**
     * @var int
     */
    private $position = 0;
    /**
     * Instantiate class, optionally taking Buffer or HEX.
     *
     * @param null|string|BufferInterface $input
     */
    public function __construct($input = null)
    {
        if (null === $input) {
            $input = '';
        }
        if (\is_string($input)) {
            $bin = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($input, null)->getBinary();
        } elseif ($input instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
            $bin = $input->getBinary();
        } else {
            throw new \InvalidArgumentException("Invalid argument to Parser");
        }
        $this->string = $bin;
        $this->position = 0;
        $this->size = \strlen($this->string);
    }
    /**
     * Get the position pointer of the parser - ie, how many bytes from 0
     *
     * @return int
     */
    public function getPosition() : int
    {
        return $this->position;
    }
    /**
     * Get the total size of the parser
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    /**
     * Parse $bytes bytes from the string, and return the obtained buffer
     *
     * @param  int $numBytes
     * @param  bool $flipBytes
     * @return BufferInterface
     * @throws \Exception
     */
    public function readBytes(int $numBytes, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        $string = \substr($this->string, $this->getPosition(), $numBytes);
        $length = \strlen($string);
        if ($length === 0) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Could not parse string of required length (empty)');
        } elseif ($length < $numBytes) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Exceptions\ParserOutOfRange('Could not parse string of required length (too short)');
        }
        $this->position += $numBytes;
        if ($flipBytes) {
            $string = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::flipBytes($string);
            /** @var string $string */
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($string, $length);
    }
    /**
     * Write $data as $bytes bytes. Can be flipped if needed.
     *
     * @param  integer $numBytes - number of bytes to write
     * @param  SerializableInterface|BufferInterface|string $data - buffer, serializable or hex
     * @param  bool $flipBytes
     * @return Parser
     */
    public function writeBytes(int $numBytes, $data, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        // Treat $data to ensure it's a buffer, with the correct size
        if ($data instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\SerializableInterface) {
            $data = $data->getBuffer();
        }
        if (\is_string($data)) {
            // Convert to a buffer
            $data = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($data, $numBytes);
        } else {
            if (!$data instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
                throw new \RuntimeException('Invalid data passed to Parser::writeBytes');
            }
        }
        $this->writeBuffer($numBytes, $data, $flipBytes);
        return $this;
    }
    /**
     * Write $data as $bytes bytes. Can be flipped if needed.
     *
     * @param  integer $numBytes
     * @param  string $data
     * @param  bool $flipBytes
     * @return Parser
     */
    public function writeRawBinary(int $numBytes, string $data, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        return $this->writeBuffer($numBytes, new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($data, $numBytes), $flipBytes);
    }
    /**
     * @param BufferInterface $buffer
     * @param bool $flipBytes
     * @param int $numBytes
     * @return Parser
     */
    public function writeBuffer(int $numBytes, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        // only create a new buffer if the size does not match
        if ($buffer->getSize() != $numBytes) {
            $buffer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($buffer->getBinary(), $numBytes);
        }
        $this->appendBuffer($buffer, $flipBytes);
        return $this;
    }
    /**
     * @param BufferInterface $buffer
     * @param bool $flipBytes
     * @return Parser
     */
    public function appendBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        $this->appendBinary($buffer->getBinary(), $flipBytes);
        return $this;
    }
    /**
     * @param string $binary
     * @param bool $flipBytes
     * @return Parser
     */
    public function appendBinary(string $binary, bool $flipBytes = \false) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        if ($flipBytes) {
            $binary = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::flipBytes($binary);
        }
        $this->string .= $binary;
        $this->size += \strlen($binary);
        return $this;
    }
    /**
     * Take an array containing serializable objects.
     * @param array<mixed|SerializableInterface|BufferInterface> $serializable
     * @return Parser
     */
    public function writeArray(array $serializable) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser
    {
        $parser = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Parser(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffertools::numToVarInt(\count($serializable)));
        foreach ($serializable as $object) {
            if ($object instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\SerializableInterface) {
                $object = $object->getBuffer();
            }
            if ($object instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
                $parser->writeBytes($object->getSize(), $object);
            } else {
                throw new \RuntimeException('Input to writeArray must be Buffer[], or SerializableInterface[]');
            }
        }
        $this->string .= $parser->getBuffer()->getBinary();
        $this->size += $parser->getSize();
        return $this;
    }
    /**
     * Return the string as a buffer
     *
     * @return BufferInterface
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($this->string, null);
    }
}
