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
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Item;
use pocketmine\level\sound\LaunchSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\Player;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\entity\Creature;
use pocketmine\entity\Shearable;
use pocketmine\nbt\tag\ListTag;

class SnowGolem extends WalkingMonster{

	const NETWORK_ID = 21;

	const NBT_KEY_PUMPKIN = "Pumpkin";

	public $width = 0.6;
	public $height = 1.8;
	protected $shoot = 0;
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
	}

	public function getName(){
		return "SnowGolem";
	}

	public function isAngry(){
		return $this->angry > 0;
	}

	public function setAngry($val){
		$this->angry = $val;
		$this->lastdamager = $damager;
	}

	public function targetOption(Creature $creature, $distance){
		if($this->lastdamager != null){
			if($creature->getId() == $this->lastdamager->getId() and $this->isAngry()){
				return $creature->isAlive() && $distance <= 30;
			}
		}
		return false;
	}

	public function checkDistance($target){
		if(sqrt($this->distanceSquared($target)) <= 20){
			return true;
		}else{
			return false;
		}
	}

	public function attackEntity(Entity $player){
		if($this->shoot < 1 and sqrt($this->distanceSquared($player)) <= 20){
			$this->shoot = 5;
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
			$snowball = Entity::createEntity("Snowball", $this->level, $nbt, $this);

			$this->server->getPluginManager()->callEvent($launch = new ProjectileLaunchEvent($snowball));
			if($launch->isCancelled()){
				$snowball->kill();
			}else{
				$snowball->spawnToAll();
				$this->level->addSound(new LaunchSound($this), $this->getViewers());
			}
		}
	}

	public function entityBaseTick($tickDiff = 1, $EnchantL = 0){
		//Timings::$timerEntityBaseTick->startTiming();
		$hasUpdate = parent::entityBaseTick($tickDiff);
		if($this->shoot > 0){
			$this->shoot--;
		}
		if($this->isAngry()){
			$this->angry--;
		}else{
			$this->setSpeed(1);
		}
		//Timings::$timerEntityBaseTick->startTiming();
		return $hasUpdate;
	}

	public function getMaxHealth(){
		return 4;
	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				$count = mt_rand(2, 3 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::SNOWBALL, 0, $count);
				}
			}
		}
		return $drops;
	}
}
