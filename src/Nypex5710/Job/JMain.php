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
          $player->sendMessage("§Hey sen, yeni görevin seni bekliyor §f$required §aadet odun kır.");
        }
      }
      if($msorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage("§Hey sen, yeni görevin seni bekliyor §f$required §aadet taş kaz.");
        }
      }
      if($bsorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage("§Hey sen, yeni görevin seni bekliyor §f$required §aadet fidan dik.");
        }
      }
      if($ksorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage("§Hey sen, yeni görevin seni bekliyor §f$required §aadet insan öldür.");
        }
      }
      if($msorgu){
        if($zaman < time()){
          $required = rand(1, 10);
          $bdg = strtotime("+1 day", time());
          $jobdb = $this->db->prepare("UPDATE jobPlayers SET gorev = $required AND yapilan = 0 AND birdahakigorev = $bdg WHERE player = '{$player->getLowerCaseName()}'");
          $jobdb->execute();
          $player->sendMessage("§Hey sen, yeni görevin seni bekliyor §f$required §aadet toprak kaz.");
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
              $player->sendMessage("§aVerilen görevi başarı ile tamamlayarak §f$para §aTL kazandın.\n§aBanka hesabınızdan parayı çekebilirsiniz.");
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
            $player->sendMessage("§aVerilen görevi başarı ile tamamlayarak §f$para §aTL kazandın.\n§aBanka hesabınızdan parayı çekebilirsiniz.");
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
                $player->sendMessage("§aVerilen görevi başarı ile tamamlayarak §f$para §aTL kazandın.\n§aBanka hesabınızdan parayı çekebilirsiniz.");
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
              $player->sendMessage("§aVerilen görevi başarı ile tamamlayarak §f$para §aTL kazandın.\n§aBanka hesabınızdan parayı çekebilirsiniz.");
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
              $player->sendMessage("§aVerilen görevi başarı ile tamamlayarak §f$para §aTL kazandın.\n§aBanka hesabınızdan parayı çekebilirsiniz.");
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
            $player->sendMessage("§f$para TL §ahesabınıza eklendi.");
            $bdz = strtotime("+3 day", time());
            $banka = $this->bank->prepare("UPDATE bankPlayers SET alinacaksure = $bdz WHERE player = '{$player->getLowerCaseName()}'");
            $banka->execute();
          }else{
            $player->sendMessage("§cParanızı çekmek için gereken süre henüz dolmamış.");
          }
          break;
        }
      });
      $form->setTitle("§6Banka Menüsü");

      $sorgu = $this->bank->query("SELECT * FROM bankPlayers WHERE player = '{$player->getLowerCaseName()}'");
      $array = $sorgu->fetchArray(SQLITE3_ASSOC);
      $para = strval($array['para']);

      $form->setContent("§aBankadaki Paranız: §f$para TL");
      $form->addButton("Paranı Çek", 1, "https://gamepedia.cursecdn.com/minecraft_gamepedia/0/01/MCoin.png");
      $form->sendToPlayer($player);
    }

    public function onCommand(CommandSender $cs, Command $cmd, string $label, array $args): bool{
        if($cmd->getName() == "meslek"){
            $meslekSahibimi = $this->db->query("SELECT * FROM jobPlayers WHERE player = '{$cs->getLowerCaseName()}'")->fetchArray(SQLITE3_ASSOC);
            if($meslekSahibimi){
                $cs->sendMessage("Zaten bir meslek seçmişsin.");
            }else{
                $gui = new GUI("Meslek Seçim Menüsü", $this);
                $gui->addItemToList(Item::get(Item::WOODEN_AXE)->setCustomName("§6Oduncu\n\n§eOduncu mesleğini seçmek için envanterine sürükle."));
                $gui->addItemToList(Item::get(Item::WOODEN_PICKAXE)->setCustomName("§6Madenci\n\n§eMadenci mesleğini seçmek için envanterine sürükle."));
                $gui->addItemToList(Item::get(Item::SHEARS)->setCustomName("§6Bahçıvan\n\n§eBahçıvan mesleğini seçmek için envanterine sürükle."));
                $gui->addItemToList(Item::get(Item::WOODEN_SWORD)->setCustomName("§6Katil\n\n§eKatil mesleğini seçmek için envanterine sürükle."));
                $gui->addItemToList(Item::get(Item::WOODEN_SHOVEL)->setCustomName("§6Mezarcı\n\n§eMezarcı mesleğini seçmek için envanterine sürükle."));
                $gui->sendTo($cs);
            }
        }
        if($cmd->getName() == "banka"){
          $this->bankMenu($cs);
        }
        return true;
    }
}
