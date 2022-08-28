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
use pocketmine\nbt\tag\IntTag;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Creature;

class Wolf extends WalkingMonster{

	const NETWORK_ID = 14;

	private $angry = 0;
	public $width = 0.72;
	public $height = 0.9;
	protected $speed = 1.2;

	public function getSpeed(){
		return $this->speed;
	}

	public function setSpeed($val){
		$this->speed = $val;
	}

	public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(8);
		$this->fireProof = true;
		$this->setDamage([0, 3, 4, 6]);
	}

	public function saveNBT(){
		parent::saveNBT();
	}

	public function getName(){
		return "Wolf";
	}

	public function isAngry(){
		return $this->angry > 0;
	}

	public function setAngry($val, $damager = null){
		$this->angry = $val;
		$this->lastdamager = $damager;
		$this->setSpeed(1.6);
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);
		if(!$source->isCancelled()){
			if($source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
				$this->setAngry(1000, $source->getDamager());
			}
		}
	}

	public function targetOption(Creature $creature, $distance){
		if($this->lastdamager != null){
			if($creature->getId() == $this->lastdamager->getId() and $this->isAngry()){
				return $creature->isAlive() && $distance <= 30;
			}
		}
		return false;
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
			$this->setSpeed(1.2);
		}
		//Timings::$timerEntityBaseTick->startTiming();
		return $hasUpdate;
	}


	public function getDrops(){
		return [];
	}

}
