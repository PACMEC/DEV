<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
class BranchInterpreter
{
    /**
     * @var array
     */
    private $disabledOps = [\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_CAT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_SUBSTR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LEFT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RIGHT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_INVERT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_AND, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_OR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_XOR, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2MUL, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_2DIV, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MUL, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_DIV, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_MOD, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_LSHIFT, \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_RSHIFT];
    /**
     * @param ScriptInterface $script
     * @return ParsedScript
     */
    public function getScriptTree(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\ParsedScript
    {
        $ast = $this->getAstForLogicalOps($script);
        $scriptPaths = $ast->flags();
        $scriptBranches = [];
        if (\count($scriptPaths) > 1) {
            foreach ($scriptPaths as $path) {
                $scriptBranches[] = $this->getBranchForPath($script, $path);
            }
        } else {
            $scriptBranches[] = $this->getBranchForPath($script, []);
        }
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\ParsedScript($script, $ast, $scriptBranches);
    }
    /**
     * Build tree of dependent logical ops
     * @param ScriptInterface $script
     * @return LogicOpNode
     */
    public function getAstForLogicalOps(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\LogicOpNode
    {
        $root = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\LogicOpNode(null);
        $current = $root;
        foreach ($script->getScriptParser()->decode() as $op) {
            switch ($op->getOp()) {
                case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF:
                case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF:
                    $split = $current->split();
                    $current = $split[$op->getOp() & 1];
                    break;
                case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF:
                    if (null === $current->getParent()) {
                        throw new \RuntimeException("Unexpected ENDIF, current scope had no parent");
                    }
                    $current = $current->getParent();
                    break;
                case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ELSE:
                    if (null === $current->getParent()) {
                        throw new \RuntimeException("Unexpected ELSE, current scope had no parent");
                    }
                    $current = $current->getParent()->getChild((int) (!$current->getValue()));
                    break;
            }
        }
        if (!$current->isRoot()) {
            throw new \RuntimeException("Unbalanced conditional - vfStack not empty at script termination");
        }
        return $root;
    }
    /**
     * Given a script and path, attempt to produce a ScriptBranch instance
     *
     * @param ScriptInterface $script
     * @param bool[] $path
     * @return ScriptBranch
     */
    public function getBranchForPath(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, array $path) : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\ScriptBranch
    {
        // parses the opcodes which were actually run
        $segments = $this->evaluateUsingStack($script, $path);
        return new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\ScriptBranch($script, $path, $segments);
    }
    /**
     * @param Stack $vfStack
     * @param bool $value
     * @return bool
     */
    private function checkExec(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack $vfStack, bool $value) : bool
    {
        $ret = 0;
        foreach ($vfStack as $item) {
            if ($item === $value) {
                $ret++;
            }
        }
        return (bool) $ret;
    }
    /**
     * @param ScriptInterface $script
     * @param int[] $logicalPath
     * @return array - array of Operation[] representing script segments
     */
    public function evaluateUsingStack(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface $script, array $logicalPath) : array
    {
        $mainStack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        foreach (\array_reverse($logicalPath) as $setting) {
            $mainStack->push($setting);
        }
        $vfStack = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Interpreter\Stack();
        $parser = $script->getScriptParser();
        $tracer = new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Path\PathTracer();
        foreach ($parser as $i => $operation) {
            $opCode = $operation->getOp();
            $fExec = !$this->checkExec($vfStack, \false);
            if (\in_array($opCode, $this->disabledOps, \true)) {
                throw new \RuntimeException('Disabled Opcode');
            }
            if (\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF <= $opCode && $opCode <= \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF) {
                switch ($opCode) {
                    case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF:
                    case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF:
                        // <expression> if [statements] [else [statements]] endif
                        $value = \false;
                        if ($fExec) {
                            if ($mainStack->isEmpty()) {
                                $op = $script->getOpcodes()->getOp($opCode & 1 ? \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_IF : \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF);
                                throw new \RuntimeException("Unbalanced conditional at {$op} - not included in logicalPath");
                            }
                            $value = $mainStack->pop();
                            if ($opCode === \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_NOTIF) {
                                $value = !$value;
                            }
                        }
                        $vfStack->push($value);
                        break;
                    case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ELSE:
                        if ($vfStack->isEmpty()) {
                            throw new \RuntimeException('Unbalanced conditional at OP_ELSE');
                        }
                        $vfStack->push(!$vfStack->pop());
                        break;
                    case \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\Opcodes::OP_ENDIF:
                        if ($vfStack->isEmpty()) {
                            throw new \RuntimeException('Unbalanced conditional at OP_ENDIF');
                        }
                        $vfStack->pop();
                        break;
                }
                $tracer->operation($operation);
            } else {
                if ($fExec) {
                    // Fill up trace with executed opcodes
                    $tracer->operation($operation);
                }
            }
        }
        if (\count($vfStack) !== 0) {
            throw new \RuntimeException('Unbalanced conditional at script end');
        }
        if (\count($mainStack) !== 0) {
            throw new \RuntimeException('Values remaining after script execution - invalid branch data');
        }
        return $tracer->done();
    }
}
