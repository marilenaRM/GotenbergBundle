<?php

namespace Sensiolabs\GotenbergBundle\Builder\Behaviors\Chromium;

trait HideHeaderFooterTrait
{
    /** @param array<string, mixed> $context */
    private function header(string $template, array $context = []): static
    {
        return $this;
    }

    private function headerRaw(string $html): static
    {
        return $this;
    }

    private function headerFile(string $path): static
    {
        return $this;
    }

    /** @param array<string, mixed> $context */
    private function footer(string $template, array $context = []): static
    {
        return $this;
    }

    private function footerRaw(string $html): static
    {
        return $this;
    }

    private function footerFile(string $path): static
    {
        return $this;
    }
}
