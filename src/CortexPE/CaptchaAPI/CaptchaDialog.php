<?php
/**
 * Created by PhpStorm.
 * User: CortexPE
 * Date: 1/4/2019
 * Time: 12:20 AM
 */

namespace CortexPE\CaptchaAPI;


use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CaptchaDialog implements Form {
	public const CAPTCHA_TYPE_NUMBER = 0;
	public const CAPTCHA_TYPE_ALPHANUMERIC = 1;
	public const CAPTCHA_TYPE_ALPNUM_CHARS = 2;
	public const CAPTCHA_TYPE_CHARS = 3;

	public const CAPTCHA_LENGTH_EASY = 3;
	public const CAPTCHA_LENGTH_MODERATE = 6;
	public const CAPTCHA_LENGTH_MEDIUM = 9;
	public const CAPTCHA_LENGTH_HARD = 12;

	private const ALPHABET = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	private const NUMBERS = "0123456789";
	private const ALPHANUMERIC = self::ALPHABET . self::NUMBERS;
	private const SPECIAL_CHARACTERS = "!@#$%^&*()_+-=[]{}|/><";
	private const ALPHANUMERIC_WITH_CHARACTERS = self::ALPHANUMERIC . self::SPECIAL_CHARACTERS;
	private const TEXT_COLORS = "01234569abcdef"; // remove colors 7 & 8.... pretty hard to see on vanilla UI

	/** @var array */
	private $data;
	/** @var callable */
	private $successCallable = null;
	/** @var callable */
	private $failureCallable = null;
	/** @var bool */
	private $persistent = true; // lol more annoyance by default >:3
	/** @var string */
	private $text = "";

	public function __construct(int $captchaType = self::CAPTCHA_TYPE_ALPHANUMERIC, int $captchaLength = self::CAPTCHA_LENGTH_MODERATE){
		$this->data["type"] = "custom_form";
		$this->data["title"] = "Are you human?";
		$this->text = "";
		for($i = 0; $i < $captchaLength; $i++){
			$this->text .= TextFormat::ESCAPE . self::getRandomCharacter(self::TEXT_COLORS);
			$r = mt_rand(0, 3);
			switch($r){
				case 0:
					$this->text .= TextFormat::ITALIC;
					break;
				case 1:
					$this->text .= TextFormat::BOLD;
					break;
				case 2:
					$this->text .= TextFormat::STRIKETHROUGH; // if this even worked xD
					break;
				case 3:
					$this->text .= TextFormat::UNDERLINE; // same as above (as of mcpe 1.8)
					break;
			}
			switch($captchaType){
				case self::CAPTCHA_TYPE_NUMBER:
					$this->text .= self::getRandomCharacter(self::NUMBERS);
					break;
				case self::CAPTCHA_TYPE_ALPHANUMERIC:
					$this->text .= self::getRandomCharacter(self::ALPHANUMERIC);
					break;
				case self::CAPTCHA_TYPE_ALPNUM_CHARS:
					$this->text .= self::getRandomCharacter(self::ALPHANUMERIC_WITH_CHARACTERS);
					break;
				case self::CAPTCHA_TYPE_CHARS:
					$this->text .= self::getRandomCharacter(self::SPECIAL_CHARACTERS);
					break;
				default:
					throw new \InvalidArgumentException("Captcha type is invalid");
			}
			$this->text .= TextFormat::RESET;
		}
		$this->data["content"] = [
			[
				"type" => "label",
				"text" => "Please do this captcha test to continue.\n\n" . $this->text . "\n",
			],
			[
				"type"        => "input",
				"text"        => "Type the text above:",
				"placeholder" => "I'm not a robot",
				"default"     => null,
			],
		];
	}

	private static function getRandomCharacter(string $string): string{
		if(($len = strlen($string)) < 1){
			throw new \InvalidArgumentException("Provided string should be at least 1 character long");
		}

		return $string[mt_rand(0, $len - 1)];
	}

	public function isPersistent(): bool{
		return $this->persistent;
	}

	public function setPersistent(bool $persistent): void{
		$this->persistent = $persistent;
	}

	public function handleResponse(Player $player, $data): void{
		if($data === null || ($data[1] ?? "") != TextFormat::clean($this->text)){
			if($this->persistent){
				$player->sendForm($this);
			} else {
				$player->addTitle(TextFormat::RED . "\xe2\x9c\x96", TextFormat::YELLOW . "Human verification failed");
				if(($failureCallable = $this->getFailureCallable()) !== null){
					$failureCallable($player);
				}
			}

			return;
		}
		$player->addTitle(TextFormat::GREEN . "\xe2\x9c\x94", "Human verification successful");
		if(($successCallable = $this->getSuccessCallable()) !== null){
			$successCallable($player);
		}
	}

	public function getFailureCallable(): ?callable{
		return $this->failureCallable;
	}

	public function setFailureCallable(callable $failureCallable): void{
		$this->failureCallable = $failureCallable;
	}

	public function getSuccessCallable(): ?callable{
		return $this->successCallable;
	}

	public function setSuccessCallable(callable $successCallable): void{
		$this->successCallable = $successCallable;
	}

	public function jsonSerialize(): array{
		return $this->data;
	}
}