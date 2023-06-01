<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Dto;

use App\Feature\AutoNumerator\Service\PrefixTemplateResolver;
use Planka\Bridge\Views\Dto\Card\CardDto;
use function Fp\Collection\map;

final class PrefixNumberContext
{
    private array $numberedCards;
    private array $unnumberedCards;
    private array $doubleNumberedCards;
    private array $unLabeledCards;

    public function __construct(private readonly string $prefix)
    {
        $this->numberedCards = [];
        $this->unnumberedCards = [];
        $this->doubleNumberedCards = [];
        $this->unLabeledCards = [];
    }

    public function addCard(CardDto $card): void
    {
        preg_match_all(
            PrefixTemplateResolver::getRegExpPrefix($this->prefix),
            $card->name,
            $matches,
            PREG_SET_ORDER
        );

        if (array_key_exists(0, $matches)) {
            $number = PrefixTemplateResolver::retrieveNumber($matches[0][0], $this->prefix);

            // map by doubled prefix to many cards
            if (array_key_exists($number, $this->numberedCards) && $this->numberedCards[$number]->id !== $card->id) {
                $this->doubleNumberedCards[$number] = [
                    $card,
                    $this->numberedCards[$number],
                ];
            }

            $this->numberedCards[$number] = $card;
        } else {
            $this->unnumberedCards[] = $card;
        }
    }

    public function addUnlabeledCard(CardDto $card): void
    {
        preg_match_all(
            PrefixTemplateResolver::getRegExpPrefix($this->prefix),
            $card->name,
            $matches,
            PREG_SET_ORDER
        );

        if (array_key_exists(0, $matches)) {
            $this->unLabeledCards[] = $card;
        }
    }

    /**
     * @return array<int, CardDto>
     */
    public function getNumberedCards(): array
    {
        return $this->numberedCards;
    }

    /**
     * @return array<int, CardDto>
     */
    public function getUnNumberedCards(): array
    {
        return $this->unnumberedCards;
    }

    /**
     * @return array<int, list<CardDto>
     */
    public function getDoubleNumberedCards(): array
    {
        return $this->doubleNumberedCards;
    }
    /**
     * @return array<int, list<CardDto>
     */
    public function getAllCards(): array
    {
        return map([...$this->numberedCards, ...$this->unnumberedCards], fn(CardDto $card) => $card->id);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return array
     */
    public function getUnlabeledCards(): array
    {
        return $this->unLabeledCards;
    }
}