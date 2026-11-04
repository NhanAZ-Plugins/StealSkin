# StealSkin

Copy an online player's skin by exact name on Axolotl-PM

[![CI](https://github.com/NhanAZ-Plugins/StealSkin/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/StealSkin/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/StealSkin)](https://github.com/NhanAZ-Plugins/StealSkin/releases/latest)
[![License](https://img.shields.io/github/license/NhanAZ-Plugins/StealSkin)](https://github.com/NhanAZ-Plugins/StealSkin/blob/master/LICENSE)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)

## Overview

This plugin targets the PocketMine-MP and Axolotl-PM API 5 ecosystem and is maintained under [NhanAZ-Plugins](https://github.com/NhanAZ-Plugins).

## Requirements and compatibility

- Axolotl-PM API 5.0.0, as declared in [`plugin.yml`](plugin.yml). PHPStan at maximum level and command tests pass against the 5.0.0 and 5.49.1 source revisions. Intermediate 5.x releases have not been tested.
- PHP 8.1 or newer, as required by the Axolotl-PM 5.x baseline.
- No external plugin or runtime library dependency.

The exact release PHAR has been booted on Axolotl-PM 5.49.1. A connected Bedrock client has not been used to inspect the visual skin update.

## Installation and command

Download the PHAR from [GitHub Releases](https://github.com/NhanAZ-Plugins/StealSkin/releases), place it in the server's `plugins` directory, and restart Axolotl-PM. The plugin has no configuration file.

| Command | Effect | Permission |
|---|---|---|
| `/stealskin <player>` | Copy the skin of the named online player to yourself | `stealskin.command`, operator by default |

The target name must match an online player's full name, ignoring letter case. Partial names are rejected so the plugin cannot select an unintended player with a similar name. The command cannot be run from the server console and cannot target yourself. StealSkin does not write a separate skin file.

## Build and verification

The [build workflow](.github/workflows/build.yml) tests command handling, runs PHPStan at maximum level and uses [DevTools](https://github.com/NhanAZ/DevTools) to build and inspect a downloadable PHAR. DevTools and Axolotl-PM source are pinned to reviewed revisions in CI.

Run `php tests/skin-command.php /path/to/Axolotl-PM` to check the command with local Axolotl-PM source and installed dependencies. For a local PHAR build, follow the [DevTools CLI guide](https://github.com/NhanAZ/DevTools/blob/main/docs/cli.md). The command fixture confirms skin assignment and one update call, not visual rendering on a Bedrock client.

## Support and license

Report bugs in [GitHub Issues](https://github.com/NhanAZ-Plugins/StealSkin/issues) or contact [NhanAZ Discord](https://discord.gg/j2X83ujT6c). StealSkin retains its existing [GPL-3.0 license](LICENSE).
