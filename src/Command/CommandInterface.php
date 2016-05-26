<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/05/16
 * Time: 13:13
 */
namespace Cundd\TestFlight\Command;

/**
 * Interface for a CLI Command
 */
interface CommandInterface
{
    /**
     * Runs the command
     * 
     * @return bool
     */
    public function run(): bool;
}
