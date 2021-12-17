<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\ScriptHashAddress;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\P2shScriptException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class P2shScript extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Script
{
    /**
     * @var \BitWasp\Buffertools\BufferInterface
     */
    protected $scriptHash;
    /**
     * @var ScriptInterface
     */
    private $outputScript;
    /**
     * @var ScriptHashAddress
     */
    private $address;
    /**
     * P2shScript constructor.
     * @param ScriptInterface $script
     * @param Opcodes|null $opcodes
     * @throws P2shScriptException
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null)
    {
        if ($script instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\WitnessScript) {
            $script = $script->getOutputScript();
        } else {
            if ($script instanceof self) {
                throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\P2shScriptException("Cannot nest P2SH scripts.");
            }
        }
        parent::__construct($script->getBuffer(), $opcodes);
        $this->scriptHash = $script->getScriptHash();
        $this->outputScript = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptFactory::scriptPubKey()->p2sh($this->scriptHash);
        $this->address = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\ScriptHashAddress($this->scriptHash);
    }
    /**
     * @throws P2shScriptException
     */
    public function getWitnessScriptHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface
    {
        throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\P2shScriptException("Cannot compute witness-script-hash for a P2shScript");
    }
    /**
     * @return ScriptInterface
     */
    public function getOutputScript() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return $this->outputScript;
    }
    /**
     * @return ScriptHashAddress
     */
    public function getAddress() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\ScriptHashAddress
    {
        return $this->address;
    }
}
