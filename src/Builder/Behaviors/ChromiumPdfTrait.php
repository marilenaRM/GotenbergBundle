<?php

namespace Sensiolabs\GotenbergBundle\Builder\Behaviors;

trait ChromiumPdfTrait
{
    use Chromium\AssetTrait;
    use Chromium\ContentTrait, Chromium\PageMarginalTrait {
        Chromium\PageMarginalTrait::header insteadof Chromium\ContentTrait;
        Chromium\PageMarginalTrait::headerRaw insteadof Chromium\ContentTrait;
        Chromium\PageMarginalTrait::headerFile insteadof Chromium\ContentTrait;
        Chromium\PageMarginalTrait::footer insteadof Chromium\ContentTrait;
        Chromium\PageMarginalTrait::footerRaw insteadof Chromium\ContentTrait;
        Chromium\PageMarginalTrait::footerFile insteadof Chromium\ContentTrait;
    }
    use Chromium\CookieTrait;
    use Chromium\CustomHttpHeadersTrait;
    use Chromium\EmulatedMediaFeaturesTrait;
    use Chromium\EmulatedMediaTypeTrait;
    use Chromium\FailOnTrait;
    use Chromium\PdfPagePropertiesTrait;
    use Chromium\PerformanceModeTrait;
    use Chromium\WaitBeforeRenderingTrait;
    use DownloadFromTrait;
    use EncryptTrait;
    use FlattenTrait;
    use MetadataTrait;
    use PdfFormatTrait;
    use SplitTrait;
    use WebhookTrait;
}
