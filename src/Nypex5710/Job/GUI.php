<?php

namespace Nypex5710\Job;

use Nypex5710\Job\JMain;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\{Player, Server};

class GUI{

    private $menu;

    public function __construct(string $name){
        $this->menu = InvMenu::create(InvMenu::TYPE_HOPPER)
            ->readonly()
            ->setName($name)
            ->setListener([$this, "onItemEvent"])
            ->setInventoryCloseListener(function(Player $player): void{
                //close
            });
    }

    public function addItemToList(Item $item): void{
        $this->menu->getInventory()->addItem($item);
    }

    public function onItemEvent(Player $player, Item $itemClickedOn): bool{
        if($itemClickedOn->getCustomName() == JMain::getInstance()->message["woodcutter"]){
            $required = rand(1, 10);
            //$money = $required*3;
            $name = $player->getLowerCaseName();
            $jobName = "oduncu";
            $bdg = strtotime("+1 day", time());
            $jobdb = JMain::getInstance()->db->prepare("INSERT INTO jobPlayers (player, meslek, gorev, yapilan, birdahakigorev, alindimi) VALUES (:player, :meslek, :gorev, :yapilan, :birdahakigorev, :alindimi)");
            $jobdb->bindValue(":player", $name);
            $jobdb->bindValue(":meslek", $jobName);
            $jobdb->bindValue(":gorev", $required);
            $jobdb->bindValue(":yapilan", 0);
            $jobdb->bindValue(":birdahakigorev", $bdg);
            $jobdb->bindValue(":alindimi", "evet");
            $jobdb->execute();
            $player->sendMessage(JMain::getInstance()->message["take-mission-woodcutter"] . $required);
            $player->removeWindow($this->menu->getInventory($player));
        }
        if($itemClickedOn->getCustomName() == JMain::getInstance()->message["miner"]){
            $required = rand(1, 10);
            $name = $player->getLowerCaseName();
            $jobName = "madenci";
            $bdg = strtotime("+1 day", time());
            $jobdb = JMain::getInstance()->db->prepare("INSERT INTO jobPlayers (player, meslek, gorev, yapilan, birdahakigorev, alindimi) VALUES (:player, :meslek, :gorev, :yapilan, :birdahakigorev, :alindimi)");
            $jobdb->bindValue(":player", $name);
            $jobdb->bindValue(":meslek", $jobName);
            $jobdb->bindValue(":gorev", $required);
            $jobdb->bindValue(":yapilan", 0);
            $jobdb->bindValue(":birdahakigorev", $bdg);
            $jobdb->bindValue(":alindimi", "evet");
            $jobdb->execute();
            $player->sendMessage(JMain::getInstance()->message["take-mission-miner"] . $required);
            $player->removeWindow($this->menu->getInventory($player));
        }
        if($itemClickedOn->getCustomName() == JMain::getInstance()->message["gardener"]){
          $required = rand(1, 10);
          $name = $player->getLowerCaseName();
          $jobName = "bahcivan";
          $bdg = strtotime("+1 day", time());
          $jobdb = JMain::getInstance()->db->prepare("INSERT INTO jobPlayers (player, meslek, gorev, yapilan, birdahakigorev, alindimi) VALUES (:player, :meslek, :gorev, :yapilan, :birdahakigorev, :alindimi)");
          $jobdb->bindValue(":player", $name);
          $jobdb->bindValue(":meslek", $jobName);
          $jobdb->bindValue(":gorev", $required);
          $jobdb->bindValue(":yapilan", 0);
          $jobdb->bindValue(":birdahakigorev", $bdg);
          $jobdb->bindValue(":alindimi", "evet");
          $jobdb->execute();
          $player->sendMessage(JMain::getInstance()->message["take-mission-gardener"] . $required);
          $player->removeWindow($this->menu->getInventory($player));
        }
        if($itemClickedOn->getCustomName() == JMain::getInstance()->message["murder"]){
          $required = rand(1, 10);
          $name = $player->getLowerCaseName();
          $jobName = "katil";
          $bdg = strtotime("+1 day", time());
          $jobdb = JMain::getInstance()->db->prepare("INSERT INTO jobPlayers (player, meslek, gorev, yapilan, birdahakigorev, alindimi) VALUES (:player, :meslek, :gorev, :yapilan, :birdahakigorev, :alindimi)");
          $jobdb->bindValue(":player", $name);
          $jobdb->bindValue(":meslek", $jobName);
          $jobdb->bindValue(":gorev", $required);
          $jobdb->bindValue(":yapilan", 0);
          $jobdb->bindValue(":birdahakigorev", $bdg);
          $jobdb->bindValue(":alindimi", "evet");
          $jobdb->execute();
          $player->sendMessage(JMain::getInstance()->message["take-mission-murder"] . $required);
          $player->removeWindow($this->menu->getInventory($player));
        }
        if($itemClickedOn->getCustomName() == JMain::getInstance()->message["dirter"]){
          $required = rand(1, 10);
          $name = $player->getLowerCaseName();
          $jobName = "mezarci";
          $bdg = strtotime("+1 day", time());
          $jobdb = JMain::getInstance()->db->prepare("INSERT INTO jobPlayers (player, meslek, gorev, yapilan, birdahakigorev, alindimi) VALUES (:player, :meslek, :gorev, :yapilan, :birdahakigorev, :alindimi)");
          $jobdb->bindValue(":player", $name);
          $jobdb->bindValue(":meslek", $jobName);
          $jobdb->bindValue(":gorev", $required);
          $jobdb->bindValue(":yapilan", 0);
          $jobdb->bindValue(":birdahakigorev", $bdg);
          $jobdb->bindValue(":alindimi", "evet");
          $jobdb->execute();
          $player->sendMessage(JMain::getInstance()->message["take-mission-dirter"] . $required);
          $player->removeWindow($this->menu->getInventory($player));
        }
        return true;
    }

    public function sendTo(Player $player): void{
        $this->menu->send($player);
    }
}
