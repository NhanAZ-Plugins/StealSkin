<?php

declare(strict_types=1);

namespace NhanAZ\StealSkin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName() !== "stealskin") {
			return false;
		}
		if (!($sender instanceof Player)) {
			$sender->sendMessage(TextFormat::RED . "This command must be executed as a player");
			return true;
		}
		if (count($args) < 1) {
			return false;
		}
		$target = $sender->getServer()->getPlayerExact($args[0]);
		if ($target === null) {
			$sender->sendMessage(KnownTranslationFactory::commands_generic_player_notFound()->prefix(TextFormat::RED));
			return true;
		}
		if ($target === $sender) {
			$sender->sendMessage(TextFormat::RED . "You can't steal skin from yourself!");
			return true;
		}
		$sender->setSkin($target->getSkin());
		$sender->sendSkin();
		$sender->sendMessage(TextFormat::GREEN . "Successfully stolen " . $target->getName() . "'s skin");
		return true;
	}
}
