<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Mnemonic;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
interface MnemonicInterface
{
    /**
     * @param BufferInterface $entropy
     * @return string[]
     */
    public function entropyToWords(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $entropy) : array;
    /**
     * @param BufferInterface $entropy
     * @return string
     */
    public function entropyToMnemonic(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface $entropy) : string;
    /**
     * @param string $mnemonic
     * @return BufferInterface
     */
    public function mnemonicToEntropy(string $mnemonic) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
}
