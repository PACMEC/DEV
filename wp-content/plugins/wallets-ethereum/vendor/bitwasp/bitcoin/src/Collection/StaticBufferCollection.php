<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Collection;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
/**
 * @deprecated v2.0.0
 */
class StaticBufferCollection extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Collection\StaticCollection
{
    /**
     * @var BufferInterface[]
     */
    protected $set = [];
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * StaticBufferCollection constructor.
     * @param BufferInterface ...$values
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface ...$values)
    {
        $this->set = $values;
    }
    /**
     * @return BufferInterface
     */
    public function bottom() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return parent::bottom();
    }
    /**
     * @return BufferInterface
     */
    public function top() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return parent::top();
    }
    /**
     * @return BufferInterface
     */
    public function current() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return $this->set[$this->position];
    }
    /**
     * @param int $offset
     * @return BufferInterface
     */
    public function offsetGet($offset)
    {
        if (!\array_key_exists($offset, $this->set)) {
            throw new \OutOfRangeException('No offset found');
        }
        return $this->set[$offset];
    }
}
