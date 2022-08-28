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
use pocketmine\item\GoldSword;
use pocketmine\nbt\tag\IntTag;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\Creature;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class PigZombie extends WalkingMonster{

	const NETWORK_ID = 36;

	private $angry = 0;

	public $width = 0.72;
	public $height = 1.8;
	public $eyeHeight = 1.62;
	protected $speed = 1;
	private $attackTicks = 0;

	public function getSpeed(){
		return $this->speed;
	}

	public function setSpeed($val){
		$this->speed = $val;
	}

	public function initEntity(){
		parent::initEntity();
		$this->setFriendly(true);
		$this->fireProof = true;
		$this->setDamage([0, 5, 9, 13]);
	}

	public function saveNBT(){
		parent::saveNBT();
	}

	public function getName(){
		return "PigZombie";
	}

	public function isAngry(){
		return $this->angry > 0;
	}

	public function setAngry($val, $damager = null){
		$this->angry = $val;
		$this->lastdamager = $damager;
		$this->setSpeed(2);
	}

	public function targetOption(Creature $creature, $distance){
		if($this->lastdamager != null){
			if($creature->getId() == $this->lastdamager->getId() and $this->isAngry()){
				return $creature->isAlive() && $distance <= 30;
			}
		}
		return false;
	}

	public function attack($damage, EntityDamageEvent $source){
		if(!$source->isCancelled()){
			if($source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
				if(($player = $source->getDamager()) instanceof Player){
					if(!$player->isCreative() and !$player->isSpectator()){
						$this->setAngry(1000, $player);
					}
				}
			}
		}
		parent::attack($damage, $source);
	}

	public function spawnTo(Player $player){
		parent::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->item = new GoldSword();
		$pk->slot = 10;
		$pk->selectedSlot = 10;
		$player->dataPacket($pk);
	}

	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) < 1.5){
			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			$player->attack($ev->getFinalDamage(), $ev);
			if($player->getHealth() <= 0){
				$this->setAngry(0);
			}
		}
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		//Timings::$timerEntityBaseTick->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);
		if($this->isAngry()){
			$this->angry--;
		}else{
			$this->setSpeed(1);
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
					switch(mt_rand(0, 1)){
						case 0:
							$drops[] = ItemItem::get(ItemItem::GOLD_INGOT, 0, 1);
							break;
						case 1:
							$drops[] = ItemItem::get(ItemItem::GOLD_SWORD, 0, 1);
							break;
					}
				}
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::ROTTEN_FLESH, 0, $count);
				}
			}
		}
		return $drops;
	}
}
