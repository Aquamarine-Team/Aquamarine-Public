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
use pocketmine\entity\Explosive;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\level\Explosion;
use pocketmine\level\sound\TNTPrimeSound;
use pocketmine\level\sound\ExplodeSound;
use pocketmine\math\Math;
use pocketmine\Player;
use pocketmine\event\entity\CreeperPowerEvent;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\Server;

class Creeper extends WalkingMonster implements Explosive{

	const NETWORK_ID = 33;

	public $width = 0.72;
	public $height = 1;

	private $boomTick = 0;
	private $boomTimer = 0;
	private $boom = false;
	protected $speed = 0.9;

	public function getSpeed(){
		return $this->speed;
	}

	public function initEntity(){
		parent::initEntity();
	}

	public function saveNBT(){
		parent::saveNBT();
	}

	public function explode(){}

	public function getName(){
		return "Creeper";
	}

	public function setPowered(bool $powered, Lightning $lightning = null){
		if($lightning != null){
			$powered = true;
			$cause = CreeperPowerEvent::CAUSE_LIGHTNING;
		}else{
			$cause = $powered ? CreeperPowerEvent::CAUSE_SET_ON : CreeperPowerEvent::CAUSE_SET_OFF;
		}

		$this->getLevel()->getServer()->getPluginManager()->callEvent($ev = new CreeperPowerEvent($this, $lightning, $cause));

		if(!$ev->isCancelled()){
			$this->namedtag->powered = new ByteTag("powered", $powered ? 1 : 0);
			$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_POWERED, $powered);
		}
	}

	public function canBeIgnited(){
		return true;
	}

	public function ignite(Player $player){
		$item = $player->getInventory()->getItemInHand();
		if($item->getDamage() >= 65){
			$player->getInventory()->setItemInHand(ItemItem::get(0, 0, 0));
		}else{
			$item->setDamage($item->getDamage() + 1);
			$player->getInventory()->setItemInHand($item);
		}
		$this->boom = true;
	}

	/**
	 * @return bool
	 */
	public function isPowered() : bool{
		return (bool) $this->namedtag["powered"];
	}

	public function onUpdate($currentTick){

        $tickDiff = $currentTick - $this->lastUpdate;

        if($this->baseTarget !== null){
            $x = $this->baseTarget->x - $this->x;
            $y = $this->baseTarget->y - $this->y;
            $z = $this->baseTarget->z - $this->z;

            $diff = abs($x) + abs($z);

						if($this->boom){
							if($this->boomTimer > 0){
								$this->boomTimer--;
							}else{
								$size = 3;
								if($this->isPowered()){
									$size = 5;
								}
								$this->boom = false;
								$ev = new ExplosionPrimeEvent($this, $size, true);
								$this->getLevel()->getServer()->getPluginManager()->callEvent($ev);
								$this->close();
								if(!$ev->isCancelled()){
									$e = new Explosion($this, $size, $this, true);
									if($ev->isBlockBreaking()){
										$e->explodeA();
									}
									$e->explodeB();
									$sound = new ExplodeSound($this);
									$this->getLevel()->addSound($sound);
								}
							}
						}
						if($this->baseTarget instanceof Creature){
							$this->speed = 1.5;
						}else{
							$this->speed = 0.9;
						}

            if($this->baseTarget instanceof Creature && $this->baseTarget->distance($this) <= 3){
							if(!$this->boom){
								if($this->boomTick < 10){
									$this->boomTick++;
								}else{
									$sound = new TNTPrimeSound($this);
									$this->getLevel()->addSound($sound);
									$this->boom = true;
									$this->boomTimer = 40;
								}
							}
            } else {
                $this->boom = false;
            }
            if($diff > 0){
                $this->yaw = rad2deg(-atan2($x / $diff, $z / $diff));
            }
            $this->pitch = $y == 0 ? 0 : rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
        }

        return parent::onUpdate($currentTick);
    }

	public function attackEntity(Entity $player){

	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				$count = mt_rand(1, 3 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::GUNPOWDER, 0, $count);
				}
			}
		}

		return $drops;
	}

}
