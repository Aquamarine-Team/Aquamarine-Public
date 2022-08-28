<?php

namespace pocketmine\entity\monster\walking;

use pocketmine\entity\monster\WalkingMonster;
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\Math;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Spider extends WalkingMonster{

	const NETWORK_ID = 35;

	public $width = 1.3;
	public $height = 1.12;

	public function getSpeed(){
		return 1.13;
	}

	public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(16);
		$this->setDamage([0, 2, 2, 3]);
	}

	public function getName(){
		return "Spider";
	}


	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) <= 1.5){
			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			$player->attack($ev->getFinalDamage(), $ev);
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			$this->setFriendly(false);
		}
		parent::attack($damage, $source);
		if($source->isCancelled()) return false;
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
					$drops[] = ItemItem::get(ItemItem::SPIDER_EYE, 0, $count);
				}
				$count = mt_rand(0, 1 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::FERMENTED_SPIDER_EYE, 0, $count);
				}
				$count = mt_rand(1, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::STRING, 0, $count);
				}
			}
		}

		return $drops;
	}

}
