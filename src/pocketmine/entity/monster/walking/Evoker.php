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

use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\monster\WalkingMonster;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Evoker extends WalkingMonster {
	const NETWORK_ID = 104;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 2;

	public $dropExp = [5, 5];

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Evoker";
	}

	public function initEntity(){
		$this->setMaxHealth(24);
		//$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_EVOKER_SPELL, true);
		parent::initEntity();
	}

	public function checkDistance($target){
		if(sqrt($this->distanceSquared($target)) <= 10){
			return true;
		}else{
			return false;
		}
	}

	public function attackEntity(Entity $player){
		if($this->distanceSquared($player) <= 10){
			//$ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
			//$player->attack($ev->getFinalDamage(), $ev);
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);
	}
	/**
	 * @return array
	 */
	public function getDrops(){
		$drops[] = ItemItem::get(ItemItem::EMERALD, 0, mt_rand(0, 1));
		$drops[] = ItemItem::get(ItemItem::TOTEM, 0, 1);

		return $drops;
	}
}
