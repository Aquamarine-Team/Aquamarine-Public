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

namespace pocketmine\entity\monster\flying;

use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Creature;
use pocketmine\entity\BaseEntity;
use pocketmine\entity\monster\FlyingMonster;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\nbt\tag\{CompoundTag, IntTag, FloatTag, ListTag, StringTag, IntArrayTag, DoubleTag, ShortTag};
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\level\Particle;

class Wither extends FlyingMonster {
	const NETWORK_ID = 52;

	public $width = 1;
	public $length = 6;
	public $height = 4;

	public $dropExp = [25, 50];
	private $boomTicks = 0;
	private $step = 0.2;
	private $motionVector = null;
	private $farest = null;
	private $attackTicks = 0;
	private $hadExplode = false;
	protected $age = 0;
	/**
	 * @return string
	 */
	public function getName() : string{
		return "Wither";
	}

	public function initEntity(){
		$this->setMaxHealth(300);
		$this->setHealth(300);
		parent::initEntity();
	}
	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
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

	public function targetOption(Creature $creature, float $distance){
		if($creature instanceof Player and !$creature->isCreative() and !$creature->isSpectator() and ($this->distance($creature) <= 40) and $creature->isAlive()){
			return true;
		}elseif(!$creature instanceof Player and $creature->isFriendly() and $creature->isAlive() and ($this->distance($creature) <= 40)){
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops[] = ItemItem::get(ItemItem::NETHER_STAR, 0, 1);
		return $drops;
	}
	public function getWitherSkullNBT() : CompoundTag{
        return Entity::createBaseNBT($this->add(0, 2, 0), new Vector3(0, 0, 0), $this->yaw, $this->pitch);
  }

	public function checkDistance($target){
		if(sqrt($this->distanceSquared($target)) <= 20){
			return true;
		}else{
			return false;
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		if($this->age < 165){
			$source->setCancelled();
		}
		parent::attack($damage, $source);
	}

	public function attackEntity(Entity $player){
		if($this->distance($player) < 3){
			if($this->attackTicks <= 0){
				$damage = 15;
				$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage);
				if($player->attack($damage, $ev) == true) $ev->useArmors();
				$player->addEffect(Effect::getEffect(20)->setDuration(120)->setAmplifier(1)->setVisible(true));
				$this->attackTicks = 10;
			}
		}elseif($this->distance($player) >= 3 and $this->distance($player) < 25 and $this->attackTicks <= 0){
			$nbt = Entity::createBaseNBT(
				$this->add(0, $this->getEyeHeight(), 0),
				new Vector3(-sin($this->yaw / 180 * M_PI) * cos($this->pitch / 180 * M_PI),
				-sin($this->pitch / 180 * M_PI),
				cos($this->yaw / 180 * M_PI) * cos($this->pitch / 180 * M_PI)),
				$this->yaw,
				$this->pitch
			);
			if(mt_rand(0, 100) > 10){
				$type = "BlueWitherSkull";
			}else{
				$type = "BlackWitherSkull";
			}
			$skull = Entity::createEntity($type, $this->getLevel(), $nbt, $this, 1.5 == 2 ? true : false);
			$skull->spawnToAll();
			$this->attackTicks = 20;
		}
	}
	public function onUpdate($currentTick){
		if($this->isClosed() or !$this->isAlive()){
			return parent::onUpdate($currentTick);
		}
		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);
		if($this->attackTicks > 0){
			$this->attackTicks--;
		}
		$this->age++;
		if($this->age < 160){
			$this->motionX = 0;
			$this->motionY = 0;
			$this->motionZ = 0;
			$this->setDataProperty(self::DATA_WITHER_INVULNERABLE_TICKS, self::DATA_TYPE_INT, $this->age);
			return;
		}elseif(!$this->hadExplode){
			$this->setImmobile(false);
			$this->hadExplode = true;
			$explosion = new Explosion($this, 2.5);
			$explosion->explodeC();
			$explosion->explodeB();
			$explosion->explodeA();
			$this->getLevel()->addParticle(new HugeExplodeSeedParticle($this));
		}
		$this->setDataProperty(self::DATA_WITHER_INVULNERABLE_TICKS, self::DATA_TYPE_INT, 0);
		$this->timings->stopTiming();
		return $hasUpdate;
	}
}
