<?php

namespace SonarSoftware\FreeRadius;

use League\CLImate\CLImate;
use RuntimeException;

class Mimosa
{
    private $climate;
    public function __construct()
    {
        $this->climate = new CLImate;
    }

    public function updateEap()
    {
        $this->climate->lightBlue()->inline("Enabling EAP and adding Mimosa dictionary... ");
        try {
            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/thirdparty/mimosa/eap /etc/freeradius/3.0/mods-available/");

            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/thirdparty/mimosa/*.pem /etc/freeradius/3.0/certs/");
            CommandExecutor::executeCommand("(cd /etc/freeradius/3.0/certs; /usr/bin/c_rehash .)");

            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/thirdparty/mimosa/dictionary.mimosa /etc/freeradius/3.0/");
            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/thirdparty/mimosa/dictionary /etc/freeradius/3.0/");

            //Copy this over again due to the EAP changes that may be missing
            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/default /etc/freeradius/3.0/sites-available/");
            CommandExecutor::executeCommand("/bin/cp " . __DIR__ . "/../conf/inner-tunnel /etc/freeradius/3.0/sites-available/");

            CommandExecutor::executeCommand("/usr/sbin/service freeradius restart");
        }
        catch (RuntimeException $e)
        {
            $this->climate->shout("FAILED!");
            $this->climate->shout($e->getMessage());
            $this->climate->shout("See /tmp/_genie_output for failure details.");
            return;
        }

        $this->climate->info("SUCCESS!");
    }
}
