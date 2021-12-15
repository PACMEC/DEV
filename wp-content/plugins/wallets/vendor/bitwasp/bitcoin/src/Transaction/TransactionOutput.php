<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class TransactionOutput extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializable implements \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
{
    /**
     * @var int
     */
    private $value;
    /**
     * @var ScriptInterface
     */
    private $script;
    /**
     * Initialize class
     *
     * @param int $value
     * @param ScriptInterface $script
     */
    public function __construct(int $value, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script)
    {
        if ($value < 0) {
            throw new \RuntimeException('Transaction output value cannot be negative');
        }
        $this->value = $value;
        $this->script = $script;
    }
    /**
     * {@inheritdoc}
     * @see TransactionOutputInterface::getValue()
     */
    public function getValue() : int
    {
        return $this->value;
    }
    /**
     * {@inheritdoc}
     * @see TransactionOutputInterface::getScript()
     */
    public function getScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->script;
    }
    /**
     * {@inheritdoc}
     * @see TransactionOutputInterface::equals()
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output) : bool
    {
        $script = $this->script->equals($output->getScript());
        if (!$script) {
            return \false;
        }
        return \gmp_cmp($this->value, $output->getValue()) === 0;
    }
    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\SerializableInterface::getBuffer()
     */
    public function getBuffer() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        return (new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Serializer\Transaction\TransactionOutputSerializer())->serialize($this);
    }
}
