<?php

namespace Sensiolabs\GotenbergBundle\Builder\Behaviors\Chromium;

use Sensiolabs\GotenbergBundle\Builder\Attributes\NormalizeGotenbergPayload;
use Sensiolabs\GotenbergBundle\Builder\Attributes\WithConfigurationNode;
use Sensiolabs\GotenbergBundle\Builder\BodyBag;
use Sensiolabs\GotenbergBundle\Builder\Util\NormalizerFactory;
use Sensiolabs\GotenbergBundle\Enumeration\Part;
use Sensiolabs\GotenbergBundle\Exception\PartRenderingException;
use Sensiolabs\GotenbergBundle\NodeBuilder\ArrayNodeBuilder;
use Sensiolabs\GotenbergBundle\NodeBuilder\ScalarNodeBuilder;

/**
 * @package Behavior\\Content
 */
trait PageMarginalTrait
{
    abstract protected function getBodyBag(): BodyBag;

    /** @param array<string, mixed> $context */
    abstract protected function withRenderedPart(Part $part, string $template, array $context = []): static;

    abstract protected function withRawPart(Part $part, string $html): static;

    /** @throws PartRenderingException */
    abstract protected function withFilePart(Part $part, string $path): static;

    /**
     * @param string               $template #Template
     * @param array<string, mixed> $context
     *
     * @throws PartRenderingException if the template could not be rendered
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @example header('header.html.twig', ['my_var' => 'value'])
     */
    #[WithConfigurationNode(new ArrayNodeBuilder('header', children: [
        new ScalarNodeBuilder('template', required: true, restrictTo: 'string'),
        new ArrayNodeBuilder('context', normalizeKeys: false, prototype: 'variable'),
    ]))]
    public function header(string $template, array $context = []): static
    {
        return $this->withRenderedPart(Part::Header, $template, $context);
    }

    /**
     * The raw html string to use as the page header.
     *
     * Warning: Assets (css, images, etc...) cannot be parsed and loaded dynamically.
     * Assets can still be loaded using https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#assets.
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @example headerRaw('<html><body><h1>The header</h1></body></html>')
     */
    public function headerRaw(string $html): static
    {
        return $this->withRawPart(Part::Header, $html);
    }

    /**
     * HTML file containing the header.
     *
     * As assets files, by default the HTML files are fetch in the assets folder of your application.
     * If your HTML files are in another folder, you can override the default value of assets_directory in your
     * configuration file config/sensiolabs_gotenberg.yml.
     *
     * Warning: Assets (css, images, etc...) cannot be parsed and loaded dynamically.
     * Assets can still be loaded using https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#assets.
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @throws PartRenderingException if the file could not be loaded
     *
     * @example headerFile('../templates/html/header.html')
     */
    public function headerFile(string $path): static
    {
        return $this->withFilePart(Part::Header, $path);
    }

    /**
     * @param string               $template #Template
     * @param array<string, mixed> $context
     *
     * @throws PartRenderingException if the template could not be rendered
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @example footer('footer.html.twig', ['my_var' => 'value'])
     */
    #[WithConfigurationNode(new ArrayNodeBuilder('footer', children: [
        new ScalarNodeBuilder('template', required: true, restrictTo: 'string'),
        new ArrayNodeBuilder('context', normalizeKeys: false, prototype: 'variable'),
    ]))]
    public function footer(string $template, array $context = []): static
    {
        return $this->withRenderedPart(Part::Footer, $template, $context);
    }

    /**
     * The raw html string to use as the page footer.
     *
     * Warning: Assets (css, images, etc...) cannot be parsed and loaded dynamically.
     * Assets can still be loaded using https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#assets.
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @example footerRaw('<html><body><h6>The footer</h6></body></html>')
     */
    public function footerRaw(string $html): static
    {
        return $this->withRawPart(Part::Footer, $html);
    }

    /**
     * HTML file containing the footer.
     *
     * As assets files, by default the HTML files are fetch in the assets folder of your application.
     * If your HTML files are in another folder, you can override the default value of assets_directory in your
     * configuration file config/sensiolabs_gotenberg.yml.
     *
     * Warning: Assets (css, images, etc...) cannot be parsed and loaded dynamically.
     * Assets can still be loaded using https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#assets.
     *
     * @see https://gotenberg.dev/docs/convert-with-chromium/convert-html-to-pdf#header--footer
     *
     * @throws PartRenderingException if the file could not be loaded
     *
     * @example footerFile('../templates/html/footer.html')
     */
    public function footerFile(string $path): static
    {
        return $this->withFilePart(Part::Footer, $path);
    }

    #[NormalizeGotenbergPayload]
    private function normalizePageMarginal(): \Generator
    {
        yield 'header.html' => NormalizerFactory::content();
        yield 'footer.html' => NormalizerFactory::content();
    }
}
