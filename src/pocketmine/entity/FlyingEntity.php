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

use pocketmine\entity\animal\Animal;
use pocketmine\entity\monster\flying\Blaze;
use pocketmine\entity\monster\FlyingMonster;
use pocketmine\math\Math;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\entity\Creature;

abstract class FlyingEntity extends BaseEntity{

	private $shootingMobs = ["Ghast", "Wither", "Blaze"];
	private $agrDistance = 40;
	protected $mooving = 0;

	protected function checkTarget($update = false){

		if ($update) {
			$this->moveTime = 0;
		}

		$target = $this->baseTarget;
		if ($this instanceof FlyingMonster) {
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
		}

		$maxY = max($this->getLevel()->getHighestBlockAt((int) $this->x, (int) $this->z) + 20, 120);
		if($this->moveTime <= 0 or !$this->baseTarget instanceof Vector3){
			$x = mt_rand(20, 100);
			$z = mt_rand(20, 100);
			if($this->y > $maxY){
				$y = mt_rand(-10, -4);
			}else{
				$y = mt_rand(-10, 10);
			}
			$this->moveTime = mt_rand(600, 1000);
			$this->baseTarget = $this->add(mt_rand(0, 1) ? $x : -$x, $y, mt_rand(0, 1) ? $z : -$z);
		}
	}

	public function updateMove(){
		if(!$this->isMovement()){
			return null;
		}

		$before = $this->baseTarget;
		$this->checkTarget($update = false);
		if($this->baseTarget instanceof Vector3){
			$x = $this->baseTarget->x - $this->x;
			$y = $this->baseTarget->y - $this->y;
			$z = $this->baseTarget->z - $this->z;

			$diff = abs($x) + abs($z);
			if($x ** 2 + $z ** 2 < 0.7 or (in_array($this->getName(), $this->shootingMobs) and ($this->distance($this->baseTarget) <= 20))){
				$this->yaw = -atan2($this->getSpeed() * 0.15 * ($x / $diff), $this->getSpeed() * 0.15 * ($z / $diff)) * 180 / M_PI;
				if(!$this->isKnockback()){
					$this->motionX = 0;
					$this->motionZ = 0;
				}
			}else{
				if(!$this->isKnockback()){
					if($this->mooving > 0){
						$this->motionX = $this->getSpeed() * 0.15 * ($x / $diff);
						$this->motionY = $this->getSpeed() * 0.27 * ($y / $diff);
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
		$isJump = false;
		$dx = $this->motionX;
		$dy = $this->motionY;
		$dz = $this->motionZ;

		$this->move($dx, $dy, $dz);
		$be = new Vector2($this->x + $dx, $this->z + $dz);
		$af = new Vector2($this->x, $this->z);

		if($be->x != $af->x || $be->y != $af->y){
			if($this instanceof Blaze){
				$x = 0;
				$z = 0;
				if($be->x - $af->x != 0){
					$x = $be->x > $af->x ? 1 : -1;
				}
				if($be->y - $af->y != 0){
					$z = $be->y > $af->y ? 1 : -1;
				}

				$vec = new Vector3(Math::floorFloat($be->x) + $x, $this->y, Math::floorFloat($be->y) + $z);
				$block = $this->level->getBlock($vec->add($x, 0, $z));
				$block2 = $this->level->getBlock($vec->add($x, 1, $z));
				if(!$block->canPassThrough()){
					$bb = $block2->getBoundingBox();
					if(
						$this->motionY > -$this->gravity * 4
						&& ($block2->canPassThrough() || ($bb == null || $bb->maxY - $this->y <= 1))
					){
						$isJump = true;
						if($this->motionY >= 0.3){
							$this->motionY += $this->gravity;
						}else{
							$this->motionY = 0.3;
						}
					}
				}
			}
		}

		if($this instanceof Blaze){
			if($this->onGround && !$isJump){
				$this->motionY = 0;
			}else if(!$isJump){
				if($this->motionY > -$this->gravity * 4){
					$this->motionY = -$this->gravity * 4;
				}else{
					$this->motionY -= $this->gravity;
				}
			}
		}
		$this->updateMovement();
		return $target;
	}

}
