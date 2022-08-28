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

use pocketmine\entity\monster\Monster;
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Timings;
use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\entity\monster\walking\Wolf;
use pocketmine\network\mcpe\protocol\EntityEventPacket;

abstract class BaseEntity extends Creature{

	protected $moveTime = 0;

	/** @var Vector3|Entity */
	protected $baseTarget = null;

	private $movement = true;
	private $friendly = false;
	private $wallcheck = true;
	public $allowMove = false;
	public $lastdamager = null;
	protected $sprintTime = 0;

	protected $speed = 1;


	public static function setCloseMonsterOnDay($val) {
		self::$closeMonsterOnDay = $val;
	}

	public function __destruct(){}

	public abstract function updateMove();

	public function getSaveId(){
		$class = new \ReflectionClass(get_class($this));
		return $class->getShortName();
	}

	public function isMovement(){
		return $this->movement;
	}

	public function isFriendly(){
		return $this->friendly;
	}

	public function isKnockback(){
		return $this->attackTime > 0;
	}

	public function isWallCheck(){
		return $this->wallcheck;
	}

	public function setMovement(bool $value){
		$this->movement = $value;
	}

	public function setFriendly(bool $bool){
		$this->friendly = $bool;
	}

	public function setWallCheck(bool $value){
		$this->wallcheck = $value;
	}

	public function getSpeed(){
		return 1;
	}

	public function initEntity(){
		parent::initEntity();

		if(isset($this->namedtag->Movement)){
			$this->setMovement($this->namedtag["Movement"]);
		}

		if(isset($this->namedtag->WallCheck)){
			$this->setWallCheck($this->namedtag["WallCheck"]);
		}
		$this->dataProperties[self::DATA_NO_AI] = [self::DATA_TYPE_BYTE, 1];
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Movement = new ByteTag("Movement", $this->isMovement());
		$this->namedtag->WallCheck = new ByteTag("WallCheck", $this->isWallCheck());
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);
	}

	public function spawnTo(Player $player){
		if(
			!isset($this->hasSpawned[$player->getId()])
			&& isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())])
		){
			$pk = new AddEntityPacket();
			$pk->eid = $this->getID();
			$pk->type = static::NETWORK_ID;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = 0;
			$pk->speedY = 0;
			$pk->speedZ = 0;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk);

			$this->hasSpawned[$player->getId()] = $player;
		}
	}

	public function isInsideOfSolid(){
		$block = $this->level->getBlock(new Vector3(Math::floorFloat($this->x), Math::floorFloat($this->y + $this->height - 0.18), Math::floorFloat($this->z)));
		$bb = $block->getBoundingBox();
		return $bb !== null and $block->isSolid() and !$block->isTransparent() and $bb->intersectsWith($this->getBoundingBox());
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		$hasUpdate = Entity::entityBaseTick($tickDiff);

		if($this->moveTime > 0){
			$this->moveTime -= $tickDiff;
		}

		if($this->attackTime > 0){
			$this->attackTime -= $tickDiff;
		}

		if($this->sprintTime > 0){
			//$this->setSprinting();
			$this->sprintTime -= $tickDiff;
		}else{
			//$this->setSprinting(false);
		}
		return $hasUpdate;
	}

	public function targetOption(Creature $creature, float $distance){
		return $this instanceof Monster && (!($creature instanceof Player) || ($creature->isSurvival() && $creature->spawned)) && $creature->isAlive() && !$creature->closed && $distance <= 81;
	}


	 public static function create($type, Position $source, ...$args){
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $source->x),
				new DoubleTag("", $source->y),
				new DoubleTag("", $source->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $source instanceof Location ? $source->yaw : 0),
				new FloatTag("", $source instanceof Location ? $source->pitch : 0)
			]),
		]);
		return Entity::createEntity($type, $source->getLevel(), $nbt, ...$args);
	}

	public function isNeedSaveOnChunkUnload() {
		return false;
	}

}
