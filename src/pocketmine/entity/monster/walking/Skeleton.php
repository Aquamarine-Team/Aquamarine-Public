<?php

/*
 *      ___                                          _
 *    /   | ____ ___  ______ _____ ___  ____ ______(_)___  ___
 *   / /| |/ __ `/ / / / __ `/ __ `__ \/ __ `/ ___/ / __ \/ _ \
 *  / ___ / /_/ / /_/ / /_/ / / / / / / /_/ / /  / / / / /  __/
 * /_/  |_\__, /\__,_/\__,_/_/ /_/ /_/\__,_/_/  /_/_/ /_/\___/
 *          /_/
 *
 * Author - MaruselPlay
 * VK - https://vk.com/maruselplay
 *
 *
 */

namespace pocketmine\entity\monster\walking;

use pocketmine\entity\monster\WalkingMonster;
use pocketmine\entity\Entity;
use pocketmine\entity\Projectile;
use pocketmine\entity\ProjectileSource;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Timings;
use pocketmine\item\Bow;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\level\sound\LaunchSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class Skeleton extends WalkingMonster implements ProjectileSource{

	const NETWORK_ID = 34;

	public $width = 0.65;
	public $height = 1.8;
	protected $shoot = 0;

	public function getName(){
		return "Skeleton";
	}

	public function checkDistance($target){
		if(sqrt($this->distanceSquared($target)) <= 10){
			return true;
		}else{
			return false;
		}
	}

	public function attackEntity(Entity $player){
		if($this->shoot < 1 and sqrt($this->distanceSquared($player)) <= 10){
			$this->shoot = 30;
			$f = 1.2;
			$nbt = Entity::createBaseNBT(
	        $this->add(0, $this->getEyeHeight() + 0.5, 0),
            new Vector3(
                -sin($this->yaw / 180 * M_PI) * cos($this->pitch / 180 * M_PI),
                sqrt($this->distanceSquared($player) / 10000),
                cos($this->yaw / 180 * M_PI) * cos($this->pitch / 180 * M_PI)
            ),
            $this->yaw,
            $this->pitch
        );

			/** @var Projectile $arrow */
			$arrow = Entity::createEntity("Arrow", $this->level, $nbt, $this);

			$ev = new EntityShootBowEvent($this, Item::get(Item::ARROW, 0, 1), $arrow, $f);
			$this->server->getPluginManager()->callEvent($ev);

			$projectile = $ev->getProjectile();
			if($ev->isCancelled()){
				$projectile->kill();
			}elseif($projectile instanceof Projectile){
				$this->server->getPluginManager()->callEvent($launch = new ProjectileLaunchEvent($projectile));
				if($launch->isCancelled()){
					$projectile->kill();
				}else{
					$projectile->spawnToAll();
					$this->level->addSound(new LaunchSound($this), $this->getViewers());
				}
			}
		}
	}

	public function spawnTo(Player $player){
		parent::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->item = new Bow();
		$pk->slot = 10;
		$pk->selectedSlot = 10;
		$player->dataPacket($pk);
	}

	public function hasHeadBlock($height = 50): bool{
		$x = floor($this->getX());
		$y = floor($this->getY()) + 2;
		$z = floor($this->getZ());
		$m = false;
		for($i=$y; $i < $y + $height; $i++){
			$block = $this->getLevel()->getBlock(new Vector3($x, $i, $z));
			if($block->getId() != 0){
				$m = true;
			}
		}
		return $m;
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		//Timings::$timerEntityBaseTick->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->shoot > 0){
			$this->shoot--;
		}

		$time = $this->getLevel() !== null ? $this->getLevel()->getTime() % Level::TIME_FULL : Level::TIME_NIGHT;
		if((!$this->isInsideOfWater()) && ($time < Level::TIME_NIGHT || $time > Level::TIME_SUNRISE) && (!$this->hasHeadBlock())){
			$this->setOnFire(1);
		}

		//Timings::$timerEntityBaseTick->startTiming();
		return $hasUpdate;
	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				if(mt_rand(0, 100) < (5 + 2 * $lootingL)){
					$drops[] = ItemItem::get(ItemItem::BOW, 0, 1);
				}
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::BONE, 0, $count);
				}
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::ARROW, 0, $count);
				}
			}
		}
		return $drops;
	}
}
