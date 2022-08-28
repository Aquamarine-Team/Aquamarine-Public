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

use pocketmine\event\player\PlayerPickupExpOrbEvent;
use pocketmine\level\sound\ExpPickupSound;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class XPOrb extends Entity {
	const NETWORK_ID = 69;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.04;
	protected $drag = 0;

	protected $experience = 0;

	protected $range = 6;

	public function initEntity(){
		parent::initEntity();
		if(isset($this->namedtag->Experience)){
			$this->experience = $this->namedtag["Experience"];
		}else $this->close();
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		if ($this->level == null) {
			return false;
		}

		$tickDiff = $currentTick - $this->lastUpdate;
		$this->lastUpdate = $currentTick;
		$this->timings->startTiming();
		$hasUpdate = $this->entityBaseTick($tickDiff);
		$this->age++;
		if($this->age > 1200){
			$this->kill();
			$this->close();
			$hasUpdate = true;
		}

		$minDistance = PHP_INT_MAX;
		$target = null;
		foreach($this->getViewers() as $p){
			if(!$p->isSpectator() and $p->isAlive()){
				if(($dist = $p->distance($this)) < $this->range){
					$target = $p;
					$minDistance = $dist;
				}
			}
		}

		if($target !== null){
			$moveSpeed = 0.7;
			$motX = ($target->getX() - $this->x) / 8;
			$motY = ($target->getY() + $target->getEyeHeight() - 0.2 - $this->y) / 8;
			$motZ = ($target->getZ() - $this->z) / 8;
			$motSqrt = sqrt($motX * $motX + $motY * $motY + $motZ * $motZ);
			$motC = 1 - $motSqrt;
			if($motC > 0){
				$motC *= $motC;
				$this->motionX = $motX / $motSqrt * $motC * $moveSpeed;
				$this->motionY = $motY / $motSqrt * $motC * $moveSpeed;
				$this->motionZ = $motZ / $motSqrt * $motC * $moveSpeed;
			}
			$this->motionY -= $this->gravity;
			if($this->isInsideOfSolid()){
				$this->setPosition($target);
			}
			$this->move($this->motionX, $this->motionY, $this->motionZ);
			$this->updateMovement();
			if($minDistance <= 1.3){
				if($this->getLevel()->getServer()->expEnabled and $target->canPickupXp()){
					$this->getLevel()->getServer()->getPluginManager()->callEvent($ev = new PlayerPickupExpOrbEvent($target, $this->getExperience()));
					if(!$ev->isCancelled()){
						$this->kill();
						$this->close();
						$this->despawnFromAll();
						if($this->getExperience() > 0){
							$target->level->addSound(new ExpPickupSound($target, mt_rand(0, 1000)));
							$target->addXp($this->getExperience());
							$target->resetXpCooldown();
						}
					}
				}
			}
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canCollideWith(Entity $entity){
		return false;
	}

	public function canBeCollidedWith() : bool{
		return false;
	}

	/**
	 * @param $exp
	 */
	public function setExperience($exp){
		$this->experience = $exp;
	}

	/**
	 * @return int
	 */
	public function getExperience(){
		return $this->experience;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_NO_AI, true);
		$pk = new AddEntityPacket();
		$pk->type = XPOrb::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
}
