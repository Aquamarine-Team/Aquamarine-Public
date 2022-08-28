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
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\level\particle\PortalParticle;
use pocketmine\item\enchantment\Enchantment;

class Enderman extends WalkingMonster{

	const NETWORK_ID = 38;

	public $width = 0.72;
	public $height = 3;
	private $angry = 0;
	protected $speed = 1;

	public function getSpeed(){
		return $this->speed;
	}

	public function setSpeed($val){
		$this->speed = $val;
	}

	public function initEntity(){
		parent::initEntity();
		$this->setFriendly(true);
		$this->setDamage([0, 4, 7, 10]);
	}

	public function getName(){
		return "Enderman";
	}

	public function isAngry(){
		return $this->angry > 0;
	}

	public function setAngry($val, $damager = null){
		$this->angry = $val;
		$this->lastdamager = $damager;
		$this->setSpeed(1.6);
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

	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) < 1.5){
			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			$player->attack($ev->getFinalDamage(), $ev);
			if($player->getHealth() <= 0){
				$this->setAngry(0);
			}
		}
	}

	public function randomFloat($min = -0.8, $max = 0.8) {
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		//Timings::$timerEntityBaseTick->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->isAngry()){
			$this->angry--;
		}else{
			$this->setSpeed(1.2);
		}

		if(mt_rand(0, 100) < 1){
			$x = $this->x + mt_rand(1, 10);
			$z = $this->z + mt_rand(1, 10);
			$y = $this->getLevel()->getHighestBlockAt($x, $z) + 1;
			for($i = 0; $i < 30; $i++){
				$this->getLevel()->addParticle(new PortalParticle(new vector3($x + $this->randomFloat(), $y + $this->randomFloat(-0.8, 2.5), $z + $this->randomFloat())));
			}
			$this->teleport($this->getLevel()->getSafeSpawn(new Vector3($x, $y, $z)));
			for($i = 0; $i < 30; $i++){
				$this->getLevel()->addParticle(new PortalParticle(new vector3($x + $this->randomFloat(), $y + $this->randomFloat(-0.8, 2.5), $z + $this->randomFloat())));
			}
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
				$count = mt_rand(0, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::ENDER_PEARL, 0, $count);
				}
			}
		}
		return $drops;
	}

}
