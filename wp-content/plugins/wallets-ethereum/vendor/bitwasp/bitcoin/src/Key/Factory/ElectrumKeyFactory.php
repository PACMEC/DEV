<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Electrum\ElectrumWordListInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\MnemonicFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class ElectrumKeyFactory
{
    /**
     * @var EcAdapterInterface
     */
    private $adapter;
    /**
     * @var PrivateKeyFactory
     */
    private $privateFactory;
    /**
     * ElectrumKeyFactory constructor.
     * @param EcAdapterInterface|null $ecAdapter
     */
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface $ecAdapter = null)
    {
        $this->adapter = $ecAdapter ?: \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Bitcoin::getEcAdapter();
        $this->privateFactory = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory($ecAdapter);
    }
    /**
     * @param string $mnemonic
     * @param ElectrumWordListInterface $wordList
     * @return ElectrumKey
     * @throws \Exception
     */
    public function fromMnemonic(string $mnemonic, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\Electrum\ElectrumWordListInterface $wordList = null) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey
    {
        $mnemonicConverter = \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic\MnemonicFactory::electrum($wordList, $this->adapter);
        $entropy = $mnemonicConverter->mnemonicToEntropy($mnemonic);
        return $this->getKeyFromSeed($entropy);
    }
    /**
     * @param BufferInterface $seed
     * @return ElectrumKey
     * @throws \Exception
     */
    public function getKeyFromSeed(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $seed) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey
    {
        // Really weird, did electrum actually hash hex string seeds?
        $binary = $oldseed = $seed->getHex();
        // Perform sha256 hash 5 times per iteration
        for ($i = 0; $i < 5 * 20000; $i++) {
            // Hash should return binary data
            $binary = \hash('sha256', $binary . $oldseed, \true);
        }
        // Convert binary data to hex.
        $secretExponent = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer($binary, 32);
        return $this->fromSecretExponent($secretExponent);
    }
    /**
     * @param BufferInterface $secret
     * @return ElectrumKey
     */
    public function fromSecretExponent(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $secret) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey
    {
        $masterKey = $this->privateFactory->fromBufferUncompressed($secret);
        return $this->fromKey($masterKey);
    }
    /**
     * @param KeyInterface $key
     * @return ElectrumKey
     */
    public function fromKey(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface $key) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey
    {
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\Deterministic\ElectrumKey($key);
    }
}
