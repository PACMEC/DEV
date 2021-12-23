<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Mutator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPoint;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\OutPointInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInput;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Buffer;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\BufferInterface;
class InputMutator
{
    /**
     * @var TransactionInputInterface
     */
    private $input;
    /**
     * @param TransactionInputInterface $input
     */
    public function __construct(TransactionInputInterface $input)
    {
        $this->input = $input;
    }
    /**
     * @return TransactionInputInterface
     */
    public function done() : TransactionInputInterface
    {
        return $this->input;
    }
    /**
     * @param array $array
     * @return $this
     */
    private function replace(array $array = [])
    {
        $this->input = new TransactionInput(\array_key_exists('outpoint', $array) ? $array['outpoint'] : $this->input->getOutPoint(), \array_key_exists('script', $array) ? $array['script'] : $this->input->getScript(), \array_key_exists('nSequence', $array) ? $array['nSequence'] : $this->input->getSequence());
        return $this;
    }
    /**
     * @param OutPointInterface $outPoint
     * @return InputMutator
     */
    public function outpoint(OutPointInterface $outPoint)
    {
        return $this->replace(array('outpoint' => $outPoint));
    }
    /**
     * @return $this
     */
    public function null()
    {
        return $this->replace(array('outpoint' => new OutPoint(new Buffer(\str_pad('', 32, "\x00"), 32), 0xffffffff)));
    }
    /**
     * @param BufferInterface $txid
     * @return $this
     */
    public function txid(BufferInterface $txid)
    {
        return $this->replace(array('txid' => $txid));
    }
    /**
     * @param int $vout
     * @return InputMutator
     */
    public function vout(int $vout)
    {
        return $this->replace(array('vout' => $vout));
    }
    /**
     * @param ScriptInterface $script
     * @return $this
     */
    public function script(ScriptInterface $script)
    {
        return $this->replace(array('script' => $script));
    }
    /**
     * @param int $nSequence
     * @return $this
     */
    public function sequence(int $nSequence)
    {
        return $this->replace(array('nSequence' => $nSequence));
    }
}
