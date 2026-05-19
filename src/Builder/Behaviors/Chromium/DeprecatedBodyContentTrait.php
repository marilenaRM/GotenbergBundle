<?php

namespace Sensiolabs\GotenbergBundle\Builder\Behaviors\Chromium;

/**
 * @package Behavior\\Content
 */
trait DeprecatedBodyContentTrait
{
    /**
     * @deprecated since 1.2, will be removed in 2.0. The page body is provided by the URL.
     *
     * @param array<string, mixed> $context
     */
    public function content(string $template, array $context = []): static
    {
        @trigger_error(\sprintf('Since sensiolabs/gotenberg-bundle 1.2: "%s" is deprecated and will be removed in 2.0. The page body is provided by the URL.', __METHOD__), \E_USER_DEPRECATED);

        return $this;
    }

    /**
     * @deprecated since 1.2, will be removed in 2.0. The page body is provided by the URL.
     */
    public function contentRaw(string $html): static
    {
        @trigger_error(\sprintf('Since sensiolabs/gotenberg-bundle 1.2: "%s" is deprecated and will be removed in 2.0. The page body is provided by the URL.', __METHOD__), \E_USER_DEPRECATED);

        return $this;
    }

    /**
     * @deprecated since 1.2, will be removed in 2.0. The page body is provided by the URL.
     */
    public function contentFile(string $path): static
    {
        @trigger_error(\sprintf('Since sensiolabs/gotenberg-bundle 1.2: "%s" is deprecated and will be removed in 2.0. The page body is provided by the URL.', __METHOD__), \E_USER_DEPRECATED);

        return $this;
    }
}
