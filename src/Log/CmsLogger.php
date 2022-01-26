<?php

namespace Efrogg\ContentRenderer\Log;

// because psr/log changed the signature, and we cannot be sure the package is locked to 1.1 or 2.0 (or 3.0)
$installedLogVersion = \Composer\InstalledVersions::getVersion('psr/log');
if(version_compare($installedLogVersion,'2','<')) {
    include_once __DIR__.'/../../__includes/CmsLoggerV1.php';
} else {
    include_once __DIR__.'/../../__includes/CmsLoggerV2.php';
}


