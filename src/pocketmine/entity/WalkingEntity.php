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

use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\block\Air;
use pocketmine\block\Liquid;
use pocketmine\Player;
use pocketmine\entity\moster\walking\IronGolem;
use pocketmine\entity\Entity;
use pocketmine\entity\moster\walking\SnowGolem;
use pocketmine\entity\moster\walking\Skeleton;
use pocketmine\entity\monster\Monster;
use pocketmine\block\Water;

abstract class WalkingEntity extends BaseEntity {

	protected $agrDistance = 25;
	protected $mooving = 0;
	protected $shootingMobs = ["Skeleton", "Stray", "SnowGolem", "Witch", "Evoker"];
	protected $neutralMobs = ["Enderman", "IronGolem", "PigZombie", "SnowGolem", "Wolf"];

	protected function checkTarget($update = false) {
		//if ($this->isKnockback() && !$update && $this->baseTarget instanceof Player && $this->baseTarget->isAlive() && sqrt($this->distanceSquared($this->baseTarget)) < $this->agrDistance) {
			//return;
		//}
		if ($update) {
			$this->moveTime = 0;
		}
		if ($this instanceof Monster and !in_array($this->getName(), $this->neutralMobs)) {
			$near = PHP_INT_MAX;
			foreach ($this->getLevel()->getServer()->getOnlinePlayers() as $player) {
				if((!$player->isCreative()) and (!$player->isSpectator())){
					if ($player->isAlive()) {
						$distance = $this->distance($player);
						if ($distance > $this->agrDistance) {
							continue;
						}
						$target = $player;
						$near = $distance;
					}
				}
			}

			if ($near <= $this->agrDistance) {
				$this->baseTarget = $target;
				$this->moveTime = 0;
				return;
			}
		}elseif($this instanceof Monster and in_array($this->getName(), $this->neutralMobs)){
			$target = null;
			$survival = false;
			foreach($this->level->getEntities() as $entity){
				if($entity instanceof Monster){
					if($this->targetOption($entity, $this->distance($entity))){
						$target = $entity;
					}
				}elseif($entity instanceof Player){
					if(!$entity->isCreative() and !$entity->isSpectator()){
						if($this->targetOption($entity, $this->distance($entity))){
							$target = $entity;
						}
					}
				}
			}
			if($target != null){
				$this->baseTarget = $target;
				$this->moveTime = 0;
				return;
			}
		}


		if ($this->moveTime <= 0) {
			$x = mt_rand(20, 100);
			$z = mt_rand(20, 100);
			$this->moveTime = mt_rand(0, 100);
			$this->baseTarget = new Vector3($this->getX() + (mt_rand(0, 1) ? $x : -$x), $this->getY(), $this->getZ() + (mt_rand(0, 1) ? $z : -$z));
			$y = $this->level->getHighestBlockAt($this->baseTarget->getX(), $this->baseTarget->getZ());
			$this->baseTarget->y = $y;
		}
	}


	public function updateMove() {
		if(!$this->allowMove){
			return null;
		}
		if (!$this->isMovement()) {
			return null;
		}

		if(!$this->willMove()) return null;

		if($this->mooving > 0){
			$this->mooving--;
		}

		$this->checkTarget($update = false);
		if($this->baseTarget instanceof Vector3){
			$x = $this->baseTarget->x - $this->x;
			$z = $this->baseTarget->z - $this->z;
			$diff = abs($x) + abs($z);

			if($x ** 2 + $z ** 2 < 0.7 or (in_array($this->getName(), $this->shootingMobs) and $this->checkDistance($this->baseTarget))){
				$this->yaw = -atan2($this->getSpeed() * 0.15 * ($x / $diff), $this->getSpeed() * 0.15 * ($z / $diff)) * 180 / M_PI;
				if(!$this->isKnockback()){
					$this->motionX = 0;
					$this->motionZ = 0;
				}
			}else{
				if(!$this->isKnockback()){
					if($this->mooving > 0){
						$this->motionX = $this->getSpeed() * 0.15 * ($x / $diff);
						$this->motionZ = $this->getSpeed() * 0.15 * ($z / $diff);
						$this->yaw = -atan2($this->motionX, $this->motionZ) * 180 / M_PI;
					}else{
						if(mt_rand(0, 100) < 3){
							$this->mooving = mt_rand(40, 200);
						}else{
							if(!$this->baseTarget instanceof Entity){
								$this->motionX = 0;
								$this->motionZ = 0;
							}
						}
					}
				}
			}
		}

		$target = $this->baseTarget;

		$dx = $this->motionX;
		$dz = $this->motionZ;
		$dy = $this->motionY;

		$newX = Math::floorFloat($this->x + $dx);
		$newZ = Math::floorFloat($this->z + $dz);
		$newY = Math::floorFloat($this->y + $dy);

		$v = $this->getDirectionVector();

		if($this->level->getBlock(new Vector3($this->x + $v->x, $this->y + 0.5, $this->z + $v->z)) instanceof Water){
			$this->motionY = 0.4; //swim
		}
		
		$block = $this->level->getBlock(new Vector3($this->x + $v->x, $this->y, $this->z + $v->z));
		if($block->isSolid()){
			$block = $this->level->getBlock(new Vector3($this->x + $v->x, $this->y + 1, $this->z + $v->z));
			if(!$block->isSolid() and $block->getId() != 85){ //мобы не прыгают через заборы
				$this->motionY = 0.3; //jump
			}
		}else {
			$block = $this->level->getBlock(new Vector3($newX, Math::floorFloat($this->y - 1), $newZ));
			if (!($block instanceof Air) && !($block instanceof Liquid)) {
				$blockY = Math::floorFloat($this->y);
				if ($this->y - $this->gravity * 4 > $blockY) {
					if(!$this->isKnockback()){
						$this->motionY = -$this->gravity * 4;
					}
				}
			} else {
				$this->motionY -= ($this->gravity * 4) / 2;
			}
		}
		$dy = $this->motionY;
		$this->move($dx, $dy, $dz);
		$this->updateMovement();
		return $target;

	}
}
