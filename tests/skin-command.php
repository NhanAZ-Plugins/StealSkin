<?php

declare(strict_types=1);

use NhanAZ\StealSkin\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Skin;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;

$serverSource = $argv[1] ?? null;
if (!is_string($serverSource) || !is_file($serverSource . '/vendor/autoload.php')) {
    fwrite(STDERR, "Usage: php tests/skin-command.php /path/to/Axolotl-PM [plugin PHAR]\n");
    exit(2);
}
require $serverSource . '/vendor/autoload.php';
$phar = $argv[2] ?? null;
if (is_string($phar)) {
    require 'phar://' . realpath($phar) . '/src/NhanAZ/StealSkin/Main.php';
} else {
    require dirname(__DIR__) . '/src/NhanAZ/StealSkin/Main.php';
}

final class TestServer extends Server {
    /** @var list<Player> */
    public array $players = [];

    public function __construct() {}

    public function getOnlinePlayers(): array {
        return $this->players;
    }
}

final class TestPlayer extends Player {
    /** @var list<Translatable|string> */
    public array $messages = [];
    public int $skinSends = 0;

    public function __construct(private TestServer $testServer, private string $testName, private Skin $testSkin) {}

    public function __destruct() {}

    public function getServer(): Server {
        return $this->testServer;
    }

    public function getName(): string {
        return $this->testName;
    }

    public function getSkin(): Skin {
        return $this->testSkin;
    }

    public function setSkin(Skin $skin): void {
        $this->testSkin = $skin;
    }

    public function sendSkin(?array $targets = null): void {
        ++$this->skinSends;
    }

    public function sendMessage(Translatable|string $message): void {
        $this->messages[] = $message;
    }
}

final class TestConsole extends ConsoleCommandSender {
    /** @var list<Translatable|string> */
    public array $messages = [];

    public function __construct(private TestServer $testServer) {}

    public function getServer(): Server {
        return $this->testServer;
    }

    public function sendMessage(Translatable|string $message): void {
        $this->messages[] = $message;
    }
}

final class TestCommand extends Command {
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        return false;
    }
}

function check(bool $condition, string $case): void {
    if (!$condition) {
        throw new RuntimeException("Failed: $case");
    }
}

$server = new TestServer();
$senderSkin = new Skin('sender', str_repeat("\x00", 16384));
$targetSkin = new Skin('target', str_repeat("\x01", 16384));
$sender = new TestPlayer($server, 'Alice', $senderSkin);
$target = new TestPlayer($server, 'Alex', $targetSkin);
$server->players = [$sender, $target];
$plugin = (new ReflectionClass(Main::class))->newInstanceWithoutConstructor();
$command = new TestCommand('stealskin');

check(!$plugin->onCommand($sender, new TestCommand('other'), 'other', ['Alex']), 'unknown command');
check(!$plugin->onCommand($sender, $command, 'stealskin', []), 'missing target usage');
check($sender->skinSends === 0, 'missing target leaves skin untouched');

$console = new TestConsole($server);
check($plugin->onCommand($console, $command, 'stealskin', ['Alex']), 'console rejection');
check(count($console->messages) === 1, 'console receives one explanation');

check($plugin->onCommand($sender, $command, 'stealskin', ['Al']), 'partial name rejected');
check($sender->getSkin() === $senderSkin && $sender->skinSends === 0, 'partial name leaves skin untouched');
check($plugin->onCommand($sender, $command, 'stealskin', ['Missing']), 'offline target rejected');
check($sender->getSkin() === $senderSkin, 'offline target leaves skin untouched');
check($plugin->onCommand($sender, $command, 'stealskin', ['Alice']), 'self target rejected');
check($sender->getSkin() === $senderSkin, 'self target leaves skin untouched');

check($plugin->onCommand($sender, $command, 'stealskin', ['aLeX']), 'exact name accepted case insensitively');
check($sender->getSkin() === $targetSkin, 'target skin assigned');
check($sender->skinSends === 1, 'skin broadcast once');
check(is_string(end($sender->messages)) && str_contains(end($sender->messages), 'Alex'), 'success message names target');

echo "PASS: 7 skin command cases\n";
