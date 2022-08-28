<?php

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;
use pocketmine\entity\Rideable;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\Player;
use pocketmine\entity\Creature;

class Mule extends WalkingAnimal implements Rideable{

    const NETWORK_ID = 25;

    public $width = 0.75;
    public $height = 1.562;
    public $length = 1.2;

    public function getName() : string{
        return "Mule";
    }

    public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(15);
	}

    public function getSpeed(){
        return 1.1;
    }

    public function targetOption(Creature $creature, $distance){
        if($creature instanceof Player){
            return $creature->spawned && $creature->isAlive() && !$creature->closed && $creature->getInventory()->getItemInHand()->getId() == ItemItem::WHEAT && $distance <= 49;
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
						$drops[] = ItemItem::get(ItemItem::LEATHER, 0, $count);
					}
				}
			}
			return $drops;
		}

    public function getKillExperience(){
        return mt_rand(1, 3);
    }

    public function getRidePosition(){
        return null;
    }

}
