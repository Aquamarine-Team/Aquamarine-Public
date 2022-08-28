<?php

namespace pocketmine\entity\monster\walking;

use pocketmine\entity\monster\WalkingMonster;
use pocketmine\entity\Effect;
use pocketmine\entity\Ageable;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Husk extends WalkingMonster implements Ageable{

    const NETWORK_ID = 47;

    public $width = 1.031;
    public $length = 0.891;
    public $height = 2;

    public function getName(){
        return "Husk";
    }

    public function initEntity(){
        parent::initEntity();

        if($this->getDataFlag(self::DATA_FLAG_BABY, 0) === null){
            $this->setDataFlag(self::DATA_FLAG_BABY, self::DATA_TYPE_BYTE, 0);
        }
        $this->setMaxHealth(20);
        $this->setDamage([0, 3, 4, 6]);
    }

    public function getSpeed(){
        return 1.1;
    }

    public function isBaby(){
        return $this->getDataFlag(self::DATA_FLAG_BABY, 0);
    }

    public function setHealth($amount){
        parent::setHealth($amount);

        if ($this->isAlive()) {
            if (15 < $this->getHealth()) {
                $this->setDamage([0, 2, 3, 4]);
            } else if (10 < $this->getHealth()) {
                $this->setDamage([0, 3, 4, 6]);
            } else if (5 < $this->getHealth()) {
                $this->setDamage([0, 3, 5, 7]);
            } else {
                $this->setDamage([0, 4, 6, 9]);
            }
        }
    }

    /**
     * @param Entity $player
     */
		 public function attackEntity(Entity $player){
	 		if($this->distanceSquared($player) <= 1.5){
	 			$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
	 			$player->attack($ev->getFinalDamage(), $ev);
				$this->addEffect(Effect::getEffect(Effect::HUNGER)->setDuration(7 * 20 * $this->server->getDifficulty()));
	 		}
	 	}

		public function getDrops(){
			$cause = $this->lastDamageCause;
			$drops = [];
			if($cause instanceof EntityDamageByEntityEvent){
				$damager = $cause->getDamager();
				if($damager instanceof Player){
					$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
					if(mt_rand(0, 199) < (5 + 2 * $lootingL)){
						switch(mt_rand(0, 3)){
							case 0:
								$drops[] = ItemItem::get(ItemItem::IRON_INGOT, 0, 1);
								break;
							case 1:
								$drops[] = ItemItem::get(ItemItem::CARROT, 0, 1);
								break;
							case 2:
								$drops[] = ItemItem::get(ItemItem::POTATO, 0, 1);
								break;
						}
					}
					$count = mt_rand(1, 2 + $lootingL);
					if($count > 0){
						$drops[] = ItemItem::get(ItemItem::ROTTEN_FLESH, 0, $count);
					}
				}
			}
			return $drops;
		}

    public function getKillExperience(){
        return 5;
    }

}
