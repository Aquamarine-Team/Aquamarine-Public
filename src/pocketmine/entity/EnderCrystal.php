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

namespace pocketmine\entity;

use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\level\{Position, Explosion};
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\network\mcpe\protocol\AddEntityPacket;

class EnderCrystal extends Vehicle{

	const NETWORK_ID = 71;

  public $height = 1;
  public $width = 1;
  public $gravity = 0.5;
  public $drag = 0.1;
	private $dropItem = true;
	public $blown = false;

  public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
  }

  /**
	 * @return string
	 */
	public function getName() : string{
		return "Ender Crystal";
	}

	public function attack($damage, EntityDamageEvent $source){
		if($source->isCancelled()){
			return false;
		}
		if($source->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION and $source->getDamager() instanceof EnderCrystal and $source->getDamager()->blown){
			return false;
		}
		$this->blown = true;
		$this->kill();
		$count = 0;
		foreach($this->getLevel()->getChunk($this->x >> 4, $this->z >> 4)->getEntities() as $entity){
			if($entity instanceof EnderCrystal){
				$count++;
				if($count < 15){
					$entity->blown = true;
					$entity->kill();
					$entity->explode();
				}
				$entity->blown = true;
				$entity->kill();
			}
		}
		$this->explode();
		parent::attack($damage, $source);
	}

	public function explode(){
		$this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 4, $this->dropItem));
		$explosion = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), $ev->getForce(), $this, $ev->dropItem());
		$explosion->explodeA();
		$explosion->explodeB();
	}

  public function spawnTo(Player $player){
    $pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = EnderCrystal::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
	  $pk->speedX = 0;
		$pk->speedY = 0;
	  $pk->speedZ = 0;
	  $pk->yaw = 0;
	  $pk->pitch = 0;
	  $pk->metadata = $this->dataProperties;
	  $player->dataPacket($pk);

	  parent::spawnTo($player);
  }
}
