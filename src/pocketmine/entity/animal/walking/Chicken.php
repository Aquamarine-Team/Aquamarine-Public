<?php

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Creature;
use pocketmine\item\enchantment\Enchantment;

class Chicken extends WalkingAnimal{

	const NETWORK_ID = 10;

	public $width = 0.4;
	public $height = 0.75;

	public function getName() : string{
		return "Chicken";
	}

	public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(4);
	}

	public function isBaby(){
		return $this->getDataFlag(self::DATA_AGEABLE_FLAGS, self::DATA_FLAG_BABY);
	}

	public function targetOption(Creature $creature, $distance){
		if($creature instanceof Player){
			return $creature->isAlive() && !$creature->closed && $creature->getInventory()->getItemInHand()->getId() == ItemItem::SEEDS && $distance <= 39;
		}
		return false;
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
					$drops[] = ItemItem::get(ItemItem::FEATHER, 0, $count);
				}
				$count = mt_rand(0, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::RAW_CHICKEN, 0, $count);
				}
			}
		}
		return $drops;
	}
}
