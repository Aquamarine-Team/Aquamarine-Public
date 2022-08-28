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
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\Vector3;
use pocketmine\entity\Creature;
use pocketmine\entity\monster\Monster;
use pocketmine\Player;

class IronGolem extends WalkingMonster{

	const NETWORK_ID = 20;

	public $width = 1.9;
	public $height = 2.1;
	private $angry = 0;
	protected $speed = 0.5;

	public function getSpeed(){
		return $this->speed;
	}

	public function setSpeed($val){
		$this->speed = $val;
	}

	public function initEntity(){
		$this->setMaxHealth(100);
		parent::initEntity();

		$this->setFriendly(true);
		$this->setDamage([0, 21, 21, 21]);
		$this->setMinDamage([0, 7, 7, 7]);
	}

	public function getName(){
		return "IronGolem";
	}

	public function isAngry(){
		return $this->angry > 0;
	}

	public function setAngry($val, $damager = null){
		$this->angry = $val;
		$this->lastdamager = $damager;
		$this->setSpeed(1);
	}

	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) < 4){
			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			$player->attack($ev->getFinalDamage(), $ev);
			$player->setMotion(new Vector3(0, 0.6, 0)); //Woohoo!
			if($player->getHealth() - $ev->getFinalDamage() <= 0){
				$this->setAngry(0);
			}
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			$source->setKnockBack(0);
			if(($player = $source->getDamager()) instanceof Player){
				if(!$player->isCreative() and !$player->isSpectator()){
					$this->setAngry(1000, $player);
				}
			}
		}
		parent::attack($damage, $source);
	}

	public function targetOption(Creature $creature, $distance){
		if($this->lastdamager != null){
			if($creature->getId() == $this->lastdamager->getId() and $this->isAngry()){
				return $creature->isAlive() && $distance <= 30;
			}
		}
		if($creature instanceof Player) return false;
		if(!$creature->isFriendly()){
			$this->setSpeed(1);
			return $creature->isAlive() and $distance <= 30;
		}
		$this->setSpeed(0.5);
		return false;
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		//Timings::$timerEntityBaseTick->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);
		if($this->isAngry()){
			$this->angry--;
		}else{
			$this->setSpeed(0.5);
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
					if($lootingL <= 0) $lootingL = 1;
					$count = mt_rand(1, 5 * ($lootingL / 1.5));
					if($count > 0){
						$drops[] = ItemItem::get(ItemItem::IRON_INGOT, 0, $count);
					}
					$count = mt_rand(0, 2 * ($lootingL / 1.5));
					if($count > 0){
						$drops[] = ItemItem::get(ItemItem::POPPY, 0, $count);
					}
				}
			}
			return $drops;
		}
}
