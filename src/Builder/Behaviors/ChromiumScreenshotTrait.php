<?php

namespace Sensiolabs\GotenbergBundle\Builder\Behaviors;

trait ChromiumScreenshotTrait
{
    use Chromium\AssetTrait;
    use Chromium\ContentTrait, Chromium\HideHeaderFooterTrait {
        Chromium\HideHeaderFooterTrait::header insteadof Chromium\ContentTrait;
        Chromium\HideHeaderFooterTrait::headerRaw insteadof Chromium\ContentTrait;
        Chromium\HideHeaderFooterTrait::headerFile insteadof Chromium\ContentTrait;
        Chromium\HideHeaderFooterTrait::footer insteadof Chromium\ContentTrait;
        Chromium\HideHeaderFooterTrait::footerRaw insteadof Chromium\ContentTrait;
        Chromium\HideHeaderFooterTrait::footerFile insteadof Chromium\ContentTrait;
    }
    use Chromium\CookieTrait;
    use Chromium\CustomHttpHeadersTrait;
    use Chromium\EmulatedMediaFeaturesTrait;
    use Chromium\EmulatedMediaTypeTrait;
    use Chromium\FailOnTrait;
    use Chromium\PerformanceModeTrait;
    use Chromium\ScreenshotPagePropertiesTrait;
    use Chromium\WaitBeforeRenderingTrait;
    use DownloadFromTrait;
    use WebhookTrait;
}
