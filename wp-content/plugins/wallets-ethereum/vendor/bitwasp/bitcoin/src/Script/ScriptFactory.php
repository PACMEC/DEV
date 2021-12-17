<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\BitcoinConsensus;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\ConsensusInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\NativeConsensus;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\OutputScriptFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\ScriptCreator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class ScriptFactory
{
    /**
     * @var OutputScriptFactory
     */
    private static $outputScriptFactory = null;
    /**
     * @param string $string
     * @return ScriptInterface
     * @throws \Exception
     */
    public static function fromHex(string $string) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return self::fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer::hex($string));
    }
    /**
     * @param BufferInterface $buffer
     * @param Opcodes|null $opcodes
     * @param Math|null $math
     * @return ScriptInterface
     */
    public static function fromBuffer(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return self::create($buffer, $opcodes, $math)->getScript();
    }
    /**
     * @param BufferInterface|null $buffer
     * @param Opcodes|null $opcodes
     * @param Math|null $math
     * @return ScriptCreator
     */
    public static function create(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $buffer = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes $opcodes = null, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Math\Math $math = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\ScriptCreator
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\ScriptCreator($math ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getMath(), $opcodes ?: new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes(), $buffer);
    }
    /**
     * Create a script consisting only of push-data operations.
     * Suitable for a scriptSig.
     *
     * @param BufferInterface[] $buffers
     * @return ScriptInterface
     */
    public static function pushAll(array $buffers) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return self::sequence(\array_map(function ($buffer) {
            if (!$buffer instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface) {
                throw new \RuntimeException('Script contained a non-push opcode');
            }
            $size = $buffer->getSize();
            if ($size === 0) {
                return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_0;
            }
            $first = \ord($buffer->getBinary()[0]);
            if ($size === 1 && $first >= 1 && $first <= 16) {
                return \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\encodeOpN($first);
            } else {
                return $buffer;
            }
        }, $buffers));
    }
    /**
     * @param int[]|\BitWasp\Bitcoin\Script\Interpreter\Number[]|BufferInterface[] $sequence
     * @return ScriptInterface
     */
    public static function sequence(array $sequence) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        return self::create()->sequence($sequence)->getScript();
    }
    /**
     * @param Operation[] $operations
     * @return ScriptInterface
     */
    public static function fromOperations(array $operations) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface
    {
        $sequence = [];
        foreach ($operations as $operation) {
            if (!$operation instanceof \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Parser\Operation) {
                throw new \RuntimeException("Invalid input to fromOperations");
            }
            $sequence[] = $operation->encode();
        }
        return self::sequence($sequence);
    }
    /**
     * @return OutputScriptFactory
     */
    public static function scriptPubKey() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\OutputScriptFactory
    {
        if (self::$outputScriptFactory === null) {
            self::$outputScriptFactory = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Factory\OutputScriptFactory();
        }
        return self::$outputScriptFactory;
    }
    /**
     * @param EcAdapterInterface|null $ecAdapter
     * @return NativeConsensus
     */
    public static function getNativeConsensus(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\NativeConsensus
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\NativeConsensus($ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter());
    }
    /**
     * @return BitcoinConsensus
     */
    public static function getBitcoinConsensus() : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\BitcoinConsensus
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\BitcoinConsensus();
    }
    /**
     * @param EcAdapterInterface|null $ecAdapter
     * @return ConsensusInterface
     */
    public static function consensus(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Consensus\ConsensusInterface
    {
        if (\extension_loaded('bitcoinconsensus')) {
            return self::getBitcoinConsensus();
        } else {
            return self::getNativeConsensus($ecAdapter);
        }
    }
}
