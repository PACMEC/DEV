<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class BlockHeaderFactory
{
    /**
     * @param string $string
     * @return BlockHeaderInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public static function fromHex(string $string) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        return self::fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($string));
    }
    /**
     * @param BufferInterface $buffer
     * @return BlockHeaderInterface
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     */
    public static function fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Block\BlockHeaderInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer())->parse($buffer);
    }
}
