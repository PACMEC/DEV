<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Parser;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface ScriptInterface extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\SerializableInterface
{
    /**
     * @return BufferInterface
     */
    public function getScriptHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @return BufferInterface
     */
    public function getWitnessScriptHash() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
    /**
     * @return Parser
     */
    public function getScriptParser() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Parser;
    /**
     * @return Opcodes
     */
    public function getOpcodes() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
    /**
     * Returns boolean indicating whether script
     * was push only. If true, $ops is populated
     * with the contained buffers
     * @param array $ops
     * @return bool
     */
    public function isPushOnly(array &$ops = null) : bool;
    /**
     * @param WitnessProgram|null $witness
     * @return bool
     */
    public function isWitness(&$witness) : bool;
    /**
     * @param BufferInterface $scriptHash
     * @return bool
     */
    public function isP2SH(&$scriptHash) : bool;
    /**
     * @param bool $accurate
     * @return int
     */
    public function countSigOps(bool $accurate = \true) : int;
    /**
     * @param ScriptInterface $scriptSig
     * @return int
     */
    public function countP2shSigOps(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptSig) : int;
    /**
     * @param ScriptInterface $scriptSig
     * @param ScriptWitnessInterface $witness
     * @param int $flags
     * @return int
     */
    public function countWitnessSigOps(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $scriptSig, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptWitnessInterface $witness, int $flags) : int;
    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function equals(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : bool;
}
