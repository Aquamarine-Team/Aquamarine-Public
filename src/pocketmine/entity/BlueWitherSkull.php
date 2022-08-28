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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\entity\Projectile;
use pocketmine\entity\Entity;
use pocketmine\event\entity\ExplosionPrimeEvent;

class BlueWitherSkull extends Projectile
{
    const NETWORK_ID = 91;

    public $width = 0.25;
    public $length = 0.25;
    public $height = 0.25;
    public $data = [];

    protected $gravity = 0;
    protected $drag = 0;
    private $near = null;

    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, $data = [])
    {
        $this->data = $data;
        parent::__construct($level, $nbt, $shootingEntity);
    }

    public function getName() : string{
      return "BlueWitherSkull";
    }

    public function onUpdate($currentTick)
    {
        if ($this->closed) {
            return false;
        }

        foreach ($this->server->getOnlinePlayers() as $player) {
          if($this->distance($player) < 50){
            $this->near = $player;
          }
        }
        if($this->near == null){
          $this->kill();
          return true;
        }

        $hasUpdate = parent::onUpdate($currentTick);

        if ($this->age > 1200 or $this->isCollided) {
          $this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 2, true));
      		if(!$ev->isCancelled()){
      			$explosion = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), $ev->getForce(), $this, $ev->dropItem());
      			$explosion->explodeA();
      			$explosion->explodeB();
      		}
            $this->kill();
            $hasUpdate = true;
        }

        return $hasUpdate;
    }

    public function spawnTo(Player $player)
    {
        $pk = new AddEntityPacket();
        $pk->type = BlueWitherSkull::NETWORK_ID;
        $pk->eid = $this->getId();
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = $this->motionX;
        $pk->speedY = $this->motionY;
        $pk->speedZ = $this->motionZ;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }
}
