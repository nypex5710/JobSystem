<?php

namespace Nypex5710\Job;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\{Server, Player};
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\command\commandSender;
use pocketmine\command\Command;
use pocketmine\item\Item;
use pocketmine\utils\Config;

use onebone\economyapi\EconomyAPI;
use Nypex5710\Job\formapi\SimpleForm;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

class JMain extends PluginBase implements Listener{

    private static $instance;

    public function onLoad(){
        self::$instance = $this;
    }

    public static function getInstance(): JMain{
        return self::$instance;
    }

    public function onEnable(): void{
        $this->getLogger()->info("Plugin aktif edildi.");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->db = new \SQLite3($this->getDataFolder() . "job.db");
        $this->db->exec("CREATE TABLE IF NOT EXISTS jobPlayers(player TEXT PRIMARY KEY, meslek TEXT, gorev INT, yapilan INT, birdahakigorev INT, alindimi TEXT);");
        $this->bank = new \SQLite3($this->getDataFolder() . "bank.db");
        $this->bank->exec("CREATE TABLE IF NOT EXISTS bankPlayers(player TEXT PRIMARY KEY, para INT, alinacaksure INT);");
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        $this->message = (new Config($this->getDataFolder() . "message.yml", Config::YAML, array(
          "take-mission-woodcutter" => "[WoodCutter] New mission received, cut wood: ",
          "take-mission-miner" => "[Miner] New mission received, break stone: ",
          "take-mission-gardener" => "[Gardener] New mission received, plant sapling: ",
          "take-mission-murder" => "[Murder] New mission received, kill player: ",
          "take-mission-dirter" => "[Dirter] New mission received, dig dirt: ",
          "mission-success" => "You have successfully completed the mission, your money has been transferred to the bank account",
          "time-is-not-over" => "Time required to withdraw money from the bank has not expired.",
          "add-money-bank" => " added to bank account.",
          "already-select-job" => "You have already chosen a job.",
          "bank-menu" => "Bank Menu",
          "you-money" => "You Money: ",
          "take-money" => "Withdraw Money",
          "job-menu" => "Job Menu",
          "woodcutter" => "WoodCutter",
          "miner" => "Miner",
          "gardener" => "Gardener",
          "murder" => "Murder",
          "dirter" => "Dirter",
        )))->getAll();
    }

    public function onDisable(): void{
        $this->getLogger()->info("Plugin de-aktif edildi.");
    }

    public function onJoin(PlayerJoinEvent $event): void{
      $player = $event->getPlayer();
      $name = $player->getLowerCaseName();

      $sure = strtotime("+3 day", time());
      $varmi = $this->bank->query("SELECT * FROM bankPlayers WHERE player = '{$name}'")->fetchArray(SQLITE3_ASSOC);
      if(!$varmi){
        $jobdb = $this->bank->prepare("INSERT INTO bankPlayers (player, para, alinacaksure) VALUES (:player, :para, :alinacaksure)");
        $jobdb->bindValue(":player", $name);
        $jobdb->bindValue(":para", "0");
        $jobdb->bindValue(":alinacaksure", $sure);
        $jobdb->execute();
      }

      $osorgu = $this->db->query("SELECT * FROM jobPlayers WHERE meslek = 'oduncu' AND player = '{$player->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
      $msorgu = $this->db->query("SELECT * FROM jobPlayers WHERE meslek = 'madenci' AND player = '{$player->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
      $bsorgu = $this->db->query("SELECT * FROM jobPlayers WHERE meslek = 'bahcivan' AND player = '{$player->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
      $ksorgu = $this->db->query("SELECT * FROM jobPlayers WHERE meslek = 'katil' AND player = '{$player->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
      $msorgu = $this->db->query("SELECT * FROM jobPlayers WHERE meslek = 'mezarci' AND player = '{$player->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);

      $sorgu = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$player->getLowerCaseName()}'");
      $array = $sorgu->fetchArray(SQLITE3_ASSOC);
      $zaman = strval($array['birdahakigorev']);

      if($osorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage($this->message["take-mission-woodcutter"] . $required);
        }
      }
      if($msorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage($this->message["take-mission-miner"] . $required);
        }
      }
      if($bsorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage($this->message["take-mission-gardener"] . $required);
        }
      }
      if($ksorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage($this->message["take-mission-murder"] . $required);
        }
      }
      if($msorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage($this->message["take-mission-dirter"] . $required);
        }
      }
    }

    public function onDeath(PlayerDeathEvent $event): void{
      $bd = $event->getEntity()->getLastDamageCause();
      if($bd instanceof EntityDamageByEntityEvent){
        $olduren = $bd->getDamager();
        $SQLmeslek4 = "SELECT * FROM jobPlayers WHERE meslek = 'katil' AND player = '{$olduren->getLowerCaseName()}'";
        $katilsorgu = $this->db->query($SQLmeslek4)->fetchArray(SQLITE3_ASSOC);

        $SQLsorgu = "SELECT * FROM jobPlayers WHERE alindimi = 'evet' AND yapilan = gorev AND player = '{$olduren->getLowerCaseName()}'";
        $herseytammi = $this->db->query($SQLsorgu)->fetchArray(SQLITE3_ASSOC);
        if($olduren instanceof Player){
          if($katilsorgu){
            if($herseytammi){
              $sorgu = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$olduren->getLowerCaseName()}'");
              $array = $sorgu->fetchArray(SQLITE3_ASSOC);
              $gorev = strval($array['gorev']);
              $para = $gorev * 3;
              $player->sendMessage($this->message["mission-success"]);
              //EconomyAPI::getInstance()->addMoney($player, $para);

              $bank = $this->bank->prepare("UPDATE bankPlayers SET para = para + $para WHERE player = '{$olduren->getLowerCaseName()}'");
              $bank->execute();

              $jobdb = $this->db->prepare("UPDATE jobPlayers SET alindimi = 'hayir' WHERE player = '{$olduren->getLowerCaseName()}'");
              $jobdb->execute();
            }else{
              $jobdb = $this->db->prepare("UPDATE jobPlayers SET yapilan = yapilan + 1 WHERE player = '{$olduren->getLowerCaseName()}'");
              $jobdb->execute();
            }
          }
        }
      }
    }

    public function onPlace(BlockPlaceEvent $event): void{
      $player = $event->getPlayer();

      $SQLmeslek3 = "SELECT * FROM jobPlayers WHERE meslek = 'bahcivan' AND player = '{$player->getLowerCaseName()}'";
      $bahcivansorgu = $this->db->query($SQLmeslek3)->fetchArray(SQLITE3_ASSOC);

      $SQLsorgu = "SELECT * FROM jobPlayers WHERE alindimi = 'evet' AND yapilan = gorev AND player = '{$player->getLowerCaseName()}'";
      $herseytammi = $this->db->query($SQLsorgu)->fetchArray(SQLITE3_ASSOC);

      $sorgu = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$player->getLowerCaseName()}'");
      $array = $sorgu->fetchArray(SQLITE3_ASSOC);
      $gorev = strval($array['gorev']);

      if($event->getBlock()->getItemId() == Item::SAPLING){
        if($bahcivansorgu){
          if($herseytammi){
            $para = $gorev * 3;
            $player->sendMessage($this->message["mission-success"]);
            //EconomyAPI::getInstance()->addMoney($player, $para);

            $bank = $this->bank->prepare("UPDATE bankPlayers SET para = para + $para WHERE player = '{$player->getLowerCaseName()}'");
            $bank->execute();

            $jobdb = $this->db->prepare("UPDATE jobPlayers SET alindimi = 'hayir' WHERE player = '{$player->getLowerCaseName()}'");
            $jobdb->execute();
          }else{
            $jobdb = $this->db->prepare("UPDATE jobPlayers SET yapilan = yapilan + 1 WHERE player = '{$player->getLowerCaseName()}'");
            $jobdb->execute();
          }
        }
      }
    }

    public function onBreak(BlockBreakEvent $event): void{
        $player = $event->getPlayer();

        $SQLmeslek = "SELECT * FROM jobPlayers WHERE meslek = 'oduncu' AND player = '{$player->getLowerCaseName()}'";
        $oduncusorgu = $this->db->query($SQLmeslek)->fetchArray(SQLITE3_ASSOC);
        $SQLmeslek2 = "SELECT * FROM jobPlayers WHERE meslek = 'madenci' AND player = '{$player->getLowerCaseName()}'";
        $madencisorgu = $this->db->query($SQLmeslek2)->fetchArray(SQLITE3_ASSOC);
        $SQLmeslek5 = "SELECT * FROM jobPlayers WHERE meslek = 'mezarci' AND player = '{$player->getLowerCaseName()}'";
        $mezarcisorgu = $this->db->query($SQLmeslek5)->fetchArray(SQLITE3_ASSOC);

        $SQLsorgu = "SELECT * FROM jobPlayers WHERE alindimi = 'evet' AND yapilan = gorev AND player = '{$player->getLowerCaseName()}'";
        $herseytammi = $this->db->query($SQLsorgu)->fetchArray(SQLITE3_ASSOC);

        $sorgu = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$player->getLowerCaseName()}'");
        $array = $sorgu->fetchArray(SQLITE3_ASSOC);
        $gorev = strval($array['gorev']);
        if($event->getBlock()->getItemId() == Item::WOOD || $event->getBlock()->getItemId() == Item::WOOD2){
            if($oduncusorgu){
              if($herseytammi){
                $para = $gorev * 3;
                $player->sendMessage($this->message["mission-success"]);
                //EconomyAPI::getInstance()->addMoney($player, $para);

                $bank = $this->bank->prepare("UPDATE bankPlayers SET para = para + $para WHERE player = '{$player->getLowerCaseName()}'");
                $bank->execute();

                $jobdb = $this->db->prepare("UPDATE jobPlayers SET alindimi = 'hayir' WHERE player = '{$player->getLowerCaseName()}'");
                $jobdb->execute();
              }else{
                $jobdb = $this->db->prepare("UPDATE jobPlayers SET yapilan = yapilan + 1 WHERE player = '{$player->getLowerCaseName()}'");
                $jobdb->execute();
              }
            }
        }
        if($event->getBlock()->getItemId() == Item::STONE || $event->getBlock()->getItemId() == Item::COBBLESTONE){
          if($madencisorgu){
            if($herseytammi){
              $para = $gorev * 3;
              $player->sendMessage($this->message["mission-success"]);
              //EconomyAPI::getInstance()->addMoney($player, $para);

              $bank = $this->bank->prepare("UPDATE bankPlayers SET para = para + $para WHERE player = '{$player->getLowerCaseName()}'");
              $bank->execute();

              $jobdb = $this->db->prepare("UPDATE jobPlayers SET alindimi = 'hayir' WHERE player = '{$player->getLowerCaseName()}'");
              $jobdb->execute();
            }else{
              $jobdb = $this->db->prepare("UPDATE jobPlayers SET yapilan = yapilan + 1 WHERE player = '{$player->getLowerCaseName()}'");
              $jobdb->execute();
            }
          }
        }
        if($event->getBlock()->getItemId() == Item::DIRT || $event->getBlock()->getItemId() == Item::GRASS){
          if($mezarcisorgu){
            if($herseytammi){
              $para = $gorev * 3;
              $player->sendMessage($this->message["mission-success"]);
              //EconomyAPI::getInstance()->addMoney($player, $para);

              $bank = $this->bank->prepare("UPDATE bankPlayers SET para = para + $para WHERE player = '{$player->getLowerCaseName()}'");
              $bank->execute();

              $jobdb = $this->db->prepare("UPDATE jobPlayers SET alindimi = 'hayir' WHERE player = '{$player->getLowerCaseName()}'");
              $jobdb->execute();
            }else{
              $jobdb = $this->db->prepare("UPDATE jobPlayers SET yapilan = yapilan + 1 WHERE player = '{$player->getLowerCaseName()}'");
              $jobdb->execute();
            }
          }
        }
    }

    public function bankMenu($player){
      $form = new SimpleForm(function (Player $event, $data){
        $player = $event->getPlayer();
        $oyuncu = $player->getName();

        if($data === null){
          return;
        }

        switch($data){
          case 0:
          $sorgu = $this->bank->query("SELECT * FROM bankPlayers WHERE player = '{$player->getLowerCaseName()}'");
          $array = $sorgu->fetchArray(SQLITE3_ASSOC);
          $zaman = strval($array['alinacaksure']);
          if($zaman < time()){
            $sorgu = $this->bank->query("SELECT * FROM bankPlayers WHERE player = '{$player->getLowerCaseName()}'");
            $array = $sorgu->fetchArray(SQLITE3_ASSOC);
            $para = strval($array['para']);
            EconomyAPI::getInstance()->addMoney($player, $para);
            $player->sendMessage("§f$para" . $this->message["add-money-bank"]);
            $bdz = strtotime("+3 day", time());
            $banka = $this->bank->prepare("UPDATE bankPlayers SET alinacaksure = $bdz WHERE player = '{$player->getLowerCaseName()}'");
            $banka->execute();
          }else{
            $player->sendMessage($this->message["time-is-not-over"]);
          }
          break;
        }
      });
      $form->setTitle($this->message["bank-menu"]);

      $sorgu = $this->bank->query("SELECT * FROM bankPlayers WHERE player = '{$player->getLowerCaseName()}'");
      $array = $sorgu->fetchArray(SQLITE3_ASSOC);
      $para = strval($array['para']);

      $form->setContent($this->message["you-money"]." §f$para TL");
      $form->addButton($this->message["take-money"], 1, "https://gamepedia.cursecdn.com/minecraft_gamepedia/0/01/MCoin.png");
      $form->sendToPlayer($player);
    }

    public function onCommand(CommandSender $cs, Command $cmd, string $label, array $args): bool{
        if($cmd->getName() == "job"){
            $meslekSahibimi = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$cs->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
            if($meslekSahibimi){
                $cs->sendMessage($this->message["already-select-job"]);
            }else{
                $gui = new GUI($this->message["job-menu"], $this);
                $gui->addItemToList(Item::get(Item::WOODEN_AXE)->setCustomName($this->message["woodcutter"]));
                $gui->addItemToList(Item::get(Item::WOODEN_PICKAXE)->setCustomName($this->message["miner"]));
                $gui->addItemToList(Item::get(Item::SHEARS)->setCustomName($this->message["gardener"]));
                $gui->addItemToList(Item::get(Item::WOODEN_SWORD)->setCustomName($this->message["murder"]));
                $gui->addItemToList(Item::get(Item::WOODEN_SHOVEL)->setCustomName($this->message["dirter"]));
                $gui->sendTo($cs);
            }
        }
        if($cmd->getName() == "bank"){
          $this->bankMenu($cs);
        }
        return true;
    }
}
