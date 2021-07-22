<?php

namespace OguzhanUmutlu\FunctionPacks;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class FunctionPacks extends PluginBase {
    public $functions = [];
    public function onEnable() {
        $dir = Server::getInstance()->getDataPath()."functions";
        if(!file_exists($dir))
            mkdir($dir);
        foreach(scandir($dir) as $file) {
            if($file != "." && $file != "..")
                $this->functions[implode(".", array_slice(explode(".", $file), 0, count(explode(".", $file))-1))] = file_get_contents($dir."\\".$file);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() != "function" || !$sender->hasPermission($command->getPermission())) return true;
        if(!isset($args[0])) {
            $sender->sendMessage($command->getUsage());
            return true;
        }
        if(!isset($this->functions[$args[0]])) {
            $sender->sendMessage("Function not found!");
            return true;
        }
        foreach(explode("\n", $this->functions[$args[0]]) as $function) {
            $function = str_replace("%player", $sender->getName(), $function);
            foreach(array_slice($args, 1) as $i => $arg)
                $function = str_replace("%".$i, $arg, $function);
            Server::getInstance()->dispatchCommand($sender, $function);
        }
        return true;
    }
}
