<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
class OutputMutator
{
    /**
     * @var TransactionOutputInterface
     */
    private $output;
    /**
     * @param TransactionOutputInterface $output
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface $output)
    {
        $this->output = $output;
    }
    /**
     * @return TransactionOutputInterface
     */
    public function done() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutputInterface
    {
        return $this->output;
    }
    /**
     * @param array $array
     * @return $this
     */
    private function replace(array $array)
    {
        $this->output = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionOutput(\array_key_exists('value', $array) ? $array['value'] : $this->output->getValue(), \array_key_exists('script', $array) ? $array['script'] : $this->output->getScript());
        return $this;
    }
    /**
     * @param int $value
     * @return $this
     */
    public function value(int $value)
    {
        return $this->replace(array('value' => $value));
    }
    /**
     * @param ScriptInterface $script
     * @return $this
     */
    public function script(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script)
    {
        return $this->replace(array('script' => $script));
    }
}
