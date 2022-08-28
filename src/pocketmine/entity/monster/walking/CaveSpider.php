<?php

namespace pocketmine\entity\monster\walking;

use pocketmine\entity\monster\WalkingMonster;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class CaveSpider extends WalkingMonster{

	const NETWORK_ID = 40;

	public $width = 0.9;
	public $height = 0.8;

	public function getSpeed(){
		return 1.3;
	}

	public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(12);
		$this->setDamage([0, 2, 3, 3]);
	}

	public function getName(){
		return "CaveSpider";
	}

	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) < 1.5){

			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			$player->attack($ev->getFinalDamage(), $ev);
		}
	}

	public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				$count = mt_rand(0, 1 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::SPIDER_EYE, 0, $count);
				}
				$count = mt_rand(0, 1 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::FERMENTED_SPIDER_EYE, 0, $count);
				}
				$count = mt_rand(0, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::STRING, 0, $count);
				}
			}
		}

		return $drops;
	}

}
